<?php

namespace App\Http\Controllers;

use App\Models\MoveEvent;
use App\Models\MovingQuote;
use App\Services\SchedulingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MoveEventController extends Controller
{
    public function __construct(private SchedulingService $scheduling)
    {
    }

    public function calendar()
    {
        $quotes = MovingQuote::orderByDesc('created_at')
            ->whereNotIn('status', ['closed', 'cancelled'])
            ->whereDoesntHave('moveEvents', fn ($q) => $q->active())
            ->get();

        return view('dashboard-calendar', [
            'quotes' => $quotes->map(fn ($q) => $this->quotePayload($q))->values(),
            'config' => [
                'maxConcurrent' => (int) config('scheduling.max_concurrent_moves'),
                'dayStart' => config('scheduling.day_start'),
                'dayEnd' => config('scheduling.day_end'),
            ],
        ]);
    }

    public function index(Request $request)
    {
        $data = $request->validate([
            'start' => 'required|date',
            'end' => 'required|date',
        ]);

        $events = MoveEvent::overlapping(Carbon::parse($data['start']), Carbon::parse($data['end']))
            ->orderBy('start_at')
            ->get();

        return response()->json($events->map(fn ($e) => $this->payload($e))->values());
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $quote = isset($data['moving_quote_id']) ? MovingQuote::find($data['moving_quote_id']) : null;
        $data = $this->fillFromQuote($data, $quote);

        if ($conflict = $this->capacityConflict($request, $data['status'] ?? 'scheduled', $data)) {
            return $conflict;
        }

        $event = MoveEvent::create($data);
        $this->scheduling->syncQuoteStatus($event->moving_quote_id);

        return response()->json(['event' => $this->payload($event->fresh())], 201);
    }

    public function update(Request $request, MoveEvent $moveEvent)
    {
        $data = $this->validated($request, $moveEvent);

        if (array_key_exists('moving_quote_id', $data) && $data['moving_quote_id']) {
            $data = $this->fillFromQuote($data, MovingQuote::find($data['moving_quote_id']));
        }

        $status = $data['status'] ?? $moveEvent->status;

        if ($conflict = $this->capacityConflict($request, $status, $data, $moveEvent)) {
            return $conflict;
        }

        $previousQuoteId = $moveEvent->moving_quote_id;
        $moveEvent->update($data);

        $this->scheduling->syncQuoteStatus($moveEvent->moving_quote_id);
        if ($previousQuoteId !== $moveEvent->moving_quote_id) {
            $this->scheduling->syncQuoteStatus($previousQuoteId);
        }

        return response()->json(['event' => $this->payload($moveEvent->fresh())]);
    }

    public function destroy(MoveEvent $moveEvent)
    {
        $quoteId = $moveEvent->moving_quote_id;
        $moveEvent->delete();
        $this->scheduling->syncQuoteStatus($quoteId);

        return response()->json(['message' => 'Event deleted.']);
    }

    private function validated(Request $request, ?MoveEvent $existing = null): array
    {
        $partial = $existing ? 'sometimes|' : '';

        $data = $request->validate([
            'moving_quote_id' => 'nullable|integer|exists:moving_quotes,id',
            'title' => 'nullable|string|max:255',
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:50',
            'origin_address' => 'nullable|string|max:255',
            'destination_address' => 'nullable|string|max:255',
            'start_at' => $partial.'required|date',
            'end_at' => $partial.'required|date',
            'crew_size' => 'nullable|integer|min:1|max:20',
            'status' => ['nullable', Rule::in(MoveEvent::STATUSES)],
            'notes' => 'nullable|string|max:5000',
            'force' => 'sometimes|boolean',
        ]);

        $start = Carbon::parse($data['start_at'] ?? $existing?->start_at);
        $end = Carbon::parse($data['end_at'] ?? $existing?->end_at);

        if ($end->lessThanOrEqualTo($start)) {
            abort(response()->json([
                'message' => 'The end time must be after the start time.',
                'errors' => ['end_at' => ['The end time must be after the start time.']],
            ], 422));
        }

        if ($existing) {
            // Drop nulls the form omitted so a partial update (e.g. drag & drop)
            // never wipes the stored fields; keep explicit nulls for the quote link.
            $data = array_filter($data, fn ($v, $k) => $v !== null || $k === 'moving_quote_id', ARRAY_FILTER_USE_BOTH);
        }

        return $data;
    }

    /** Copies the lead's details into the event for any field left blank. */
    private function fillFromQuote(array $data, ?MovingQuote $quote): array
    {
        if (! $quote) {
            if (empty($data['title'])) {
                $data['title'] = $data['customer_name'] ?? 'Move';
            }

            return $data;
        }

        $data['customer_name'] = $data['customer_name'] ?? $quote->name;
        $data['customer_phone'] = $data['customer_phone'] ?? $quote->phone;
        $data['origin_address'] = $data['origin_address'] ?? ($quote->origin_address ?: $quote->origin_zip);
        $data['destination_address'] = $data['destination_address'] ?? ($quote->destination_address ?: $quote->destination_zip);
        $data['title'] = ! empty($data['title']) ? $data['title'] : ($quote->name ?: $quote->phone ?: 'Move');

        return $data;
    }

    private function capacityConflict(Request $request, string $status, array $data, ?MoveEvent $existing = null)
    {
        if (! in_array($status, MoveEvent::ACTIVE_STATUSES, true) || $request->boolean('force')) {
            return null;
        }

        $start = Carbon::parse($data['start_at'] ?? $existing->start_at);
        $end = Carbon::parse($data['end_at'] ?? $existing->end_at);

        if (! $this->scheduling->exceedsCapacity($start, $end, $existing?->id)) {
            return null;
        }

        $max = config('scheduling.max_concurrent_moves');

        return response()->json([
            'message' => "That time slot is already at capacity ({$max} simultaneous moves).",
            'conflict' => true,
            'conflicts' => $this->scheduling->overlapping($start, $end, $existing?->id)
                ->map(fn ($e) => $this->payload($e))->values(),
        ], 409);
    }

    private function quotePayload(MovingQuote $q): array
    {
        return [
            'id' => $q->id,
            'name' => $q->name,
            'phone' => $q->phone,
            'status' => $q->status,
            'origin' => $q->origin_address ?: $q->origin_zip,
            'destination' => $q->destination_address ?: $q->destination_zip,
            'preferred_date' => $q->preferred_date?->toDateString(),
            'schedule' => $q->schedule,
            'hours' => $this->scheduling->suggestedHours($q),
        ];
    }

    private function payload(MoveEvent $e): array
    {
        return [
            'id' => $e->id,
            'moving_quote_id' => $e->moving_quote_id,
            'title' => $e->title,
            'customer_name' => $e->customer_name,
            'customer_phone' => $e->customer_phone,
            'origin_address' => $e->origin_address,
            'destination_address' => $e->destination_address,
            'start' => $e->start_at->format('Y-m-d\TH:i:s'),
            'end' => $e->end_at->format('Y-m-d\TH:i:s'),
            'crew_size' => $e->crew_size,
            'status' => $e->status,
            'notes' => $e->notes,
        ];
    }
}
