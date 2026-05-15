@extends('layouts.admin')

@section('title', 'Admin overview')
@section('main_class', 'max-w-[1320px]')

@section('content')
    @php
        $now = now();
    @endphp

    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-emerald-700">Admin console</p>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-zinc-950">Feature flag operations</h1>
            <p class="mt-1 text-sm text-zinc-500">Rollout state, targeting, schedules, and change activity.</p>
        </div>
        <div class="flex items-center gap-1 rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm shadow-sm">
            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
            <span class="font-semibold tabular-nums text-zinc-900">{{ $stats['active'] }}</span>
            <span class="text-zinc-500">of</span>
            <span class="font-semibold tabular-nums text-zinc-900">{{ $stats['total'] }}</span>
            <span class="text-zinc-500">flags active</span>
        </div>
    </div>

    <section class="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-7">
        @foreach ([
            ['label' => 'Total', 'value' => $stats['total'], 'caption' => 'configured flags', 'class' => 'text-zinc-950'],
            ['label' => 'Active', 'value' => $stats['active'], 'caption' => 'serving now', 'class' => 'text-emerald-700'],
            ['label' => 'Disabled', 'value' => $stats['disabled'], 'caption' => 'off in clients', 'class' => 'text-zinc-600'],
            ['label' => 'Targeted', 'value' => $stats['targeted'], 'caption' => 'audience rules', 'class' => 'text-violet-700'],
            ['label' => 'Limited', 'value' => $stats['limited'], 'caption' => 'percentage gates', 'class' => 'text-amber-700'],
            ['label' => 'Scheduled', 'value' => $stats['scheduled'], 'caption' => 'pending start', 'class' => 'text-blue-700'],
            ['label' => 'Expired', 'value' => $stats['expired'], 'caption' => 'past window', 'class' => 'text-red-600'],
        ] as $stat)
            <div class="rounded-lg border border-zinc-200 bg-white px-4 py-3 shadow-sm">
                <div class="flex items-baseline justify-between gap-3">
                    <p class="text-[11px] font-semibold uppercase tracking-wide text-zinc-400">{{ $stat['label'] }}</p>
                    <p class="text-2xl font-bold tracking-tight tabular-nums {{ $stat['class'] }}">{{ $stat['value'] }}</p>
                </div>
                <p class="mt-1 text-xs text-zinc-500">{{ $stat['caption'] }}</p>
            </div>
        @endforeach
    </section>

    <section class="mt-4 overflow-hidden rounded-lg border border-zinc-200 bg-white shadow-sm">
        <div class="flex items-center justify-between gap-4 border-b border-zinc-100 px-5 py-4">
            <div>
                <h2 class="text-sm font-semibold text-zinc-950">Feature flag inventory</h2>
                <p class="mt-0.5 text-xs text-zinc-500">Sorted by flag key with rollout and schedule state.</p>
            </div>
            <span class="hidden rounded-md bg-zinc-50 px-2 py-1 text-xs font-semibold tabular-nums text-zinc-500 ring-1 ring-inset ring-zinc-200 sm:inline-flex">
                {{ $flags->count() }} rows
            </span>
        </div>

        @if ($flags->isEmpty())
            <div class="px-5 py-10 text-sm text-zinc-400">No feature flags yet.</div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-[1160px] w-full text-sm">
                    <thead>
                        <tr class="border-b border-zinc-100 bg-zinc-50/80">
                            <th class="w-[28%] px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-zinc-500">Flag</th>
                            <th class="w-[12%] px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-zinc-500">Status</th>
                            <th class="w-[18%] px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-zinc-500">Targeting</th>
                            <th class="w-[15%] px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-zinc-500">Exposure</th>
                            <th class="w-[17%] px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-zinc-500">Schedule</th>
                            <th class="w-[10%] px-4 py-3 text-right text-[11px] font-semibold uppercase tracking-wide text-zinc-500">Changed</th>
                            <th class="px-5 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @foreach ($flags as $flag)
                            @php
                                if (! $flag->enabled) {
                                    $stateLabel = 'Disabled';
                                    $stateClass = 'bg-zinc-100 text-zinc-500 ring-zinc-200';
                                    $dotClass = 'bg-zinc-400';
                                } elseif ($flag->starts_at && $now->isBefore($flag->starts_at)) {
                                    $stateLabel = 'Scheduled';
                                    $stateClass = 'bg-blue-50 text-blue-700 ring-blue-200';
                                    $dotClass = 'bg-blue-400';
                                } elseif ($flag->ends_at && $now->isAfter($flag->ends_at)) {
                                    $stateLabel = 'Expired';
                                    $stateClass = 'bg-red-50 text-red-600 ring-red-200';
                                    $dotClass = 'bg-red-400';
                                } else {
                                    $stateLabel = 'Active';
                                    $stateClass = 'bg-emerald-50 text-emerald-700 ring-emerald-200';
                                    $dotClass = 'bg-emerald-500';
                                }

                                $rules = $flag->attribute_rules ?? [];
                                $visibleRules = array_slice($rules, 0, 2);
                                $extraRules = count($rules) - count($visibleRules);
                            @endphp
                            <tr class="transition-colors hover:bg-zinc-50/70">
                                <td class="px-5 py-4">
                                    <a href="{{ route('admin.feature_flags.edit', $flag) }}"
                                       class="block truncate font-mono text-sm font-semibold text-zinc-950 hover:text-emerald-700">
                                        {{ $flag->name }}
                                    </a>
                                    @if ($flag->description)
                                        <p class="mt-0.5 max-w-md truncate text-xs text-zinc-400">{{ $flag->description }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-md px-2 py-1 text-xs font-semibold ring-1 ring-inset {{ $stateClass }}">
                                        <span class="h-1.5 w-1.5 rounded-full {{ $dotClass }}"></span>
                                        {{ $stateLabel }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    @if (empty($rules))
                                        <span class="text-xs text-zinc-400">All users</span>
                                    @else
                                        <div class="flex max-w-[240px] flex-wrap gap-1">
                                            @foreach ($visibleRules as $rule)
                                                <span class="rounded-md bg-violet-50 px-2 py-0.5 text-xs font-medium text-violet-700 ring-1 ring-inset ring-violet-200">
                                                    {{ $rule['attribute'] }}: {{ implode(', ', $rule['values']) }}
                                                </span>
                                            @endforeach
                                            @if ($extraRules > 0)
                                                <span class="rounded-md bg-zinc-100 px-2 py-0.5 text-xs font-medium text-zinc-500 ring-1 ring-inset ring-zinc-200">
                                                    +{{ $extraRules }}
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-4">
                                    @if ($flag->rollout_percentage !== null)
                                        <div class="flex min-w-32 items-center gap-2">
                                            <div class="h-1.5 w-24 overflow-hidden rounded-full bg-zinc-200">
                                                <div class="h-full rounded-full bg-emerald-500" style="width: {{ $flag->rollout_percentage }}%"></div>
                                            </div>
                                            <span class="w-9 text-xs font-semibold tabular-nums text-zinc-700">{{ $flag->rollout_percentage }}%</span>
                                        </div>
                                    @else
                                        <div class="text-xs">
                                            <span class="font-semibold tabular-nums text-zinc-700">100%</span>
                                            <span class="ml-1 text-zinc-400">matched</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-4">
                                    @if ($flag->starts_at || $flag->ends_at)
                                        <div class="space-y-0.5 whitespace-nowrap text-xs text-zinc-500">
                                            @if ($flag->starts_at)
                                                <p>starts {{ $flag->starts_at->format('M j, H:i') }}</p>
                                            @endif
                                            @if ($flag->ends_at)
                                                <p>ends {{ $flag->ends_at->format('M j, H:i') }}</p>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-xs text-zinc-400">No schedule</span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-4 py-4 text-right text-xs text-zinc-400">
                                    {{ $flag->updated_at?->diffForHumans() ?? 'never' }}
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <a href="{{ route('admin.feature_flags.edit', $flag) }}"
                                       title="Edit {{ $flag->name }}"
                                       class="inline-flex h-8 w-8 items-center justify-center rounded-md border border-zinc-200 bg-white text-zinc-500 shadow-sm transition-colors hover:border-zinc-300 hover:bg-zinc-50 hover:text-zinc-900">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endsection
