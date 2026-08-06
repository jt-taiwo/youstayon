<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table): void {
            $table->id();

            $table->uuid('uuid')->unique();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('service_type');

            $table->string('provider');

            $table->string('provider_reference')
                ->nullable();

            $table->string('payment_method');

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

            $table->json('request_payload');

            $table->json('response_payload')
                ->nullable();

            $table->timestamp('completed_at')
                ->nullable();

            $table->timestamps();

            $table->index([
                'user_id',
                'status',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
