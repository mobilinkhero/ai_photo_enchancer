<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppFeature extends Model
{
    protected $fillable = [
        'feature_id',
        'title',
        'description',
        'icon',
        'color',
        'is_premium',
        'enabled',
        'coins',
        'benefits',
        'before_url',
        'after_url',
        'sort_order',
    ];

    protected $casts = [
        'benefits' => 'array',
        'is_premium' => 'boolean',
        'enabled' => 'boolean',
    ];

    /**
     * Return the feature in the exact shape the Flutter app expects.
     */
    public function toAppArray(): array
    {
        return [
            'id' => $this->feature_id,
            'title' => $this->title,
            'description' => $this->description ?? '',
            'icon' => $this->icon,
            'color' => $this->color,
            'isPremium' => $this->is_premium,
            'enabled' => $this->enabled,
            'coins' => $this->coins,
            'benefits' => $this->benefits ?? [],
            'beforeUrl' => $this->before_url ?? '',
            'afterUrl' => $this->after_url ?? '',
        ];
    }
}
