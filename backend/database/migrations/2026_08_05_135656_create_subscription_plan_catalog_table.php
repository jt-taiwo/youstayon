<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_plan_catalog', function (Blueprint $table): void {
            $table->id();

            $table->uuid('uuid')->unique();

            $table->foreignId('subscription_category_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('provider_name');

            $table->string('plan_name');

            $table->decimal('amount', 12, 2);

            $table->decimal('usage_limit', 14, 4)
                ->nullable();

            $table->string('usage_unit', 20)
                ->nullable();

            $table->string('currency', 10)
                ->default('NGN');

            $table->boolean('is_active')
                ->default(true);

            $table->timestamps();

            $table->index(
                ['subscription_category_id', 'provider_name'],
                'spc_cat_provider_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_plan_catalog');
    }
};
