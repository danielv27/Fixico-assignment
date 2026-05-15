<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreFeatureFlagRequest;
use App\Http\Requests\Admin\UpdateFeatureFlagRequest;
use App\Models\FeatureFlag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class FeatureFlagWebController extends Controller
{
    public function index(): View
    {
        $flags = FeatureFlag::query()
            ->orderBy('name')
            ->get();

        return view('admin.feature_flags.index', [
            'flags' => $flags,
            'stats' => $this->stats($flags),
        ]);
    }

    public function create(): View
    {
        return view('admin.feature_flags.create');
    }

    public function store(StoreFeatureFlagRequest $request): RedirectResponse
    {
        FeatureFlag::create($request->validated());

        return redirect()->route('admin.feature_flags.index')
            ->with('success', 'Feature flag created.');
    }

    public function edit(FeatureFlag $flag): View
    {
        return view('admin.feature_flags.edit', compact('flag'));
    }

    public function update(UpdateFeatureFlagRequest $request, FeatureFlag $flag): RedirectResponse
    {
        $flag->update($request->validated());

        return redirect()->route('admin.feature_flags.index')
            ->with('success', "'{$flag->name}' saved.");
    }

    public function destroy(FeatureFlag $flag): RedirectResponse
    {
        $flag->delete();

        return redirect()->route('admin.feature_flags.index')
            ->with('success', 'Feature flag deleted.');
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
