@extends('layouts.admin')

@section('title', 'Feature flags')

@section('content')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight">Feature flags</h1>
            <p class="mt-1 text-sm text-zinc-500">Manage rollouts, targeting, and schedules.</p>
        </div>
        <a href="{{ route('admin.flags.create') }}"
           class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-emerald-700 transition-colors">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            New flag
        </a>
    </div>

    @if ($flags->isEmpty())
        <div class="mt-10 rounded-xl border border-dashed border-zinc-300 bg-white px-6 py-16 text-center">
            <svg class="mx-auto h-10 w-10 text-zinc-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/>
            </svg>
            <p class="mt-3 text-sm font-medium text-zinc-700">No feature flags yet</p>
            <p class="mt-1 text-sm text-zinc-500">Create your first flag to start controlling feature rollouts.</p>
            <a href="{{ route('admin.flags.create') }}"
               class="mt-4 inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700 transition-colors">
                Create flag
            </a>
        </div>
    @else
        <div class="mt-6 overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-zinc-100 bg-zinc-50">
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-zinc-500">Flag</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-zinc-500">Targeting</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-zinc-500">Rollout</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-zinc-500">Status</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100">
                    @foreach ($flags as $flag)
                        @php
                            $now = now();
                            if (!$flag->enabled) {
                                $statusLabel = 'Disabled';
                                $statusClass = 'bg-zinc-100 text-zinc-500';
                                $dotClass = 'bg-zinc-400';
                            } elseif ($flag->starts_at && $now->isBefore($flag->starts_at)) {
                                $statusLabel = 'Scheduled';
                                $statusClass = 'bg-blue-50 text-blue-700';
                                $dotClass = 'bg-blue-400';
                            } elseif ($flag->ends_at && $now->isAfter($flag->ends_at)) {
                                $statusLabel = 'Expired';
                                $statusClass = 'bg-red-50 text-red-600';
                                $dotClass = 'bg-red-400';
                            } else {
                                $statusLabel = 'Active';
                                $statusClass = 'bg-emerald-50 text-emerald-700';
                                $dotClass = 'bg-emerald-500';
                            }
                        @endphp
                        <tr class="hover:bg-zinc-50 transition-colors">
                            <td class="px-4 py-3.5">
                                <div class="font-mono text-sm font-medium text-zinc-900">{{ $flag->name }}</div>
                                @if ($flag->description)
                                    <div class="mt-0.5 text-xs text-zinc-500 max-w-xs truncate">{{ $flag->description }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3.5">
                                @if (!empty($flag->attribute_rules))
                                    <div class="flex flex-wrap gap-1">
                                        @foreach ($flag->attribute_rules as $rule)
                                            <span class="inline-flex items-center rounded-md bg-violet-50 px-2 py-0.5 text-xs font-medium text-violet-700 ring-1 ring-inset ring-violet-200">
                                                {{ $rule['attribute'] }}: {{ implode(', ', $rule['values']) }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-xs text-zinc-400">All users</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5">
                                @if ($flag->rollout_percentage !== null)
                                    <div class="flex items-center gap-2">
                                        <div class="w-20 h-1.5 rounded-full bg-zinc-200 overflow-hidden">
                                            <div class="h-full rounded-full bg-emerald-500" style="width: {{ $flag->rollout_percentage }}%"></div>
                                        </div>
                                        <span class="text-xs font-medium text-zinc-700">{{ $flag->rollout_percentage }}%</span>
                                    </div>
                                @else
                                    <span class="text-xs text-zinc-400">100%</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="inline-flex items-center gap-1.5 rounded-md px-2 py-0.5 text-xs font-medium ring-1 ring-inset {{ $statusClass }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $dotClass }}"></span>
                                    {{ $statusLabel }}
                                </span>
                                @if ($flag->starts_at && $now->isBefore($flag->starts_at))
                                    <div class="mt-1 text-xs text-zinc-400">from {{ $flag->starts_at->format('M j, H:i') }}</div>
                                @elseif ($flag->ends_at && $now->isAfter($flag->ends_at))
                                    <div class="mt-1 text-xs text-zinc-400">ended {{ $flag->ends_at->format('M j, H:i') }}</div>
                                @elseif ($flag->ends_at)
                                    <div class="mt-1 text-xs text-zinc-400">until {{ $flag->ends_at->format('M j, H:i') }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-right">
                                <a href="{{ route('admin.flags.edit', $flag) }}"
                                   class="inline-flex items-center gap-1 text-xs font-medium text-zinc-500 hover:text-zinc-900 transition-colors">
                                    Edit
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection
