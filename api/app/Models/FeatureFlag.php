<?php

namespace App\Models;

use App\FeatureFlags\FlagObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy(FlagObserver::class)]
class FeatureFlag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'enabled',
        'attribute_rules',
        'rollout_percentage',
        'starts_at',
        'ends_at',
    ];

    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
            'attribute_rules' => 'array',
            'rollout_percentage' => 'integer',
            'starts_at' => 'immutable_datetime',
            'ends_at' => 'immutable_datetime',
        ];
    }
}
