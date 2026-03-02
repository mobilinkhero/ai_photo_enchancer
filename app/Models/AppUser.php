<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AppUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'uid',
        'name',
        'email',
        'platform',
        'subscription',
        'credits',
        'photos_enhanced',
        'is_banned',
        'subscription_expires_at'
    ];

    protected $casts = [
        'is_banned' => 'boolean',
        'subscription_expires_at' => 'datetime',
    ];

    public function photos()
    {
        return $this->hasMany(Photo::class, 'user_uid', 'uid');
    }

    public function isPro(): bool
    {
        return in_array($this->subscription, ['pro', 'premium']);
    }
}
