<?php

namespace App\Services;

use App\Models\MoveEvent;

class MoveNotifier
{
    /** Hours before the move at which the reminder is sent. */
    public const REMINDER_LEAD_HOURS = 24;

    /**
     * Call after an event is created or changed. Sends the customer
     * confirmation once, and the reminder right away when the move is already
     * less than 24h out (the hourly job would otherwise be too late or slow).
     */
    public function handleScheduled(MoveEvent $event): void
    {
        if (! in_array($event->status, MoveEvent::ACTIVE_STATUSES, true)) {
            return;
        }

        $email = $this->customerEmail($event);

        if ($email && ! $event->confirmation_sent_at) {
            if (ResendService::sendMoveConfirmation($event, $email)) {
                $event->forceFill(['confirmation_sent_at' => now()])->save();
            }
        }

        if ($this->isReminderDue($event)) {
            $this->sendReminder($event);
        }
    }

    /** Sends every pending reminder that is due. Returns how many were sent. */
    public function sendDueReminders(): int
    {
        $sent = 0;

        MoveEvent::active()
            ->whereNull('reminder_sent_at')
            ->where('start_at', '>', now())
            ->where('start_at', '<=', now()->addHours(self::REMINDER_LEAD_HOURS))
            ->with('quote')
            ->each(function (MoveEvent $event) use (&$sent) {
                if ($this->sendReminder($event)) {
                    $sent++;
                }
            });

        return $sent;
    }

    private function isReminderDue(MoveEvent $event): bool
    {
        return ! $event->reminder_sent_at
            && $event->start_at->isFuture()
            && $event->start_at->lessThanOrEqualTo(now()->addHours(self::REMINDER_LEAD_HOURS));
    }

    private function sendReminder(MoveEvent $event): bool
    {
        if (! ResendService::sendMoveReminder($event, $this->customerEmail($event))) {
            return false;
        }

        $event->forceFill(['reminder_sent_at' => now()])->save();

        return true;
    }

    private function customerEmail(MoveEvent $event): ?string
    {
        return $event->quote?->email ?: null;
    }
}
