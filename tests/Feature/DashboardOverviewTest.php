<?php

namespace Tests\Feature;

use App\Models\MovingQuote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardOverviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
        $this->get('/dashboard/leads')->assertRedirect('/login');
    }

    public function test_overview_renders_with_and_without_leads(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/dashboard')->assertOk()->assertSee('No leads yet.');

        MovingQuote::create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'origin_address' => 'Dallas, TX',
            'destination_address' => 'Plano, TX',
        ]);

        $this->actingAs($user)->get('/dashboard')->assertOk()->assertSee('Jane Doe');
    }

    public function test_leads_page_renders_the_table_component(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/dashboard/leads')
            ->assertOk()
            ->assertSee('data-vue="BookingTable"', false);
    }
}
