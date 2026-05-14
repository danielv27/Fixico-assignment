<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeatureFlag;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $flags = FeatureFlag::query()
            ->orderBy('name')
            ->get();

        return view('admin.dashboard', [
            'flags' => $flags,
            'stats' => $this->stats($flags),
            'limitedRollouts' => $flags
                ->filter(fn (FeatureFlag $flag): bool => $flag->enabled && $flag->rollout_percentage !== null)
                ->sortBy('rollout_percentage')
                ->values(),
            'reviewFlags' => $flags
                ->filter(fn (FeatureFlag $flag): bool => ! $flag->enabled
                    || ($flag->starts_at !== null && now()->isBefore($flag->starts_at))
                    || ($flag->ends_at !== null && now()->isAfter($flag->ends_at)))
                ->take(5)
                ->values(),
        ]);
    }

    /**
     * @param  Collection<int, FeatureFlag>  $flags
     * @return array<string, int>
     */
    private function stats(Collection $flags): array
    {
        $now = now();

        return [
            'total' => $flags->count(),
            'active' => $flags
                ->filter(fn (FeatureFlag $flag): bool => $flag->enabled
                    && ($flag->starts_at === null || $now->isAfter($flag->starts_at))
                    && ($flag->ends_at === null || $now->isBefore($flag->ends_at)))
                ->count(),
            'disabled' => $flags->where('enabled', false)->count(),
            'targeted' => $flags
                ->filter(fn (FeatureFlag $flag): bool => ! empty($flag->attribute_rules ?? []))
                ->count(),
            'limited' => $flags
                ->filter(fn (FeatureFlag $flag): bool => $flag->rollout_percentage !== null)
                ->count(),
            'scheduled' => $flags
                ->filter(fn (FeatureFlag $flag): bool => $flag->enabled
                    && $flag->starts_at !== null
                    && $now->isBefore($flag->starts_at))
                ->count(),
            'expired' => $flags
                ->filter(fn (FeatureFlag $flag): bool => $flag->ends_at !== null && $now->isAfter($flag->ends_at))
                ->count(),
        ];
    }
}
