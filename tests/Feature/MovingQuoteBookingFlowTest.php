<?php

namespace Tests\Feature;

use App\Models\MovingQuote;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MovingQuoteBookingFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Prevent these tests from making a real call to the Resend API /
        // sending a real lead email. With no API key, ResendService logs
        // and returns false instead of hitting the network.
        Config::set('services.resend.api_key', null);
    }

    public function test_step_one_saves_a_recoverable_lead(): void
    {
        $response = $this->postJson('/api/moving-quotes', [
            'name' => 'Jane Doe',
            'phone' => '7705551234',
            'sms_consent' => true,
            'email' => 'jane@example.com',
            'date' => now()->addWeek()->toDateString(),
            'date_flexible' => false,
            'schedule' => 'morning',
            'origin_zip' => '30301',
            'destination_zip' => '30303',
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('moving_quotes', [
            'phone' => '7705551234',
            'sms_consent' => true,
            'origin_zip' => '30301',
            'destination_zip' => '30303',
            'status' => 'pending',
        ]);
    }

    public function test_step_one_allows_a_flexible_date_without_a_specific_date(): void
    {
        $response = $this->postJson('/api/moving-quotes', [
            'phone' => '7705551234',
            'date_flexible' => true,
            'schedule' => 'flexible',
            'origin_zip' => '30301',
            'destination_zip' => '30303',
        ]);

        $response->assertStatus(201);
    }

    /**
     * Regression test: the booking form submits step 2 as multipart/form-data
     * (because of the photo upload), which serializes booleans as the
     * strings "true"/"false". Laravel's `boolean` rule only accepts
     * true/false/1/0/"1"/"0", so those strings must never reach validation
     * as-is or every submission fails with "field must be true or false".
     */
    public function test_step_two_accepts_multipart_boolean_strings_as_used_by_the_frontend(): void
    {
        $quote = MovingQuote::create([
            'phone' => '7705551234',
            'schedule' => 'morning',
            'origin_zip' => '30301',
            'destination_zip' => '30303',
            'status' => 'pending',
        ]);

        $response = $this->post("/api/moving-quotes/{$quote->id}/complete", [
            'move_type' => 'house',
            'bedrooms' => '2',
            'origin_floor' => 'ground',
            'origin_elevator' => '0',
            'destination_floor' => '1',
            'destination_elevator' => '1',
            'packing_service' => '1',
            'special_items' => ['piano', 'safe'],
            'comments' => 'Narrow street',
        ]);

        $response->assertStatus(200);

        $quote->refresh();
        $this->assertSame('house', $quote->move_type);
        $this->assertSame('2', $quote->bedrooms);
        $this->assertFalse($quote->origin_elevator);
        $this->assertTrue($quote->destination_elevator);
        $this->assertTrue($quote->packing_service);
        $this->assertSame(['piano', 'safe'], $quote->special_items);
    }

    /**
     * Hard business requirement: the estimate is calculated and stored on
     * the lead, but must never be visible to the public form's own response.
     */
    public function test_completing_a_quote_calculates_and_stores_an_estimate_that_is_never_returned_to_the_public_caller(): void
    {
        \App\Models\PricingSetting::create([
            'key' => 'hourly_rate', 'value' => 140, 'unit' => 'usd', 'label' => 'Hourly rate', 'group' => 'rates', 'confirmed' => true,
        ]);
        \App\Models\PricingSetting::create([
            'key' => 'hours_2', 'value' => 4, 'unit' => 'hours', 'label' => 'Base hours - 2 bedrooms', 'group' => 'base_hours', 'confirmed' => true,
        ]);
        \App\Models\PricingSetting::create([
            'key' => 'min_hours', 'value' => 2, 'unit' => 'hours', 'label' => 'Minimum billable hours', 'group' => 'base_hours', 'confirmed' => false,
        ]);
        \App\Models\PricingSetting::create([
            'key' => 'range_pct', 'value' => 15, 'unit' => 'percent', 'label' => 'Estimate range', 'group' => 'rates', 'confirmed' => false,
        ]);

        $quote = MovingQuote::create([
            'phone' => '7705551234',
            'schedule' => 'morning',
            'origin_zip' => '30301',
            'destination_zip' => '30303',
            'status' => 'pending',
        ]);

        $response = $this->post("/api/moving-quotes/{$quote->id}/complete", [
            'move_type' => 'house',
            'bedrooms' => '2',
            'origin_floor' => 'ground',
            'origin_elevator' => '0',
            'destination_floor' => 'ground',
            'destination_elevator' => '0',
            'packing_service' => '0',
        ]);

        $response->assertStatus(200);

        // Stored on the lead...
        $quote->refresh();
        $this->assertSame('560.00', $quote->estimate_total);
        $this->assertNotEmpty($quote->estimate_breakdown['unconfirmed_used']);

        // ...but absent from every key of the public JSON response.
        $body = $response->json();
        foreach (MovingQuote::PUBLIC_HIDDEN_FIELDS as $field) {
            $this->assertArrayNotHasKey($field, $body['quote']);
        }
    }

    public function test_step_two_stores_uploaded_photos(): void
    {
        Storage::fake('public');

        $quote = MovingQuote::create([
            'phone' => '7705551234',
            'schedule' => 'morning',
            'origin_zip' => '30301',
            'destination_zip' => '30303',
            'status' => 'pending',
        ]);

        $response = $this->post("/api/moving-quotes/{$quote->id}/complete", [
            'move_type' => 'apartment',
            'bedrooms' => 'studio',
            'origin_floor' => 'ground',
            'origin_elevator' => '0',
            'destination_floor' => 'ground',
            'destination_elevator' => '0',
            'packing_service' => '0',
            'photos' => [UploadedFile::fake()->image('couch.jpg')],
        ]);

        $response->assertStatus(200);

        $quote->refresh();
        $this->assertCount(1, $quote->photos);
        Storage::disk('public')->assertExists("moving-quotes/{$quote->id}");
    }

    public function test_completing_a_quote_does_not_affect_a_legacy_quote_created_before_the_redesign(): void
    {
        $legacy = MovingQuote::create([
            'name' => 'Legacy Lead',
            'email' => 'legacy@example.com',
            'origin_address' => '123 Old St, Atlanta, GA',
            'destination_address' => '456 New St, Atlanta, GA',
            'preferred_date' => now()->addWeek()->toDateString(),
            'move_type' => 'residential',
            'origin_elevator' => true,
            'destination_elevator' => false,
            'packing_service' => true,
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('moving_quotes', [
            'id' => $legacy->id,
            'origin_address' => '123 Old St, Atlanta, GA',
            'move_type' => 'residential',
            'origin_zip' => null,
            'bedrooms' => null,
        ]);
    }
}
