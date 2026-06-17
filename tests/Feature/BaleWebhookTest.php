<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
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
}
