<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'subscription_usage_records',
            function (Blueprint $table): void {
                $table->id();

                $table->foreignId('subscription_id')
                    ->constrained('subscriptions')
                    ->cascadeOnDelete();

                $table->decimal(
                    'quantity',
                    14,
                    4
                );

                $table->string(
                    'unit',
                    20
                );

                $table->string(
                    'source',
                    50
                )->default('manual');

                $table->timestamp('recorded_at');

                $table->timestamps();

                $table->index([
                    'subscription_id',
                    'recorded_at',
                ]);
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'subscription_usage_records'
        );
    }
};