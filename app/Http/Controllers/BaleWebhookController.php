<?php

namespace App\Http\Controllers;

use App\Services\BaleBotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BaleWebhookController extends Controller
{
    public function __construct(private readonly BaleBotService $bot)
    {
    }

    public function handle(Request $request): JsonResponse
    {
        if ($secret = config('bale.webhook_secret')) {
            abort_unless($request->header('X-Bale-Secret') === $secret, 403);
        }

        try {
            $this->bot->handleUpdate($request->all());
        } catch (\Throwable $exception) {
            Log::error('Bale webhook failed', [
                'message' => $exception->getMessage(),
                'update' => $request->all(),
            ]);
        }

        return response()->json(['ok' => true]);
    }
}
