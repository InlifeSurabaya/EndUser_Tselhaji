<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserPreference extends Model
{
    use SoftDeletes;

    protected $fillable = ['user_id', 'planned_budget', 'planned_duration', 'planned_quota'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
