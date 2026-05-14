<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreFlagRequest;
use App\Http\Requests\Admin\UpdateFlagRequest;
use App\Models\FeatureFlag;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FlagWebController extends Controller
{
    public function index(): View
    {
        $flags = FeatureFlag::query()->orderBy('name')->get();

        return view('admin.flags.index', compact('flags'));
    }

    public function create(): View
    {
        return view('admin.flags.create');
    }

    public function store(StoreFlagRequest $request): RedirectResponse
    {
        FeatureFlag::create($request->validated());

        return redirect()->route('admin.flags.index')
            ->with('success', 'Flag created.');
    }

    public function edit(FeatureFlag $flag): View
    {
        return view('admin.flags.edit', compact('flag'));
    }

    public function update(UpdateFlagRequest $request, FeatureFlag $flag): RedirectResponse
    {
        $flag->update($request->validated());

        return redirect()->route('admin.flags.index')
            ->with('success', "'{$flag->name}' saved.");
    }

    public function destroy(FeatureFlag $flag): RedirectResponse
    {
        $flag->delete();

        return redirect()->route('admin.flags.index')
            ->with('success', 'Flag deleted.');
    }
}
