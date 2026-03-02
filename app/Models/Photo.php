<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Photo extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_uid',
        'original_path',
        'enhanced_path',
        'provider',
        'model',
        'status',
        'error_message',
        'processing_time'
    ];


    public function isCompleted(): bool
    {
        return $this->status === 'done';
    }
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}
