@extends('layouts.admin')

@section('title', $flag->name)

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
        <h1 class="font-mono text-xl font-semibold tracking-tight">{{ $flag->name }}</h1>
    </div>

    {{--
        The update and delete forms are SIBLINGS, not nested.
        HTML does not allow nested <form> elements — browsers strip the inner
        one and mix its hidden inputs into the outer form, which causes the
        wrong HTTP method to be sent.  The Save button uses form="update-form"
        to stay associated with the update form even though it lives outside it.
    --}}

    <form id="update-form" method="POST" action="{{ route('admin.flags.update', $flag) }}">
        @csrf
        @method('PATCH')

        @include('admin.flags._form', [
            'rulesJson'   => old('attribute_rules', json_encode($flag->attribute_rules ?? [])),
            'pct'         => old('rollout_percentage', $flag->rollout_percentage),
            'startsAt'    => old('starts_at', $flag->starts_at?->format('Y-m-d\TH:i')),
            'endsAt'      => old('ends_at', $flag->ends_at?->format('Y-m-d\TH:i')),
            'description' => old('description', $flag->description),
            'enabled'     => old('enabled', $flag->enabled),
            'flagName'    => $flag->name,
        ])
    </form>

    {{-- Delete form: completely separate from the update form above --}}
    <form id="delete-form" method="POST" action="{{ route('admin.flags.destroy', $flag) }}">
        @csrf
        @method('DELETE')
    </form>

    <div class="mt-6 flex items-center justify-between">
        <button type="submit" form="delete-form"
                onclick="return confirm('Permanently delete {{ $flag->name }}? This cannot be undone.')"
                class="inline-flex items-center gap-1.5 rounded-lg border border-red-200 px-3.5 py-2 text-sm font-medium text-red-600 hover:bg-red-50 transition-colors">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            Delete flag
        </button>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.flags.index') }}"
               class="rounded-lg px-4 py-2 text-sm font-medium text-zinc-600 hover:bg-zinc-100 transition-colors">
                Cancel
            </a>
            <button type="submit" form="update-form"
                    class="rounded-lg bg-emerald-600 px-5 py-2 text-sm font-medium text-white shadow-sm hover:bg-emerald-700 transition-colors">
                Save changes
            </button>
        </div>
    </div>
@endsection
