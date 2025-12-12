<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserProfile extends Model
{
    /** @use HasFactory<\Database\Factories\UserProfileFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = ['user_id', 'fullname', 'gender', 'birth_date', 'phone', 'address', 'avatar'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
