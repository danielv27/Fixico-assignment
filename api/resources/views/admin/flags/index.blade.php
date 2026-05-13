@extends('layouts.admin')

@section('title', 'Feature flags')

@section('content')
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-semibold tracking-tight">Feature flags</h1>
        <a href="{{ route('admin.flags.create') }}"
           class="rounded bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">
            New flag
        </a>
    </div>

    @if ($flags->isEmpty())
        <p class="mt-6 text-zinc-500">No flags yet.</p>
    @else
        <ul class="mt-6 divide-y divide-zinc-200 rounded border border-zinc-200 bg-white">
            @foreach ($flags as $flag)
                <li class="flex items-center gap-4 px-4 py-3">
                    <span class="h-2.5 w-2.5 flex-shrink-0 rounded-full {{ $flag->enabled ? 'bg-emerald-500' : 'bg-zinc-300' }}"></span>
                    <div class="flex min-w-0 flex-1 flex-col">
                        <span class="truncate font-mono text-sm font-medium">{{ $flag->name }}</span>
                        @if ($flag->description)
                            <span class="truncate text-xs text-zinc-500">{{ $flag->description }}</span>
                        @endif
                    </div>
                    <a href="{{ route('admin.flags.edit', $flag) }}"
                       class="text-sm text-zinc-500 hover:text-zinc-900">
                        Edit →
                    </a>
                </li>
            @endforeach
        </ul>
    @endif
@endsection
