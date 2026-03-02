<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Hash;

class AdminUser extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'password', 'role', 'last_login_at'];
    protected $hidden = ['password'];
    protected $casts = ['last_login_at' => 'datetime'];

    public function setPasswordAttribute(string $value): void
    {
        $this->attributes['password'] = Hash::make($value);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin';
    }
}
