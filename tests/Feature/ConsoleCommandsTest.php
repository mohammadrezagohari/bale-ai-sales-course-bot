<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ConsoleCommandsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_create_user_creates_user_with_hashed_password(): void
    {
        $this->artisan('admin:create-user', [
            'email' => 'admin@example.com',
            '--name' => 'Main Admin',
            '--password' => 'secret-password',
        ])->assertSuccessful();

        $user = User::where('email', 'admin@example.com')->firstOrFail();

        $this->assertSame('Main Admin', $user->name);
        $this->assertTrue(Hash::check('secret-password', $user->password));
    }

    public function test_admin_create_user_requires_password(): void
    {
        $this->artisan('admin:create-user', [
            'email' => 'admin@example.com',
            '--password' => '',
        ])
            ->expectsQuestion('Password', '')
            ->assertFailed();
    }

    public function test_bale_set_webhook_command_returns_success_when_api_succeeds(): void
    {
        Http::fake(['https://tapi.bale.ai/*' => Http::response(['ok' => true])]);

        $this->artisan('bale:set-webhook', ['url' => 'https://example.com/webhook/bale'])
            ->assertSuccessful();

        Http::assertSent(fn ($request): bool => str_ends_with($request->url(), '/setWebhook')
            && $request->data()['url'] === 'https://example.com/webhook/bale');
    }

    public function test_bale_set_webhook_command_returns_failure_when_api_fails(): void
    {
        Http::fake(['https://tapi.bale.ai/*' => Http::response(['ok' => false])]);

        $this->artisan('bale:set-webhook', ['url' => 'https://example.com/webhook/bale'])
            ->assertFailed();
    }
}
