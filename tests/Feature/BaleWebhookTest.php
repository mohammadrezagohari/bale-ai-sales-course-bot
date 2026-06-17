<?php

namespace Tests\Feature;

use App\Services\BaleBotService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Mockery;
use Tests\TestCase;

class BaleWebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_start_message_returns_ok_and_calls_bale_api(): void
    {
        Http::fake([
            'https://tapi.bale.ai/*' => Http::response(['ok' => true], 200),
        ]);

        $response = $this->postJson(route('bale.webhook'), [
            'update_id' => 1,
            'message' => [
                'text' => '/start',
                'chat' => ['id' => 100],
                'from' => ['id' => 100, 'first_name' => 'Ali'],
            ],
        ]);

        $response->assertOk()->assertJson(['ok' => true]);

        Http::assertSent(fn ($request) => str_ends_with($request->url(), '/sendMessage'));
    }

    public function test_webhook_secret_is_required_when_configured(): void
    {
        config(['bale.webhook_secret' => 'top-secret']);

        $this->postJson(route('bale.webhook'), ['update_id' => 2])
            ->assertForbidden();
    }

    public function test_webhook_secret_allows_valid_requests(): void
    {
        config(['bale.webhook_secret' => 'top-secret']);

        $bot = Mockery::mock(BaleBotService::class);
        $bot->shouldReceive('handleUpdate')
            ->once()
            ->with(['update_id' => 3]);

        $this->app->instance(BaleBotService::class, $bot);

        $this->withHeader('X-Bale-Secret', 'top-secret')
            ->postJson(route('bale.webhook'), ['update_id' => 3])
            ->assertOk()
            ->assertJson(['ok' => true]);
    }

    public function test_webhook_logs_bot_failures_but_still_acknowledges_bale(): void
    {
        Log::spy();

        $bot = Mockery::mock(BaleBotService::class);
        $bot->shouldReceive('handleUpdate')
            ->once()
            ->andThrow(new \RuntimeException('boom'));

        $this->app->instance(BaleBotService::class, $bot);

        $this->postJson(route('bale.webhook'), ['update_id' => 4])
            ->assertOk()
            ->assertJson(['ok' => true]);

        Log::shouldHaveReceived('error')
            ->once()
            ->with('Bale webhook failed', Mockery::on(
                fn (array $context): bool => $context['message'] === 'boom'
                    && $context['update'] === ['update_id' => 4]
            ));
    }
}
