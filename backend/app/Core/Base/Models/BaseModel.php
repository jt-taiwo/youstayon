<?php

declare(strict_types=1);

namespace App\Core\Base\Models;

use App\Core\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

abstract class BaseModel extends Model
{
    use UsesUuid;
    use SoftDeletes;

    /**
     * Mass assignment.
     *
     * Every domain model should explicitly override this.
     *
     * @var array<int,string>
     */
    protected $fillable = [];

    /**
     * Hidden attributes.
     *
     * @var array<int,string>
     */
    protected $hidden = [];

    /**
     * Common casts.
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Standard ordering.
     */
    public function scopeLatestFirst($query)
    {
        return $query->orderByDesc('created_at');
    }

    /**
     * Standard oldest ordering.
     */
    public function scopeOldestFirst($query)
    {
        return $query->orderBy('created_at');
    }
}