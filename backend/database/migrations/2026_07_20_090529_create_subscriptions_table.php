<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Domains\Subscription\Enums\SubscriptionStatus;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table): void {
            $table->id();

            $table->uuid('uuid')
                ->unique();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('subscription_category_id')
                ->constrained('subscription_categories')
                ->restrictOnDelete();

            $table->string('provider_name');

            $table->string('plan_name')
                ->nullable();

            $table->decimal('amount', 12, 2)
                ->nullable();

            $table->string('currency', 3)
                ->default('NGN');

            $table->timestamp('started_at')
                ->nullable();

            $table->timestamp('expires_at')
                ->nullable();

            $table->timestamp('renewal_at')
                ->nullable();

            $table->string('status')
                ->default(SubscriptionStatus::ACTIVE->value);

            $table->text('notes')
                ->nullable();

            $table->timestamps();

            $table->index([
                'user_id',
                'status',
            ]);

            $table->index([
                'user_id',
                'expires_at',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};