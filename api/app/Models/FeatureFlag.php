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
    ];

    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
        ];
    }
}
