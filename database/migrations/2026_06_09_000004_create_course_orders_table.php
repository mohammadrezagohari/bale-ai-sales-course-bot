<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lead_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('amount')->default(0);
            $table->string('status')->default('pending');
            $table->string('payment_reference')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['course_id', 'status']);
            $table->index(['lead_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_orders');
    }
};
