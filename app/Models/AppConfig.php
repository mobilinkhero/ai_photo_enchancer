<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;

class AppConfig extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value', 'group', 'type', 'label', 'description'];

    /**
     * Get a config value by key, with optional default.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $config = static::where('key', $key)->first();
        if (!$config)
            return $default;

        return match ($config->type) {
            'boolean' => filter_var($config->value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $config->value,
            'json' => json_decode($config->value, true),
            default => $config->value,
        };
    }

    /**
     * Set a config value by key.
     */
    public static function set(string $key, mixed $value): void
    {
        $val = is_array($value) ? json_encode($value) : (string) $value;
        static::updateOrCreate(['key' => $key], ['value' => $val]);
    }

    /**
     * Get all configs grouped.
     */
    public static function getGroup(string $group): array
    {
        return static::where('group', $group)->get()->keyBy('key')->toArray();
    }
}
