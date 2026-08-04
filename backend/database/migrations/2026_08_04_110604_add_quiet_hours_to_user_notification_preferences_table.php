<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_notification_preferences', function (Blueprint $table): void {
            $table->boolean('quiet_hours_enabled')
                ->default(false)
                ->after('radar_enabled');

            $table->time('quiet_hours_start')
                ->nullable()
                ->after('quiet_hours_enabled');

            $table->time('quiet_hours_end')
                ->nullable()
                ->after('quiet_hours_start');
        });
    }

    public function down(): void
    {
        Schema::table('user_notification_preferences', function (Blueprint $table): void {
            $table->dropColumn([
                'quiet_hours_enabled',
                'quiet_hours_start',
                'quiet_hours_end',
            ]);
        });
    }
};
