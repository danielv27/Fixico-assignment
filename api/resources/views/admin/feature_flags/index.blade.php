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
                'edit_url'           => route('admin.feature_flags.edit', $flag),
            ])->values()->toJson();
        @endphp

        <div class="mt-6 overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm"
             x-data="flagTable({{ $tableData }})">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-zinc-100 bg-zinc-50/80">
                        <th class="px-5 py-3 text-left">
                            <button @click="sortBy('name')" class="inline-flex items-center gap-1 text-xs font-semibold uppercase tracking-wide text-zinc-500 hover:text-zinc-800 transition-colors">
                                Flag <span x-html="sortIcon('name')"></span>
                            </button>
                        </th>
                        <th class="px-5 py-3 text-left">
                            <button @click="sortBy('targeting')" class="inline-flex items-center gap-1 text-xs font-semibold uppercase tracking-wide text-zinc-500 hover:text-zinc-800 transition-colors">
                                Targeting <span x-html="sortIcon('targeting')"></span>
                            </button>
                        </th>
                        <th class="px-5 py-3 text-left">
                            <button @click="sortBy('rollout')" class="inline-flex items-center gap-1 text-xs font-semibold uppercase tracking-wide text-zinc-500 hover:text-zinc-800 transition-colors">
                                Rollout <span x-html="sortIcon('rollout')"></span>
                            </button>
                        </th>
                        <th class="px-5 py-3 text-left">
                            <button @click="sortBy('status')" class="inline-flex items-center gap-1 text-xs font-semibold uppercase tracking-wide text-zinc-500 hover:text-zinc-800 transition-colors">
                                Status <span x-html="sortIcon('status')"></span>
                            </button>
                        </th>
                        <th class="px-5 py-3 text-center">
                            <button @click="sortBy('enabled')" class="inline-flex items-center gap-1 text-xs font-semibold uppercase tracking-wide text-zinc-500 hover:text-zinc-800 transition-colors">
                                On <span x-html="sortIcon('enabled')"></span>
                            </button>
                        </th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100">
                    <template x-for="flag in sorted" :key="flag.id">
                        <tr class="hover:bg-zinc-50/60 transition-colors">

                            {{-- Flag name + description --}}
                            <td class="px-5 py-4">
                                <span class="font-mono text-sm font-semibold text-zinc-900" x-text="flag.name"></span>
                                <p x-show="flag.description" class="mt-0.5 max-w-xs truncate text-xs text-zinc-400" x-text="flag.description"></p>
                            </td>

                            {{-- Targeting rules --}}
                            <td class="px-5 py-4">
                                <template x-if="flag.attribute_rules.length > 0">
                                    <div class="flex flex-wrap items-center gap-1">
                                        <template x-for="(rule, i) in flag.attribute_rules.slice(0, 2)" :key="i">
                                            <span class="inline-flex items-center rounded-md bg-violet-50 px-2 py-0.5 text-xs font-medium text-violet-700 ring-1 ring-inset ring-violet-200 whitespace-nowrap"
                                                  x-text="rule.attribute + ': ' + rule.values.join(', ')"></span>
                                        </template>
                                        <template x-if="flag.attribute_rules.length > 2">
                                            <span class="rounded-md bg-zinc-100 px-2 py-0.5 text-xs font-medium text-zinc-500 ring-1 ring-inset ring-zinc-200"
                                                  x-text="'+' + (flag.attribute_rules.length - 2) + ' more'"></span>
                                        </template>
                                    </div>
                                </template>
                                <template x-if="flag.attribute_rules.length === 0">
                                    <span class="text-xs text-zinc-400">All users</span>
                                </template>
                            </td>

                            {{-- Rollout percentage --}}
                            <td class="px-5 py-4">
                                <template x-if="flag.rollout_percentage !== null">
                                    <div class="flex items-center gap-2">
                                        <div class="h-1.5 w-16 overflow-hidden rounded-full bg-zinc-200">
                                            <div class="h-full rounded-full bg-emerald-500" :style="'width: ' + flag.rollout_percentage + '%'"></div>
                                        </div>
                                        <span class="text-xs font-semibold text-zinc-700" x-text="flag.rollout_percentage + '%'"></span>
                                    </div>
                                </template>
                                <template x-if="flag.rollout_percentage === null">
                                    <span class="text-xs text-zinc-400">100%</span>
                                </template>
                            </td>

                            {{-- Status (reactive) --}}
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-md px-2.5 py-1 text-xs font-semibold ring-1 ring-inset"
                                      :class="statusFor(flag).cls">
                                    <span class="h-1.5 w-1.5 flex-shrink-0 rounded-full" :class="statusFor(flag).dot"></span>
                                    <span x-text="statusFor(flag).label"></span>
                                </span>
                            </td>

                            {{-- Toggle --}}
                            <td class="px-5 py-4 text-center">
                                <button type="button"
                                        @click="toggle(flag)"
                                        :disabled="loadingId === flag.id || isExpired(flag)"
                                        :aria-label="isExpired(flag) ? 'Expired' : (flag.enabled ? 'Disable' : 'Enable')"
                                        :title="isExpired(flag) ? 'Expired flags cannot be toggled' : ''"
                                        class="relative inline-flex h-5 w-9 flex-shrink-0 rounded-full transition-colors duration-200 focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-1 disabled:cursor-not-allowed"
                                        :class="isExpired(flag) ? 'bg-zinc-200 opacity-40' : (flag.enabled ? 'bg-emerald-500 cursor-pointer' : 'bg-zinc-300 cursor-pointer')">
                                    <span class="absolute top-0.5 left-0.5 h-4 w-4 rounded-full bg-white shadow transition-transform duration-200"
                                          :class="flag.enabled ? 'translate-x-4' : 'translate-x-0'"></span>
                                </button>
                            </td>

                            {{-- Edit link --}}
                            <td class="px-4 py-4">
                                <a :href="flag.edit_url"
                                   :title="'Edit ' + flag.name"
                                   class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-zinc-200 bg-white text-zinc-500 shadow-sm transition-all hover:border-zinc-300 hover:bg-zinc-50 hover:text-zinc-800 hover:shadow">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    @endif

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
                    if (this.sortKey === 'name')      return f.name;
                    if (this.sortKey === 'targeting') return f.attribute_rules.length;
                    if (this.sortKey === 'rollout')   return f.rollout_percentage ?? 100;
                    if (this.sortKey === 'status')    return this.statusFor(f).order;
                    if (this.sortKey === 'enabled')   return f.enabled ? 1 : 0;
                };
                const [va, vb] = [v(a), v(b)];
                const cmp = typeof va === 'string' ? va.localeCompare(vb) : va - vb;
                return this.sortDir === 'asc' ? cmp : -cmp;
            });
        },

        sortBy(key) {
            if (this.sortKey === key) {
                this.sortDir = this.sortDir === 'asc' ? 'desc' : 'asc';
            } else {
                this.sortKey = key;
                this.sortDir = 'asc';
            }
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

            if (!flag.enabled) {
                return { label: 'Disabled',  cls: 'bg-zinc-100 text-zinc-500 ring-zinc-200/80',       dot: 'bg-zinc-400',   order: 0 };
            }
            if (endsAt && now > endsAt) {
                const d = endsAt.toLocaleDateString('en', { month: 'short', day: 'numeric' });
                return { label: `Expired · ${d}`,       cls: 'bg-red-50 text-red-600 ring-red-200/80',         dot: 'bg-red-400',    order: 1 };
            }
            if (startsAt && now < startsAt) {
                const d = startsAt.toLocaleDateString('en', { month: 'short', day: 'numeric' });
                return { label: `Scheduled · ${d}`,     cls: 'bg-blue-50 text-blue-700 ring-blue-200/80',      dot: 'bg-blue-400',   order: 2 };
            }
            if (endsAt) {
                const d = endsAt.toLocaleDateString('en', { month: 'short', day: 'numeric' });
                return { label: `Active · ends ${d}`,   cls: 'bg-emerald-50 text-emerald-700 ring-emerald-200/80', dot: 'bg-emerald-500', order: 3 };
            }
            return { label: 'Active', cls: 'bg-emerald-50 text-emerald-700 ring-emerald-200/80', dot: 'bg-emerald-500', order: 4 };
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
