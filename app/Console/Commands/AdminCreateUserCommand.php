<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class AdminCreateUserCommand extends Command
{
    protected $signature = 'admin:create-user {email} {--name=Admin} {--password=}';

    protected $description = 'Create or update a Filament admin user.';

    public function handle(): int
    {
        $password = $this->option('password') ?: $this->secret('Password');

        if (! $password) {
            $this->error('Password is required.');

            return self::FAILURE;
        }

        $user = User::updateOrCreate(
            ['email' => $this->argument('email')],
            [
                'name' => $this->option('name'),
                'password' => $password,
            ],
        );

        $this->info("Admin user is ready: {$user->email}");

        return self::SUCCESS;
    }
}
