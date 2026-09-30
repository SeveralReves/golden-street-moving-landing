<?php

namespace App\Http\Controllers;

use App\Models\MovingQuote;
use Illuminate\Http\Request;
use App\Services\EstimatorService;
use App\Services\ResendService;
use App\Services\ZipDistanceService;
use Illuminate\Support\Facades\Storage;

class MovingQuoteController extends Controller
{
    /**
     * The estimate is calculated for every lead but is a hard business
     * requirement that it never reaches the public form. This strips it
     * from a model before it's returned to the customer-facing API, unless
     * the (currently off) config flag turns that on for the future.
     */
    private function publicQuotePayload(MovingQuote $quote): array
    {
        $data = $quote->toArray();

        if (! config('estimator.show_estimate_to_customer')) {
            $data = collect($data)->except(MovingQuote::PUBLIC_HIDDEN_FIELDS)->all();
        }

        return $data;
    }

    /**
     * Step 1 of the booking form: contact info + date.
     * Saved immediately so the lead is recoverable even if the visitor
     * never reaches step 2.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'phone' => 'required|string|max:30',
            'sms_consent' => 'boolean',
            'email' => 'nullable|email|max:255',

            'date' => 'required_if:date_flexible,false|nullable|date',
            'date_flexible' => 'boolean',
            'schedule' => 'required|string|in:morning,afternoon,flexible',

            'origin_zip' => 'required|digits:5',
            'destination_zip' => 'required|digits:5',
        ]);

        $quote = MovingQuote::create([
            'name' => $validated['name'] ?? null,
            'phone' => $validated['phone'],
            'sms_consent' => $validated['sms_consent'] ?? false,
            'email' => $validated['email'] ?? null,

            'preferred_date' => $validated['date'] ?? null,
            'date_flexible' => $validated['date_flexible'] ?? false,
            'schedule' => $validated['schedule'],

            'origin_zip' => $validated['origin_zip'],
            'destination_zip' => $validated['destination_zip'],

            'status' => 'pending',
        ]);

        $sent = ResendService::sendLead($quote);

        $quote->email_sent = $sent;
        $quote->email_sent_at = now();
        $quote->save();

        return response()->json([
            'message' => 'Quote request saved successfully.',
            'email_sent' => $sent,
            'quote' => $this->publicQuotePayload($quote),
        ], 201);
    }

    /**
     * Step 2 of the booking form: size of the move. Completes the lead
     * created in store() and re-sends the lead email with the full details.
     */
    public function complete(Request $request, MovingQuote $movingQuote)
    {
        $validated = $request->validate([
            'move_type' => 'required|string|in:house,apartment,office,labor_only',
            'bedrooms' => 'required|string|in:studio,1,2,3,4+',

            'origin_floor' => 'required|string|max:10',
            'origin_elevator' => 'required|boolean',
            'destination_floor' => 'required|string|max:10',
            'destination_elevator' => 'required|boolean',

            'packing_service' => 'required|boolean',
            'special_items' => 'nullable|array',
            'special_items.*' => 'string|in:large_fridge,piano,pool_table,jacuzzi,safe',
            'comments' => 'nullable|string|max:500',

            'photos' => 'nullable|array|max:8',
            'photos.*' => 'file|image|max:8192',
        ]);

        $photoPaths = $movingQuote->photos ?? [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store("moving-quotes/{$movingQuote->id}", 'public');
                $photoPaths[] = Storage::disk('public')->url($path);
            }
        }

        $movingQuote->fill([
            'move_type' => $validated['move_type'],
            'bedrooms' => $validated['bedrooms'],

            'origin_floor' => $validated['origin_floor'],
            'origin_elevator' => $validated['origin_elevator'],
            'destination_floor' => $validated['destination_floor'],
            'destination_elevator' => $validated['destination_elevator'],

            'packing_service' => $validated['packing_service'],
            'special_items' => $validated['special_items'] ?? [],
            'comments' => $validated['comments'] ?? null,
            'photos' => $photoPaths,
        ]);

        // Straight-line distance between the ZIPs collected at step 1. Falls
        // back to null (no travel fee) when either ZIP isn't in the dataset,
        // instead of guessing.
        $miles = ZipDistanceService::milesBetween($movingQuote->origin_zip, $movingQuote->destination_zip);

        // Same EstimatorService the admin simulator uses, so the numbers can
        // never drift apart. The result is only ever stored on the lead and
        // sent to the owner's inbox, never returned to this endpoint's caller.
        $estimate = EstimatorService::fromDatabase()->estimate([
            'move_type' => $validated['move_type'],
            'bedrooms' => $validated['bedrooms'],
            'origin_floor' => $validated['origin_floor'],
            'origin_elevator' => $validated['origin_elevator'],
            'destination_floor' => $validated['destination_floor'],
            'destination_elevator' => $validated['destination_elevator'],
            'packing_service' => $validated['packing_service'],
            'special_items' => $validated['special_items'] ?? [],
            'miles' => $miles,
        ]);

        $movingQuote->estimate_total = $estimate['total'];
        $movingQuote->estimate_range_low = $estimate['range_low'];
        $movingQuote->estimate_range_high = $estimate['range_high'];
        $movingQuote->estimate_hours = $estimate['hours'];
        $movingQuote->estimate_breakdown = $estimate;
        $movingQuote->save();

        $sent = ResendService::sendLead($movingQuote);

        $movingQuote->email_sent = $sent;
        $movingQuote->email_sent_at = now();
        $movingQuote->save();

        return response()->json([
            'message' => 'Quote request completed successfully.',
            'email_sent' => $sent,
            'quote' => $this->publicQuotePayload($movingQuote),
        ]);
    }

    public function update(Request $request, MovingQuote $movingQuote)
    {
        // Validamos solo el status, porque es lo que viene del modal
        $data = $request->validate([
            'status' => 'required|string|in:pending,in_review,schedule,closed,cancelled',
        ]);

        $movingQuote->status = $data['status'];
        $movingQuote->save();

        return response()->json([
            'message' => 'Quote status updated successfully.',
            'quote'   => $movingQuote,
        ]);
    }

    public function resendEmail(MovingQuote $movingQuote)
    {
        $sent = ResendService::sendLead($movingQuote);

        $movingQuote->email_sent = $sent;
        $movingQuote->email_sent_at = now();
        $movingQuote->save();

        return response()->json([
            'message' => $sent ? 'Email resent successfully.' : 'Email could not be sent.',
            'email_sent' => $sent,
            'quote' => $movingQuote,
        ], $sent ? 200 : 502);
    }
}
