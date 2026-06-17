<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('bale_user_id');
            $table->string('chat_id')->nullable();
            $table->string('intent');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('username')->nullable();
            $table->text('last_message')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['bale_user_id', 'intent']);
            $table->index('intent');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
