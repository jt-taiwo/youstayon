<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchases', function (Blueprint $table): void {
            $table->string('attribution_source')
                ->nullable()
                ->after('payment_method');

            $table->string('attribution_campaign')
                ->nullable()
                ->after('attribution_source');
        });
    }

    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table): void {
            $table->dropColumn([
                'attribution_source',
                'attribution_campaign',
            ]);
        });
    }
};
