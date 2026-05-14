@extends('layouts.admin')

@section('title', 'New flag')

@section('content')
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.flags.index') }}"
           class="inline-flex items-center gap-1 text-sm text-zinc-500 hover:text-zinc-800 transition-colors">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Flags
        </a>
        <span class="text-zinc-300">/</span>
        <h1 class="text-xl font-semibold tracking-tight">New flag</h1>
    </div>

    <form method="POST" action="{{ route('admin.flags.store') }}">
        @csrf

        {{-- Name (create-only) --}}
        <div class="mb-6 rounded-xl border border-zinc-200 bg-white p-5 shadow-sm">
            <h2 class="text-sm font-semibold text-zinc-800">Flag name</h2>
            <p class="mt-0.5 text-xs text-zinc-500">Lowercase, dots, underscores, hyphens. Cannot be changed after creation.</p>
            <div class="mt-4">
                <input id="name" name="name" type="text" value="{{ old('name') }}"
                       placeholder="reports.my_feature"
                       autofocus
                       class="w-full rounded-lg border border-zinc-300 px-3 py-2 font-mono text-sm text-zinc-900 placeholder-zinc-400 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 @error('name') border-red-400 @enderror">
                @error('name')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        @include('admin.flags._form', [
            'rulesJson'   => old('attribute_rules', '[]'),
            'pct'         => old('rollout_percentage'),
            'startsAt'    => old('starts_at', ''),
            'endsAt'      => old('ends_at', ''),
            'description' => old('description', ''),
            'enabled'     => old('enabled', false),
            'flagName'    => null,  // read from name input live in JS
        ])

        <div class="mt-6 flex items-center justify-end gap-3">
            <a href="{{ route('admin.flags.index') }}"
               class="rounded-lg px-4 py-2 text-sm font-medium text-zinc-600 hover:bg-zinc-100 transition-colors">
                Cancel
            </a>
            <button type="submit"
                    class="rounded-lg bg-emerald-600 px-5 py-2 text-sm font-medium text-white shadow-sm hover:bg-emerald-700 transition-colors">
                Create flag
            </button>
        </div>
    </form>
@endsection
