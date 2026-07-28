<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_renewal_histories', function (Blueprint $table): void {
            $table->id();

            $table->uuid('uuid')->unique();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('previous_subscription_id')
                ->constrained('subscriptions')
                ->cascadeOnDelete();

            $table->foreignId('new_subscription_id')
                ->constrained('subscriptions')
                ->cascadeOnDelete();

            $table->date('previous_start_date');
            $table->date('previous_expiry_date');

            $table->date('new_start_date');
            $table->date('new_expiry_date');

            $table->string('reason')->default('manual');

            $table->json('metadata')->nullable();

            $table->timestamp('renewed_at');

            $table->timestamps();

            $table->index('user_id');
            $table->index('previous_subscription_id');
            $table->index('new_subscription_id');
            $table->index('renewed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_renewal_histories');
    }
};