<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\WebhookEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_passes_counts_and_recent_records_to_view(): void
    {
        Lead::create(['bale_user_id' => '1', 'chat_id' => '1', 'intent' => 'webinar']);
        Lead::create(['bale_user_id' => '2', 'chat_id' => '2', 'intent' => 'course']);
        Lead::create(['bale_user_id' => '3', 'chat_id' => '3', 'intent' => 'technical_support']);
        Lead::create(['bale_user_id' => '4', 'chat_id' => '4', 'intent' => 'consultant']);
        WebhookEvent::create(['event_id' => 'evt-1', 'payload' => ['ok' => true]]);

        $response = $this->get(route('dashboard'));

        $response->assertOk()
            ->assertViewHas('leadCount', 4)
            ->assertViewHas('webinarCount', 1)
            ->assertViewHas('courseCount', 1)
            ->assertViewHas('technicalCount', 1)
            ->assertViewHas('consultantCount', 1)
            ->assertViewHas('leads', fn ($leads): bool => $leads->count() === 4)
            ->assertViewHas('events', fn ($events): bool => $events->count() === 1);
    }

    public function test_dashboard_can_render_in_english_and_persian(): void
    {
        $this->get(route('dashboard', ['lang' => 'en']))
            ->assertOk()
            ->assertSee('Course Sales Bot Dashboard')
            ->assertSee('dir="ltr"', false);

        $this->get(route('dashboard', ['lang' => 'fa']))
            ->assertOk()
            ->assertSee('داشبورد بات فروش دوره')
            ->assertSee('dir="rtl"', false);
    }
}
