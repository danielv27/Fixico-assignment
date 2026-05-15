<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreFeatureFlagRequest;
use App\Http\Requests\Admin\UpdateFeatureFlagRequest;
use App\Models\FeatureFlag;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FeatureFlagWebController extends Controller
{
    public function create(): View
    {
        return view('admin.feature_flags.create');
    }

    public function store(StoreFeatureFlagRequest $request): RedirectResponse
    {
        FeatureFlag::create($request->validated());

        return redirect()->route('admin.dashboard')
            ->with('success', 'Feature flag created.');
    }

    public function edit(FeatureFlag $flag): View
    {
        return view('admin.feature_flags.edit', compact('flag'));
    }

    public function update(UpdateFeatureFlagRequest $request, FeatureFlag $flag): RedirectResponse
    {
        $flag->update($request->validated());

        return redirect()->route('admin.dashboard')
            ->with('success', "'{$flag->name}' saved.");
    }

    public function destroy(FeatureFlag $flag): RedirectResponse
    {
        $flag->delete();

        return redirect()->route('admin.dashboard')
            ->with('success', 'Feature flag deleted.');
    }
}
