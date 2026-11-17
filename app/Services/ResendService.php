<?php

namespace App\Services;

use App\Models\MoveEvent;
use App\Models\MovingQuote;
use App\Models\PricingSetting;
use Illuminate\Support\Facades\Log;
use Resend;

class ResendService
{
    public static function sendLead(MovingQuote $data): bool
    {
        $apiKey    = config('services.resend.api_key');
        $fromEmail = config('services.resend.from_email');
        $fromName  = config('services.resend.from_name');
        $toEmail   = config('services.resend.to_email');

        if (!$apiKey) {
            Log::error('Resend: API key is missing');
            return false;
        }

        $unconfirmedKeys = $data->estimate_breakdown['unconfirmed_used'] ?? [];
        $unconfirmedLabels = $unconfirmedKeys
            ? PricingSetting::whereIn('key', $unconfirmedKeys)->pluck('label')->all()
            : [];

        // Render del template Blade
        $body = view('emails.moving_lead', [
            'quote' => $data,
            'unconfirmedLabels' => $unconfirmedLabels,
        ])->render();

        try {
            $response = Resend::client($apiKey)->emails->send([
                'from' => "{$fromName} <{$fromEmail}>",
                'to' => [$toEmail],
                'subject' => '🟡 New Contact from Golden Street Moving',
                'html' => $body,
                'attachments' => [
                    [
                        'filename' => 'logo-1.png',
                        'content' => base64_encode(file_get_contents(public_path('images/logo-1.png'))),
                        'content_id' => 'logo-1',
                    ],
                ],
            ]);

            Log::info('Resend email id: ' . ($response['id'] ?? 'unknown'));

            return true;
        } catch (\Exception $e) {
            Log::error('Resend error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Confirmation sent to the customer once their move is on the calendar.
     */
    public static function sendMoveConfirmation(MoveEvent $event, string $customerEmail): bool
    {
        return self::deliver(
            [$customerEmail],
            'Your move with Golden Street Moving is scheduled',
            'emails.move_confirmation',
            ['event' => $event]
        );
    }

    /**
     * "Your move is tomorrow" reminder. Goes to the customer (when they left
     * an email) and to the owner inbox from RESEND_TO_EMAIL.
     */
    public static function sendMoveReminder(MoveEvent $event, ?string $customerEmail): bool
    {
        $sent = false;

        if ($customerEmail) {
            $sent = self::deliver(
                [$customerEmail],
                'Reminder: your move with Golden Street Moving is coming up',
                'emails.move_reminder',
                ['event' => $event, 'forOwner' => false]
            ) || $sent;
        }

        if ($ownerEmail = config('services.resend.to_email')) {
            $sent = self::deliver(
                [$ownerEmail],
                '⏰ Move reminder: ' . $event->start_at->format('M j, g:i A'),
                'emails.move_reminder',
                ['event' => $event, 'forOwner' => true]
            ) || $sent;
        }

        return $sent;
    }

    private static function deliver(array $to, string $subject, string $view, array $data): bool
    {
        $apiKey = config('services.resend.api_key');

        if (! $apiKey) {
            Log::error('Resend: API key is missing');
            return false;
        }

        $fromEmail = config('services.resend.from_email');
        $fromName  = config('services.resend.from_name');

        try {
            $response = Resend::client($apiKey)->emails->send([
                'from' => "{$fromName} <{$fromEmail}>",
                'to' => $to,
                'subject' => $subject,
                'html' => view($view, $data)->render(),
                'attachments' => [
                    [
                        'filename' => 'logo-1.png',
                        'content' => base64_encode(file_get_contents(public_path('images/logo-1.png'))),
                        'content_id' => 'logo-1',
                    ],
                ],
            ]);

            Log::info("Resend [{$view}] email id: " . ($response['id'] ?? 'unknown'));

            return true;
        } catch (\Exception $e) {
            Log::error("Resend [{$view}] error: " . $e->getMessage());
            return false;
        }
    }
}
