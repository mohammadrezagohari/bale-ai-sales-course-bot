<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseOrder;
use App\Models\Lead;
use App\Models\TeamMember;
use App\Models\WebhookEvent;
use App\Models\Webinar;
use App\Models\WebinarRegistration;
use App\Services\BaleBotService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use ReflectionMethod;
use Tests\TestCase;

class BaleBotServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_course_list_records_a_lead_and_returns_only_active_courses_in_order(): void
    {
        Http::fake(['https://tapi.bale.ai/*' => Http::response(['ok' => true])]);

        Course::create([
            'title' => 'Inactive course',
            'slug' => 'inactive-course',
            'is_active' => false,
            'sort_order' => 1,
        ]);
        $second = Course::create([
            'title' => 'Second course',
            'slug' => 'second-course',
            'is_active' => true,
            'sort_order' => 20,
        ]);
        $first = Course::create([
            'title' => 'First course',
            'slug' => 'first-course',
            'is_active' => true,
            'sort_order' => 10,
        ]);

        $this->postJson(route('bale.webhook'), $this->messageUpdate('course:list'))->assertOk();

        $this->assertDatabaseHas('webhook_events', ['event_id' => '1001']);
        $this->assertDatabaseHas('leads', [
            'bale_user_id' => '42',
            'chat_id' => '42',
            'intent' => 'course_list',
            'first_name' => 'Ali',
        ]);

        Http::assertSent(function ($request) use ($first, $second): bool {
            $payload = $request->data();

            return str_ends_with($request->url(), '/sendMessage')
                && $payload['chat_id'] === 42
                && $payload['reply_markup']['inline_keyboard'][0][0]['callback_data'] === 'course:'.$first->id
                && $payload['reply_markup']['inline_keyboard'][1][0]['callback_data'] === 'course:'.$second->id;
        });
    }

    public function test_buy_course_creates_or_updates_lead_and_creates_pending_order(): void
    {
        Http::fake(['https://tapi.bale.ai/*' => Http::response(['ok' => true])]);

        $course = Course::create([
            'title' => 'Laravel Bot Course',
            'slug' => 'laravel-bot-course',
            'price' => 250000,
            'checkout_url' => 'https://example.com/pay/course',
            'is_active' => true,
        ]);

        $this->postJson(route('bale.webhook'), $this->callbackUpdate('buy:'.$course->id))->assertOk();

        $lead = Lead::where('bale_user_id', '42')->where('intent', 'course_purchase')->firstOrFail();

        $this->assertDatabaseHas('course_orders', [
            'course_id' => $course->id,
            'lead_id' => $lead->id,
            'amount' => 250000,
            'status' => 'pending',
        ]);

        Http::assertSent(fn ($request): bool => str_contains($request->data()['text'], 'https://example.com/pay/course'));
    }

    public function test_webinar_registration_is_idempotent_for_same_lead(): void
    {
        Http::fake(['https://tapi.bale.ai/*' => Http::response(['ok' => true])]);

        $webinar = Webinar::create([
            'title' => 'Sales webinar',
            'registration_url' => 'https://example.com/webinar',
            'is_active' => true,
        ]);

        $payload = $this->callbackUpdate('webinar:'.$webinar->id);

        $this->postJson(route('bale.webhook'), $payload)->assertOk();
        $this->postJson(route('bale.webhook'), array_replace($payload, ['update_id' => 1002]))->assertOk();

        $this->assertSame(1, WebinarRegistration::count());
        $this->assertDatabaseHas('webinar_registrations', [
            'webinar_id' => $webinar->id,
            'status' => 'registered',
        ]);
    }

    public function test_team_message_lists_active_members_only(): void
    {
        Http::fake(['https://tapi.bale.ai/*' => Http::response(['ok' => true])]);

        TeamMember::create([
            'name' => 'Hidden Member',
            'role' => 'Inactive',
            'is_active' => false,
        ]);
        TeamMember::create([
            'name' => 'Visible Member',
            'role' => 'Instructor',
            'bio' => 'Course teacher',
            'is_active' => true,
        ]);

        $this->invokeBotMethod('team', 42);

        Http::assertSent(function ($request): bool {
            $text = $request->data()['text'];

            return str_contains($text, 'Visible Member')
                && ! str_contains($text, 'Hidden Member');
        });
    }

    public function test_support_and_consultant_paths_store_their_intents(): void
    {
        Http::fake(['https://tapi.bale.ai/*' => Http::response(['ok' => true])]);
        config([
            'bale.support_technical_url' => 'https://example.com/support',
            'bale.support_consultant_url' => 'https://example.com/consultant',
        ]);

        $this->invokeBotMethod('support', 42, $this->messageUpdate('support')['message']);
        $this->invokeBotMethod('consultant', 42, $this->messageUpdate('consultant')['message']);

        $this->assertDatabaseHas('leads', ['bale_user_id' => '42', 'intent' => 'technical_support']);
        $this->assertDatabaseHas('leads', ['bale_user_id' => '42', 'intent' => 'consultant']);

        Http::assertSent(fn ($request): bool => str_contains($request->data()['text'], 'https://example.com/support'));
        Http::assertSent(fn ($request): bool => str_contains($request->data()['text'], 'https://example.com/consultant'));
    }

    public function test_update_without_message_only_records_webhook_event(): void
    {
        Http::fake();

        $this->postJson(route('bale.webhook'), ['update_id' => 999, 'poll' => ['id' => 'p1']])->assertOk();

        $this->assertSame(1, WebhookEvent::count());
        $this->assertSame(0, Lead::count());
        Http::assertNothingSent();
    }

    private function messageUpdate(string $text, int $updateId = 1001): array
    {
        return [
            'update_id' => $updateId,
            'message' => [
                'text' => $text,
                'chat' => ['id' => 42],
                'from' => [
                    'id' => 42,
                    'first_name' => 'Ali',
                    'last_name' => 'Ahmadi',
                    'username' => 'ali',
                ],
            ],
        ];
    }

    private function callbackUpdate(string $data, int $updateId = 1001): array
    {
        return [
            'update_id' => $updateId,
            'callback_query' => [
                'data' => $data,
                'from' => [
                    'id' => 42,
                    'first_name' => 'Ali',
                    'last_name' => 'Ahmadi',
                    'username' => 'ali',
                ],
                'message' => [
                    'chat' => ['id' => 42],
                ],
            ],
        ];
    }

    private function invokeBotMethod(string $method, mixed ...$arguments): void
    {
        $reflection = new ReflectionMethod(BaleBotService::class, $method);
        $reflection->setAccessible(true);
        $reflection->invoke(app(BaleBotService::class), ...$arguments);
    }
}
