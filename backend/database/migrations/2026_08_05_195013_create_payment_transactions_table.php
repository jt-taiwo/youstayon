<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_transactions', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('provider');

            $table->string('provider_reference')
                ->nullable()
                ->index();

            $table->string('reference')
                ->unique();

            $table->decimal('amount', 14, 2);

            $table->string('currency', 3)
                ->default('NGN');

            $table->enum('status', [
                'pending',
                'processing',
                'successful',
                'failed',
                'cancelled',
            ])->default('pending');

            $table->json('meta')->nullable();

            $table->timestamp('paid_at')->nullable();

            $table->timestamps();

            $table->index([
                'provider',
                'status',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
