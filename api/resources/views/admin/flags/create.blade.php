@extends('layouts.admin')

@section('title', 'New flag')

@section('content')
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.flags.index') }}" class="text-sm text-zinc-500 hover:text-zinc-900">← Flags</a>
        <h1 class="text-3xl font-semibold tracking-tight">New flag</h1>
    </div>

    <form method="POST" action="{{ route('admin.flags.store') }}" class="mt-6 flex flex-col gap-4">
        @csrf

        <div class="flex flex-col gap-1">
            <label for="name" class="text-sm font-medium">
                Name
                <span class="font-normal text-zinc-500">(lowercase · dots · hyphens — e.g. reports.bulk_delete)</span>
            </label>
            <input id="name" name="name" type="text" value="{{ old('name') }}"
                   placeholder="reports.my_feature"
                   class="rounded border border-zinc-300 bg-white px-3 py-2 font-mono text-sm @error('name') border-red-400 @enderror">
            @error('name')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex flex-col gap-1">
            <label for="description" class="text-sm font-medium">Description</label>
            <textarea id="description" name="description" rows="2"
                      class="rounded border border-zinc-300 bg-white px-3 py-2 text-sm">{{ old('description') }}</textarea>
            @error('description')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <input type="hidden" name="enabled" value="0">
        <label class="flex cursor-pointer items-center gap-3">
            <input type="checkbox" name="enabled" value="1" {{ old('enabled') ? 'checked' : '' }} class="h-4 w-4 rounded border-zinc-300">
            <span class="text-sm font-medium">Enabled</span>
        </label>

        <div class="flex flex-col gap-1">
            <label for="attribute_rules" class="text-sm font-medium">
                Attribute rules
                <span class="font-normal text-zinc-500">(JSON array — allowed attributes: country, role)</span>
            </label>
            <textarea id="attribute_rules" name="attribute_rules" rows="3"
                      placeholder='[{"attribute":"role","values":["admin"]}]'
                      class="rounded border border-zinc-300 bg-white px-3 py-2 font-mono text-sm @error('attribute_rules.*') border-red-400 @enderror">{{ old('attribute_rules', '[]') }}</textarea>
            @error('attribute_rules.*')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex flex-col gap-1">
            <label for="rollout_percentage" class="text-sm font-medium">
                Rollout percentage
                <span class="font-normal text-zinc-500">(0–100 · blank = 100 %)</span>
            </label>
            <input id="rollout_percentage" name="rollout_percentage" type="number" min="0" max="100"
                   value="{{ old('rollout_percentage') }}"
                   class="w-32 rounded border border-zinc-300 bg-white px-3 py-2 text-sm @error('rollout_percentage') border-red-400 @enderror">
            @error('rollout_percentage')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div class="flex flex-col gap-1">
                <label for="starts_at" class="text-sm font-medium">Activates at</label>
                <input id="starts_at" name="starts_at" type="datetime-local" value="{{ old('starts_at') }}"
                       class="rounded border border-zinc-300 bg-white px-3 py-2 text-sm @error('starts_at') border-red-400 @enderror">
                @error('starts_at')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex flex-col gap-1">
                <label for="ends_at" class="text-sm font-medium">Expires at</label>
                <input id="ends_at" name="ends_at" type="datetime-local" value="{{ old('ends_at') }}"
                       class="rounded border border-zinc-300 bg-white px-3 py-2 text-sm @error('ends_at') border-red-400 @enderror">
                @error('ends_at')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div>
            <button type="submit" class="rounded bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">
                Create flag
            </button>
        </div>
    </form>
@endsection
