<?php

namespace Tests\Feature;

use App\Models\MoveEvent;
use App\Models\MovingQuote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MoveEventCalendarTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'customer_name' => 'Jane Doe',
            'start_at' => '2026-11-10T08:00',
            'end_at' => '2026-11-10T12:00',
        ], $overrides);
    }

    public function test_guests_cannot_use_the_calendar(): void
    {
        $this->postJson('/api/move-events', $this->payload())->assertStatus(401);
    }

    public function test_scheduling_a_quote_moves_it_to_schedule_and_back(): void
    {
        $user = User::factory()->create();
        $quote = MovingQuote::create(['name' => 'Jane', 'phone' => '7705551234', 'status' => 'in_review']);

        $id = $this->actingAs($user)
            ->postJson('/api/move-events', $this->payload(['moving_quote_id' => $quote->id]))
            ->assertStatus(201)
            ->json('event.id');

        $this->assertSame('schedule', $quote->fresh()->status);
        $this->assertSame('Jane', MoveEvent::find($id)->title);

        $this->actingAs($user)->deleteJson("/api/move-events/{$id}")->assertOk();
        $this->assertSame('in_review', $quote->fresh()->status);
    }

    public function test_capacity_conflict_is_rejected_unless_forced(): void
    {
        config(['scheduling.max_concurrent_moves' => 1]);
        $user = User::factory()->create();

        $this->actingAs($user)->postJson('/api/move-events', $this->payload())->assertStatus(201);

        $overlap = $this->payload(['start_at' => '2026-11-10T10:00', 'end_at' => '2026-11-10T14:00']);
        $this->actingAs($user)->postJson('/api/move-events', $overlap)->assertStatus(409);
        $this->actingAs($user)->postJson('/api/move-events', $overlap + ['force' => true])->assertStatus(201);

        // Back-to-back is not a conflict.
        $this->actingAs($user)->postJson('/api/move-events', $this->payload([
            'start_at' => '2026-11-11T08:00', 'end_at' => '2026-11-11T12:00',
        ]))->assertStatus(201);
        $this->actingAs($user)->postJson('/api/move-events', $this->payload([
            'start_at' => '2026-11-11T12:00', 'end_at' => '2026-11-11T16:00',
        ]))->assertStatus(201);
    }

    public function test_end_must_be_after_start(): void
    {
        $this->actingAs(User::factory()->create())
            ->postJson('/api/move-events', $this->payload(['end_at' => '2026-11-10T07:00']))
            ->assertStatus(422);
    }

    public function test_drag_and_drop_partial_update_keeps_other_fields(): void
    {
        $user = User::factory()->create();
        $id = $this->actingAs($user)->postJson('/api/move-events', $this->payload(['notes' => 'Piano']))->json('event.id');

        $this->actingAs($user)->putJson("/api/move-events/{$id}", [
            'start_at' => '2026-11-12T09:00', 'end_at' => '2026-11-12T13:00',
        ])->assertOk();

        $event = MoveEvent::find($id);
        $this->assertSame('Piano', $event->notes);
        $this->assertSame('2026-11-12 09:00:00', $event->start_at->toDateTimeString());
    }
}
