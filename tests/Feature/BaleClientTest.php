<?php

namespace Tests\Feature;

use App\Services\BaleClient;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BaleClientTest extends TestCase
{
    public function test_send_message_posts_to_bale_api_with_reply_markup(): void
    {
        Http::fake(['https://bale.test/*' => Http::response(['ok' => true, 'result' => ['message_id' => 1]])]);
        config([
            'bale.api_base_url' => 'https://bale.test',
            'bale.token' => 'testing-token',
        ]);

        $response = app(BaleClient::class)->sendMessage(123, 'Hello', [
            'keyboard' => [[['text' => 'Start']]],
        ]);

        $this->assertTrue($response['ok']);

        Http::assertSent(fn ($request): bool => $request->url() === 'https://bale.test/bottesting-token/sendMessage'
            && $request->method() === 'POST'
            && $request->data()['chat_id'] === 123
            && $request->data()['text'] === 'Hello'
            && $request->data()['reply_markup']['keyboard'][0][0]['text'] === 'Start');
    }

    public function test_set_webhook_posts_url_to_bale_api(): void
    {
        Http::fake(['https://bale.test/*' => Http::response(['ok' => true])]);
        config([
            'bale.api_base_url' => 'https://bale.test/',
            'bale.token' => 'testing-token',
        ]);

        $response = app(BaleClient::class)->setWebhook('https://app.test/webhook/bale');

        $this->assertTrue($response['ok']);

        Http::assertSent(fn ($request): bool => $request->url() === 'https://bale.test/bottesting-token/setWebhook'
            && $request->data()['url'] === 'https://app.test/webhook/bale');
    }
}
