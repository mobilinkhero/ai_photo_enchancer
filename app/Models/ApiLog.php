<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiLog extends Model
{
    protected $fillable = [
        'request_id',
        'client_ip',
        'client_endpoint',
        'client_method',
        'client_headers',
        'client_payload',
        'ai_provider',
        'ai_endpoint',
        'ai_request_payload',
        'ai_request_size_bytes',
        'ai_response_status',
        'ai_response_body',
        'ai_response_time_ms',
        'ai_model',
        'ai_output_url',
        'status',
        'error_message',
        'total_time_ms',
        'user_uid',
        'photo_id',
    ];

    protected $casts = [
        'client_headers' => 'array',
        'client_payload' => 'array',
        'ai_request_payload' => 'array',
        'ai_response_body' => 'array',
    ];

    public function photo()
    {
        return $this->belongsTo(Photo::class);
    }

    public function user()
    {
        return $this->belongsTo(AppUser::class, 'user_uid', 'uid');
    }

    public function isSuccess(): bool
    {
        return $this->status === 'success';
    }
    public function isError(): bool
    {
        return $this->status === 'error';
    }
    public function isTimeout(): bool
    {
        return $this->status === 'timeout';
    }

    public function getFormattedTimeAttribute(): string
    {
        if (!$this->total_time_ms)
            return '—';
        if ($this->total_time_ms >= 1000) {
            return round($this->total_time_ms / 1000, 2) . 's';
        }
        return round($this->total_time_ms) . 'ms';
    }

    public function getAiResponseTimeAttribute(): string
    {
        if (!$this->ai_response_time_ms)
            return '—';
        if ($this->ai_response_time_ms >= 1000) {
            return round($this->ai_response_time_ms / 1000, 2) . 's';
        }
        return round($this->ai_response_time_ms) . 'ms';
    }
}
