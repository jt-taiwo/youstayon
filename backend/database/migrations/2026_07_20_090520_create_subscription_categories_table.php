<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_categories', function (Blueprint $table): void {
            $table->id();

            $table->uuid('uuid')
                ->unique();

            $table->string('name')
                ->unique();

            $table->string('slug')
                ->unique();

            $table->text('description')
                ->nullable();

            $table->boolean('is_active')
                ->default(true);

            $table->timestamps();

            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_categories');
    }
};