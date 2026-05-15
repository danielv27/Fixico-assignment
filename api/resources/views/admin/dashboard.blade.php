@extends('layouts.admin')

@section('title', 'Feature flags')
@section('main_class', 'max-w-[1320px]')

@section('content')
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-emerald-700">Admin console</p>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-zinc-950">Feature flag operations</h1>
            <p class="mt-1 text-sm text-zinc-500">Rollout state, targeting, schedules, and change activity.</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="flex items-center gap-1 rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm shadow-sm">
                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                <span class="font-semibold tabular-nums text-zinc-900">{{ $stats['active'] }}</span>
                <span class="text-zinc-500">of</span>
                <span class="font-semibold tabular-nums text-zinc-900">{{ $stats['total'] }}</span>
                <span class="text-zinc-500">flags active</span>
            </div>
            <a href="{{ route('admin.feature_flags.create') }}"
               class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 transition-colors">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                New flag
            </a>
        </div>
    </div>

    <section class="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-7">
        @foreach ([
            ['label' => 'Total',     'value' => $stats['total'],     'caption' => 'configured flags', 'class' => 'text-zinc-950'],
            ['label' => 'Active',    'value' => $stats['active'],    'caption' => 'serving now',       'class' => 'text-emerald-700'],
            ['label' => 'Disabled',  'value' => $stats['disabled'],  'caption' => 'off in clients',    'class' => 'text-zinc-600'],
            ['label' => 'Targeted',  'value' => $stats['targeted'],  'caption' => 'audience rules',    'class' => 'text-violet-700'],
            ['label' => 'Limited',   'value' => $stats['limited'],   'caption' => 'percentage gates',  'class' => 'text-amber-700'],
            ['label' => 'Scheduled', 'value' => $stats['scheduled'], 'caption' => 'pending start',     'class' => 'text-blue-700'],
            ['label' => 'Expired',   'value' => $stats['expired'],   'caption' => 'past window',       'class' => 'text-red-600'],
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

    @php
        $tableData = $flags->map(fn ($flag) => [
            'id'                 => $flag->id,
            'name'               => $flag->name,
            'description'        => $flag->description,
            'enabled'            => $flag->enabled,
            'attribute_rules'    => $flag->attribute_rules ?? [],
            'rollout_percentage' => $flag->rollout_percentage,
            'starts_at'          => $flag->starts_at?->toISOString(),
            'ends_at'            => $flag->ends_at?->toISOString(),
            'updated_at'         => $flag->updated_at?->toISOString(),
            'edit_url'           => route('admin.feature_flags.edit', $flag),
        ])->values()->toJson();
    @endphp

    <section class="mt-4 overflow-hidden rounded-lg border border-zinc-200 bg-white shadow-sm"
             x-data="flagTable({{ $tableData }})">
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
                            <th class="w-[26%] px-5 py-3 text-left">
                                <button @click="sortBy('name')" class="inline-flex items-center gap-1 text-[11px] font-semibold uppercase tracking-wide text-zinc-500 hover:text-zinc-800 transition-colors">
                                    Flag <span x-html="sortIcon('name')"></span>
                                </button>
                            </th>
                            <th class="w-[11%] px-4 py-3 text-left">
                                <button @click="sortBy('status')" class="inline-flex items-center gap-1 text-[11px] font-semibold uppercase tracking-wide text-zinc-500 hover:text-zinc-800 transition-colors">
                                    Status <span x-html="sortIcon('status')"></span>
                                </button>
                            </th>
                            <th class="w-[17%] px-4 py-3 text-left">
                                <button @click="sortBy('targeting')" class="inline-flex items-center gap-1 text-[11px] font-semibold uppercase tracking-wide text-zinc-500 hover:text-zinc-800 transition-colors">
                                    Targeting <span x-html="sortIcon('targeting')"></span>
                                </button>
                            </th>
                            <th class="w-[14%] px-4 py-3 text-left">
                                <button @click="sortBy('exposure')" class="inline-flex items-center gap-1 text-[11px] font-semibold uppercase tracking-wide text-zinc-500 hover:text-zinc-800 transition-colors">
                                    Exposure <span x-html="sortIcon('exposure')"></span>
                                </button>
                            </th>
                            <th class="w-[16%] px-4 py-3 text-left">
                                <button @click="sortBy('schedule')" class="inline-flex items-center gap-1 text-[11px] font-semibold uppercase tracking-wide text-zinc-500 hover:text-zinc-800 transition-colors">
                                    Schedule <span x-html="sortIcon('schedule')"></span>
                                </button>
                            </th>
                            <th class="w-[9%] px-4 py-3 text-right">
                                <button @click="sortBy('changed')" class="inline-flex items-center gap-1 text-[11px] font-semibold uppercase tracking-wide text-zinc-500 hover:text-zinc-800 transition-colors">
                                    Changed <span x-html="sortIcon('changed')"></span>
                                </button>
                            </th>
                            <th class="w-[7%] px-5 py-3 text-center">
                                <button @click="sortBy('enabled')" class="inline-flex items-center gap-1 text-[11px] font-semibold uppercase tracking-wide text-zinc-500 hover:text-zinc-800 transition-colors">
                                    On <span x-html="sortIcon('enabled')"></span>
                                </button>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        <template x-for="flag in sorted" :key="flag.id">
                            <tr class="transition-colors hover:bg-zinc-50/70">

                                {{-- Flag name (clickable → edit) + description --}}
                                <td class="px-5 py-4">
                                    <a :href="flag.edit_url"
                                       class="block truncate font-mono text-sm font-semibold text-zinc-950 hover:text-emerald-700 transition-colors"
                                       x-text="flag.name"></a>
                                    <p x-show="flag.description"
                                       class="mt-0.5 max-w-md truncate text-xs text-zinc-400"
                                       x-text="flag.description"></p>
                                </td>

                                {{-- Status (reactive) --}}
                                <td class="px-4 py-4">
                                    <span class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-md px-2 py-1 text-xs font-semibold ring-1 ring-inset cursor-default"
                                          :class="statusFor(flag).cls"
                                          :title="statusFor(flag).date ?? ''">
                                        <span class="h-1.5 w-1.5 rounded-full" :class="statusFor(flag).dot"></span>
                                        <span x-text="statusFor(flag).label"></span>
                                    </span>
                                </td>

                                {{-- Targeting rules --}}
                                <td class="px-4 py-4">
                                    <template x-if="flag.attribute_rules.length > 0">
                                        <div class="flex max-w-[240px] flex-wrap gap-1">
                                            <template x-for="(rule, i) in flag.attribute_rules.slice(0, 2)" :key="i">
                                                <span class="rounded-md bg-violet-50 px-2 py-0.5 text-xs font-medium text-violet-700 ring-1 ring-inset ring-violet-200"
                                                      x-text="rule.attribute + ': ' + rule.values.join(', ')"></span>
                                            </template>
                                            <template x-if="flag.attribute_rules.length > 2">
                                                <span class="rounded-md bg-zinc-100 px-2 py-0.5 text-xs font-medium text-zinc-500 ring-1 ring-inset ring-zinc-200"
                                                      x-text="'+' + (flag.attribute_rules.length - 2)"></span>
                                            </template>
                                        </div>
                                    </template>
                                    <template x-if="flag.attribute_rules.length === 0">
                                        <span class="text-xs text-zinc-400">All users</span>
                                    </template>
                                </td>

                                {{-- Exposure --}}
                                <td class="px-4 py-4">
                                    <template x-if="flag.rollout_percentage !== null">
                                        <div class="flex min-w-32 items-center gap-2">
                                            <div class="h-1.5 w-24 overflow-hidden rounded-full bg-zinc-200">
                                                <div class="h-full rounded-full bg-emerald-500" :style="'width: ' + flag.rollout_percentage + '%'"></div>
                                            </div>
                                            <span class="w-9 text-xs font-semibold tabular-nums text-zinc-700" x-text="flag.rollout_percentage + '%'"></span>
                                        </div>
                                    </template>
                                    <template x-if="flag.rollout_percentage === null">
                                        <div class="text-xs">
                                            <span class="font-semibold tabular-nums text-zinc-700">100%</span>
                                            <span class="ml-1 text-zinc-400">matched</span>
                                        </div>
                                    </template>
                                </td>

                                {{-- Schedule --}}
                                <td class="px-4 py-4">
                                    <template x-if="flag.starts_at || flag.ends_at">
                                        <div class="space-y-0.5 whitespace-nowrap text-xs text-zinc-500">
                                            <p x-show="flag.starts_at" x-text="'starts ' + fmtDate(flag.starts_at)"></p>
                                            <p x-show="flag.ends_at"   x-text="'ends '   + fmtDate(flag.ends_at)"></p>
                                        </div>
                                    </template>
                                    <template x-if="!flag.starts_at && !flag.ends_at">
                                        <span class="text-xs text-zinc-400">No schedule</span>
                                    </template>
                                </td>

                                {{-- Changed --}}
                                <td class="whitespace-nowrap px-4 py-4 text-right text-xs text-zinc-400"
                                    x-text="timeAgo(flag.updated_at)"></td>

                                {{-- Toggle --}}
                                <td class="px-5 py-4 text-center">
                                    <button type="button"
                                            @click="toggle(flag)"
                                            :disabled="loadingId === flag.id || isExpired(flag)"
                                            :title="isExpired(flag) ? 'Expired flags cannot be toggled' : ''"
                                            class="relative inline-flex h-5 w-9 flex-shrink-0 rounded-full transition-colors duration-200 focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-1 disabled:cursor-not-allowed"
                                            :class="isExpired(flag) ? 'bg-zinc-200 opacity-40' : (flag.enabled ? 'bg-emerald-500 cursor-pointer' : 'bg-zinc-300 cursor-pointer')">
                                        <span class="absolute top-0.5 left-0.5 h-4 w-4 rounded-full bg-white shadow transition-transform duration-200"
                                              :class="flag.enabled ? 'translate-x-4' : 'translate-x-0'"></span>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        @endif
    </section>

<script>
function flagTable(flags) {
    return {
        flags,
        sortKey: 'name',
        sortDir: 'asc',
        loadingId: null,

        get sorted() {
            return [...this.flags].sort((a, b) => {
                const v = (f) => {
                    if (this.sortKey === 'name')     return f.name;
                    if (this.sortKey === 'status')   return this.statusFor(f).order;
                    if (this.sortKey === 'targeting') return f.attribute_rules.length;
                    if (this.sortKey === 'exposure') return f.rollout_percentage ?? 100;
                    if (this.sortKey === 'schedule') return f.starts_at ?? f.ends_at ?? '9999';
                    if (this.sortKey === 'changed')  return f.updated_at ?? '';
                    if (this.sortKey === 'enabled')  return f.enabled ? 1 : 0;
                };
                const [va, vb] = [v(a), v(b)];
                const cmp = typeof va === 'string' ? va.localeCompare(vb) : va - vb;
                return this.sortDir === 'asc' ? cmp : -cmp;
            });
        },

        sortBy(key) {
            this.sortDir = this.sortKey === key && this.sortDir === 'asc' ? 'desc' : 'asc';
            this.sortKey = key;
        },

        sortIcon(key) {
            if (this.sortKey !== key) return '<svg class="h-3 w-3 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>';
            return this.sortDir === 'asc'
                ? '<svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>'
                : '<svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>';
        },

        isExpired(flag) {
            return flag.ends_at !== null && new Date() > new Date(flag.ends_at);
        },

        statusFor(flag) {
            const now = new Date();
            const startsAt = flag.starts_at ? new Date(flag.starts_at) : null;
            const endsAt   = flag.ends_at   ? new Date(flag.ends_at)   : null;
            const fmt = (d) => d.toLocaleDateString('en', { month: 'short', day: 'numeric' });

            if (!flag.enabled)               return { label: 'Disabled',  date: null,                    cls: 'bg-zinc-100 text-zinc-500 ring-zinc-200',       dot: 'bg-zinc-400',    order: 0 };
            if (endsAt && now > endsAt)      return { label: 'Expired',   date: fmt(endsAt),             cls: 'bg-red-50 text-red-600 ring-red-200',           dot: 'bg-red-400',     order: 1 };
            if (startsAt && now < startsAt)  return { label: 'Scheduled', date: 'from ' + fmt(startsAt), cls: 'bg-blue-50 text-blue-700 ring-blue-200',        dot: 'bg-blue-400',    order: 2 };
            if (endsAt)                      return { label: 'Active',    date: 'until ' + fmt(endsAt),  cls: 'bg-emerald-50 text-emerald-700 ring-emerald-200', dot: 'bg-emerald-500', order: 3 };
            return                                  { label: 'Active',    date: null,                    cls: 'bg-emerald-50 text-emerald-700 ring-emerald-200', dot: 'bg-emerald-500', order: 4 };
        },

        fmtDate(iso) {
            if (!iso) return '';
            return new Date(iso).toLocaleDateString('en', { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
        },

        timeAgo(iso) {
            if (!iso) return 'never';
            const seconds = Math.floor((Date.now() - new Date(iso)) / 1000);
            if (seconds < 60)   return 'just now';
            if (seconds < 3600) return Math.floor(seconds / 60) + 'm ago';
            if (seconds < 86400) return Math.floor(seconds / 3600) + 'h ago';
            if (seconds < 2592000) return Math.floor(seconds / 86400) + 'd ago';
            return new Date(iso).toLocaleDateString('en', { month: 'short', day: 'numeric' });
        },

        async toggle(flag) {
            if (this.isExpired(flag)) return;
            this.loadingId = flag.id;
            try {
                const res = await fetch(`/api/admin/feature_flags/${flag.id}`, {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ enabled: !flag.enabled }),
                });
                if (res.ok) {
                    const json = await res.json();
                    flag.enabled = json.data.enabled;
                }
            } finally {
                this.loadingId = null;
            }
        },
    };
}
</script>
@endsection
