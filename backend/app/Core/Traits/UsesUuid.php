<?php

declare(strict_types=1);

namespace App\Core\Traits;

use Illuminate\Support\Str;

trait UsesUuid
{
    protected static function bootUsesUuid(): void
    {
        static::creating(function ($model): void {

            if (
                empty($model->uuid)
                && $model->isFillable('uuid')
            ) {
                $model->uuid = (string) Str::uuid();
            }

        });
    }
}