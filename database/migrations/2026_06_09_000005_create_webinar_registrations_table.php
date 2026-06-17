<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('webinar_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('webinar_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lead_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('registered');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['webinar_id', 'lead_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webinar_registrations');
    }
};
