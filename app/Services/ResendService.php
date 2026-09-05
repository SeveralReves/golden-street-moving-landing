<?php

namespace App\Services;

use App\Models\MovingQuote;
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

        // Render del template Blade
        $body = view('emails.moving_lead', [
            'quote' => $data,
        ])->render();

        try {
            $response = Resend::client($apiKey)->emails->send([
                'from' => "{$fromName} <{$fromEmail}>",
                'to' => [$toEmail],
                'subject' => '🟡 New Contact from Golden Street Moving',
                'html' => $body,
            ]);

            Log::info('Resend email id: ' . ($response['id'] ?? 'unknown'));

            return true;
        } catch (\Exception $e) {
            Log::error('Resend error: ' . $e->getMessage());
            return false;
        }
    }
}
