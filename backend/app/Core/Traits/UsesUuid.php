<?php

declare(strict_types=1);

namespace App\Core\Traits;

use Illuminate\Support\Str;

trait UsesUuid
{
    /**
     * UUID primary key.
     */
    public $incrementing = false;

    /**
     * UUID key type.
     */
    protected $keyType = 'string';

    /**
     * Automatically generate UUID.
     */
    protected static function bootUsesUuid(): void
    {
        static::creating(function ($model): void {

            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }

        });
    }
}