<?php

namespace App\Services;

use App\Models\MovingQuote;
use SendGrid\Mail\Mail;
use Illuminate\Support\Facades\Log;

class SendGridService
{
    public static function sendLead(MovingQuote $data): bool
    {
        $apiKey    = config('services.sendgrid.api_key');
        $fromEmail = config('services.sendgrid.from_email');
        $fromName  = config('services.sendgrid.from_name');
        $toEmail   = config('services.sendgrid.to_email');

        if (!$apiKey) {
            Log::error('SendGrid: API key is missing');
            return false;
        }

        $email = new Mail();
        $email->setFrom($fromEmail, $fromName);
        $email->setSubject('🟡 New Contact from Golden Street Moving');
        $email->addTo($toEmail, 'Golden Street Moving');

        // Render del template Blade
        $body = view('emails.moving_lead', [
            'quote' => $data,
        ])->render();

        $email->addContent('text/html', $body);

        $sendgrid = new \SendGrid($apiKey);

        try {
            $response = $sendgrid->send($email);

            Log::info('SendGrid status: '.$response->statusCode());
            Log::info('SendGrid body: '.$response->body());

            return $response->statusCode() === 202;
        } catch (\Exception $e) {
            Log::error('SendGrid error: ' . $e->getMessage());
            return false;
        }
    }
}
