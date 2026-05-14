@extends('layouts.admin')

@section('title', 'Feature flags')

@section('content')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900">Feature flags</h1>
            <p class="mt-1 text-sm text-zinc-500">Control rollouts, targeting, and schedules.</p>
        </div>
        <a href="{{ route('admin.feature_flags.create') }}"
           class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 transition-colors">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            New flag
        </a>
    </div>

    @if ($flags->isEmpty())
        <div class="mt-10 rounded-xl border border-dashed border-zinc-300 bg-white px-6 py-16 text-center shadow-sm">
            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-zinc-100">
                <svg class="h-6 w-6 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/>
                </svg>
            </div>
            <p class="mt-3 text-sm font-semibold text-zinc-700">No feature flags yet</p>
            <p class="mt-1 text-sm text-zinc-500">Create your first flag to start controlling rollouts.</p>
            <a href="{{ route('admin.feature_flags.create') }}"
               class="mt-4 inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700 transition-colors">
                Create flag
            </a>
        </div>
    @else
        <div class="mt-6 overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-zinc-100 bg-zinc-50/80">
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">Flag</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">Targeting</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">Rollout</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">Status</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-zinc-500">On</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100">
                    @foreach ($flags as $flag)
                        @php
                            $now = now();
                            // Human-readable label — split on first dot
                            $nameParts = explode('.', $flag->name, 2);
                            $namespace = count($nameParts) > 1 ? $nameParts[0] : null;
                            $label = ucwords(str_replace('_', ' ', end($nameParts)));

                            // Status
                            if (!$flag->enabled) {
                                $statusLabel = 'Disabled';
                                $statusClass = 'bg-zinc-100 text-zinc-500 ring-zinc-200/80';
                                $dotClass   = 'bg-zinc-400';
                            } elseif ($flag->starts_at && $now->isBefore($flag->starts_at)) {
                                $statusLabel = 'Scheduled · ' . $flag->starts_at->format('M j');
                                $statusClass = 'bg-blue-50 text-blue-700 ring-blue-200/80';
                                $dotClass   = 'bg-blue-400';
                            } elseif ($flag->ends_at && $now->isAfter($flag->ends_at)) {
                                $statusLabel = 'Expired · ' . $flag->ends_at->format('M j');
                                $statusClass = 'bg-red-50 text-red-600 ring-red-200/80';
                                $dotClass   = 'bg-red-400';
                            } elseif ($flag->ends_at) {
                                $statusLabel = 'Active · ends ' . $flag->ends_at->format('M j');
                                $statusClass = 'bg-emerald-50 text-emerald-700 ring-emerald-200/80';
                                $dotClass   = 'bg-emerald-500';
                            } else {
                                $statusLabel = 'Active';
                                $statusClass = 'bg-emerald-50 text-emerald-700 ring-emerald-200/80';
                                $dotClass   = 'bg-emerald-500';
                            }

                            // Targeting — cap to 2 visible chips
                            $rules       = $flag->attribute_rules ?? [];
                            $visibleRules = array_slice($rules, 0, 2);
                            $extra       = count($rules) - count($visibleRules);
                        @endphp
                        <tr class="hover:bg-zinc-50/60 transition-colors">

                            {{-- Flag name — human-readable, namespace as small tag --}}
                            <td class="px-5 py-4">
                                <div class="flex items-baseline gap-2">
                                    <span class="text-sm font-semibold text-zinc-900">{{ $label }}</span>
                                    @if ($namespace)
                                        <span class="rounded bg-zinc-100 px-1.5 py-0.5 font-mono text-[10px] font-medium text-zinc-400">{{ $namespace }}</span>
                                    @endif
                                </div>
                                @if ($flag->description)
                                    <p class="mt-0.5 max-w-xs truncate text-xs text-zinc-400">{{ $flag->description }}</p>
                                @endif
                            </td>

                            {{-- Targeting — max 2 chips + overflow badge --}}
                            <td class="px-5 py-4">
                                @if (!empty($rules))
                                    <div class="flex flex-wrap items-center gap-1">
                                        @foreach ($visibleRules as $rule)
                                            <span class="inline-flex items-center rounded-md bg-violet-50 px-2 py-0.5 text-xs font-medium text-violet-700 ring-1 ring-inset ring-violet-200 whitespace-nowrap">
                                                {{ $rule['attribute'] }}: {{ implode(', ', $rule['values']) }}
                                            </span>
                                        @endforeach
                                        @if ($extra > 0)
                                            <span class="rounded-md bg-zinc-100 px-2 py-0.5 text-xs font-medium text-zinc-500 ring-1 ring-inset ring-zinc-200">
                                                +{{ $extra }} more
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-xs text-zinc-400">All users</span>
                                @endif
                            </td>

                            {{-- Rollout --}}
                            <td class="px-5 py-4">
                                @if ($flag->rollout_percentage !== null)
                                    <div class="flex items-center gap-2">
                                        <div class="h-1.5 w-16 overflow-hidden rounded-full bg-zinc-200">
                                            <div class="h-full rounded-full bg-emerald-500" style="width: {{ $flag->rollout_percentage }}%"></div>
                                        </div>
                                        <span class="text-xs font-semibold text-zinc-700">{{ $flag->rollout_percentage }}%</span>
                                    </div>
                                @else
                                    <span class="text-xs text-zinc-400">100%</span>
                                @endif
                            </td>

                            {{-- Status --}}
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-md px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $statusClass }}">
                                    <span class="h-1.5 w-1.5 flex-shrink-0 rounded-full {{ $dotClass }}"></span>
                                    {{ $statusLabel }}
                                </span>
                            </td>

                            {{-- Inline toggle --}}
                            <td class="px-5 py-4 text-center"
                                x-data="inlineToggle({{ $flag->id }}, {{ $flag->enabled ? 'true' : 'false' }})">
                                <button type="button"
                                        @click="toggle()"
                                        :disabled="loading"
                                        :aria-label="enabled ? 'Disable' : 'Enable'"
                                        class="relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer rounded-full transition-colors duration-200 focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-1 disabled:cursor-wait disabled:opacity-60"
                                        :class="enabled ? 'bg-emerald-500' : 'bg-zinc-300'">
                                    <span class="absolute top-0.5 left-0.5 h-4 w-4 rounded-full bg-white shadow transition-transform duration-200"
                                          :class="enabled ? 'translate-x-4' : 'translate-x-0'"></span>
                                </button>
                            </td>

                            {{-- Pencil edit button --}}
                            <td class="px-4 py-4">
                                <a href="{{ route('admin.feature_flags.edit', $flag) }}"
                                   title="Edit {{ $flag->name }}"
                                   class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-zinc-200 bg-white text-zinc-500 shadow-sm transition-all hover:border-zinc-300 hover:bg-zinc-50 hover:text-zinc-800 hover:shadow">
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

<script>
function inlineToggle(id, initial) {
    return {
        enabled: initial,
        loading: false,
        async toggle() {
            this.loading = true;
            try {
                const res = await fetch(`/api/admin/feature_flags/${id}`, {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ enabled: !this.enabled }),
                });
                if (res.ok) {
                    const json = await res.json();
                    this.enabled = json.data.enabled;
                }
            } finally {
                this.loading = false;
            }
        },
    };
}
</script>
@endsection
