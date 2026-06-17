<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class BaleClient
{
    private function http(): PendingRequest
    {
        return Http::baseUrl(rtrim(config('bale.api_base_url'), '/').'/bot'.config('bale.token'))
            ->acceptJson()
            ->asJson()
            ->timeout(10);
    }

    public function sendMessage(int|string $chatId, string $text, array $replyMarkup = []): array
    {
        $payload = [
            'chat_id' => $chatId,
            'text' => $text,
        ];

        if ($replyMarkup !== []) {
            $payload['reply_markup'] = $replyMarkup;
        }

        return $this->http()->post('/sendMessage', $payload)->json() ?? [];
    }

    public function setWebhook(string $url): array
    {
        return $this->http()->post('/setWebhook', ['url' => $url])->json() ?? [];
    }
}
