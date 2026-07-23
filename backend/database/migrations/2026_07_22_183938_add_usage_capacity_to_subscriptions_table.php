<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscriptions', function (
            Blueprint $table
        ): void {
            $table->decimal(
                'usage_limit',
                14,
                4
            )
                ->nullable()
                ->after('amount');

            $table->string(
                'usage_unit',
                20
            )
                ->nullable()
                ->after('usage_limit');
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (
            Blueprint $table
        ): void {
            $table->dropColumn([
                'usage_limit',
                'usage_unit',
            ]);
        });
    }
};