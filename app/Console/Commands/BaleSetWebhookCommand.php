<?php

namespace App\Console\Commands;

use App\Services\BaleClient;
use Illuminate\Console\Command;

class BaleSetWebhookCommand extends Command
{
    protected $signature = 'bale:set-webhook {url? : Public HTTPS webhook URL}';

    protected $description = 'Register the Bale bot webhook URL.';

    public function handle(BaleClient $client): int
    {
        $url = $this->argument('url') ?: route('bale.webhook');
        $response = $client->setWebhook($url);

        $this->line(json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return (bool) ($response['ok'] ?? false) ? self::SUCCESS : self::FAILURE;
    }
}
