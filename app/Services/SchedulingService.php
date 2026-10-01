<?php

namespace App\Services;

use App\Models\MoveEvent;
use App\Models\MovingQuote;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class SchedulingService
{
    /**
     * Active moves that overlap the given window. A window that ends exactly
     * when another starts is not a conflict.
     */
    public function overlapping(Carbon $start, Carbon $end, ?int $ignoreId = null): Collection
    {
        return MoveEvent::active()
            ->overlapping($start, $end)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->orderBy('start_at')
            ->get();
    }

    /**
     * True when adding one more move to the window would exceed the number of
     * moves that can run simultaneously. Peak concurrency is evaluated at each
     * start boundary, so two non-overlapping moves never block a third.
     */
    public function exceedsCapacity(Carbon $start, Carbon $end, ?int $ignoreId = null): bool
    {
        $events = $this->overlapping($start, $end, $ignoreId);
        $max = (int) config('scheduling.max_concurrent_moves', 2);

        $points = $events->pluck('start_at')->push($start)
            ->filter(fn ($t) => $t >= $start && $t < $end)
            ->unique(fn ($t) => $t->timestamp);

        foreach ($points as $point) {
            $running = $events->filter(fn ($e) => $e->start_at <= $point && $e->end_at > $point)->count();

            if ($running >= $max) {
                return true;
            }
        }

        return false;
    }

    /** Suggested duration in hours for a quote, based on its estimate. */
    public function suggestedHours(?MovingQuote $quote): float
    {
        $hours = (float) ($quote?->estimate_hours ?? 0);

        return $hours > 0 ? $hours : (float) config('scheduling.default_duration_hours', 4);
    }

    /**
     * Keeps the lead's pipeline status coherent with its calendar events:
     * any active event -> schedule; only completed ones -> closed; no live
     * events left -> a previously scheduled lead goes back to in_review.
     * Leads the owner already closed or cancelled by hand are left alone
     * unless a new active event is added.
     */
    public function syncQuoteStatus(?int $quoteId): void
    {
        if (! $quoteId || ! ($quote = MovingQuote::find($quoteId))) {
            return;
        }

        $statuses = $quote->moveEvents()->pluck('status');

        if ($statuses->intersect(MoveEvent::ACTIVE_STATUSES)->isNotEmpty()) {
            $new = 'schedule';
        } elseif ($statuses->contains('completed')) {
            $new = 'closed';
        } elseif ($quote->status === 'schedule') {
            $new = 'in_review';
        } else {
            return;
        }

        if ($quote->status !== $new) {
            $quote->update(['status' => $new]);
        }
    }
}
