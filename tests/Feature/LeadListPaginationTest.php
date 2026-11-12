<?php

namespace Tests\Feature;

use App\Models\MovingQuote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadListPaginationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_list_leads(): void
    {
        $this->getJson('/api/moving-quotes')->assertStatus(401);
    }

    public function test_leads_are_paginated_newest_first(): void
    {
        foreach (range(1, 20) as $i) {
            MovingQuote::create(['name' => "Lead {$i}", 'phone' => '7705550000', 'status' => 'pending']);
        }

        $response = $this->actingAs(User::factory()->create())
            ->getJson('/api/moving-quotes?per_page=15')
            ->assertOk()
            ->assertJsonPath('total', 20)
            ->assertJsonPath('last_page', 2)
            ->assertJsonCount(15, 'data');

        $this->assertSame('Lead 20', $response->json('data.0.name'));

        $this->actingAs(User::factory()->create())
            ->getJson('/api/moving-quotes?per_page=15&page=2')
            ->assertJsonCount(5, 'data');
    }

    public function test_a_single_lead_can_be_fetched(): void
    {
        $quote = MovingQuote::create(['name' => 'Jane', 'phone' => '7705551234']);

        $this->actingAs(User::factory()->create())
            ->getJson("/api/moving-quotes/{$quote->id}")
            ->assertOk()
            ->assertJsonPath('name', 'Jane');
    }
}
