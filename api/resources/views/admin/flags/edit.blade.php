@extends('layouts.admin')

@section('title', $flag->name)

@section('content')
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.flags.index') }}" class="text-sm text-zinc-500 hover:text-zinc-900">← Flags</a>
        <h1 class="truncate font-mono text-3xl font-semibold tracking-tight">{{ $flag->name }}</h1>
    </div>

    <form method="POST" action="{{ route('admin.flags.update', $flag) }}" class="mt-6 flex flex-col gap-4">
        @csrf
        @method('PATCH')

        <div class="flex flex-col gap-1">
            <label for="description" class="text-sm font-medium">Description</label>
            <textarea id="description" name="description" rows="2"
                      class="rounded border border-zinc-300 bg-white px-3 py-2 text-sm">{{ old('description', $flag->description) }}</textarea>
            @error('description')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <input type="hidden" name="enabled" value="0">
        <label class="flex cursor-pointer items-center gap-3">
            <input type="checkbox" name="enabled" value="1"
                   {{ old('enabled', $flag->enabled) ? 'checked' : '' }}
                   class="h-4 w-4 rounded border-zinc-300">
            <span class="text-sm font-medium">Enabled</span>
        </label>

        <div>
            <button type="submit"
                    class="rounded bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">
                Save changes
            </button>
        </div>
    </form>

    <hr class="my-8 border-zinc-200">

    <form method="POST" action="{{ route('admin.flags.destroy', $flag) }}">
        @csrf
        @method('DELETE')
        <button type="submit"
                onclick="return confirm('Delete {{ $flag->name }}?')"
                class="rounded border border-red-300 px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50">
            Delete flag
        </button>
    </form>
@endsection
