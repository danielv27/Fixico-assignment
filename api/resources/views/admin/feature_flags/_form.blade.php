<div class="flex flex-col gap-6">
    <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm">
        <h2 class="text-sm font-semibold text-zinc-800">Basic info</h2>
        <div class="mt-4 flex flex-col gap-4">
            <div class="flex flex-col gap-1.5">
                <label for="description" class="text-sm font-medium text-zinc-700">Description</label>
                <textarea id="description" name="description" rows="2"
                          placeholder="What does this flag control?"
                          class="rounded-lg border border-zinc-300 px-3 py-2 text-sm placeholder-zinc-400 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 @error('description') border-red-400 @enderror">{{ old('description', $description ?? '') }}</textarea>
                @error('description')
                    <p class="text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div x-data="{ on: {{ old('enabled', $enabled ?? false) ? 'true' : 'false' }} }">
                <input type="hidden" name="enabled" :value="on ? '1' : '0'">
                <button type="button" @click="on = !on"
                        class="flex items-center gap-3 group"
                        :aria-pressed="on">
                    <div class="relative flex h-5 w-9 rounded-full transition-colors duration-200"
                         :class="on ? 'bg-emerald-500' : 'bg-zinc-300'">
                        <span class="absolute top-0.5 left-0.5 h-4 w-4 rounded-full bg-white shadow transition-transform duration-200"
                              :class="on ? 'translate-x-4' : 'translate-x-0'"></span>
                    </div>
                    <span class="text-sm font-medium text-zinc-700" x-text="on ? 'Enabled' : 'Disabled'"></span>
                </button>

                @if ($isExpired ?? false)
                    <p class="mt-2 text-xs text-amber-600">
                        This flag is expired — the enabled state has no effect until you extend the expiry date.
                    </p>
                @endif
            </div>
        </div>
    </div>

    <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm"
         x-data="rulesBuilder({{ $rulesJson }})">

        <div class="flex items-start justify-between">
            <div>
                <h2 class="text-sm font-semibold text-zinc-800">Audience targeting</h2>
                <p class="mt-0.5 text-xs text-zinc-500">All rules must match (AND). Leave empty to target everyone.</p>
            </div>
            <button type="button" @click="addRule()"
                    class="inline-flex items-center gap-1 rounded-md bg-zinc-100 px-2.5 py-1.5 text-xs font-medium text-zinc-700 hover:bg-zinc-200 transition-colors">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add rule
            </button>
        </div>

        <div class="mt-4 flex flex-col gap-3">
            <template x-for="(rule, index) in rules" :key="index">
                <div class="rounded-lg border border-zinc-200 bg-zinc-50 p-3">
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-semibold text-zinc-400 w-8 flex-shrink-0 uppercase tracking-wide"
                              x-text="index === 0 ? 'IF' : 'AND'"></span>

                        <select x-model="rule.attribute" @change="rule.values = []"
                                class="rounded-md border border-zinc-300 bg-white px-2.5 py-1.5 text-xs font-medium text-zinc-700 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                            <option value="country">country</option>
                            <option value="role">role</option>
                        </select>

                        <span class="text-xs text-zinc-400">is</span>

                        <button type="button" @click="removeRule(index)"
                                class="ml-auto rounded p-1 text-zinc-300 hover:bg-zinc-200 hover:text-zinc-600 transition-colors">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="mt-2.5 flex flex-wrap gap-1.5">
                        <template x-for="opt in optionsFor(rule.attribute)" :key="opt">
                            <button type="button"
                                    @click="toggleValue(index, opt)"
                                    class="rounded-full border px-3 py-0.5 text-xs font-medium transition-all"
                                    :class="rule.values.includes(opt)
                                        ? 'border-violet-500 bg-violet-600 text-white shadow-sm'
                                        : 'border-zinc-300 bg-white text-zinc-600 hover:border-zinc-400 hover:bg-zinc-50'">
                                <span x-text="opt"></span>
                            </button>
                        </template>
                    </div>

                    <p x-show="rule.values.length === 0" x-cloak
                       class="mt-2 text-xs text-amber-600">
                        Select at least one value.
                    </p>
                </div>
            </template>

            <div x-show="rules.length === 0" x-cloak
                 class="rounded-lg border border-dashed border-zinc-200 py-4 text-center text-xs text-zinc-400">
                No targeting rules — flag applies to all users.
            </div>
        </div>

        <input type="hidden" name="attribute_rules" :value="serialize()">

        @error('attribute_rules.*')
            <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm">
        <h2 class="text-sm font-semibold text-zinc-800">Schedule <span class="font-normal text-zinc-400">(optional)</span></h2>
        <p class="mt-0.5 text-xs text-zinc-500">Leave blank for no boundary on that side.</p>
        <div class="mt-4 grid grid-cols-2 gap-4">
            <div class="flex flex-col gap-1.5">
                <label for="starts_at" class="text-sm font-medium text-zinc-700">Activates at</label>
                <input id="starts_at" name="starts_at" type="datetime-local"
                       value="{{ old('starts_at', $startsAt ?? '') }}"
                       class="rounded-lg border border-zinc-300 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 @error('starts_at') border-red-400 @enderror">
                @error('starts_at')
                    <p class="text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex flex-col gap-1.5">
                <label for="ends_at" class="text-sm font-medium text-zinc-700">Expires at</label>
                <input id="ends_at" name="ends_at" type="datetime-local"
                       value="{{ old('ends_at', $endsAt ?? '') }}"
                       class="rounded-lg border border-zinc-300 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 @error('ends_at') border-red-400 @enderror">
                @error('ends_at')
                    <p class="text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm"
         x-data="percentageSlider({{ $pct ?? 'null' }})">

        <div class="flex items-start justify-between">
            <div>
                <h2 class="text-sm font-semibold text-zinc-800">Rollout percentage</h2>
                <p class="mt-0.5 text-xs text-zinc-500">
                    Fraction of eligible users who see this feature. Bucketing is deterministic — the same user always lands in the same bucket.
                    Enable to set a limit and <span class="font-medium text-zinc-600">simulate which users are included</span>.
                </p>
            </div>
            <label class="inline-flex cursor-pointer items-center gap-2 text-xs font-medium text-zinc-600">
                <input type="checkbox" x-model="enabled" class="h-3.5 w-3.5 rounded border-zinc-300 text-emerald-600 focus:ring-emerald-500">
                Limit rollout
            </label>
        </div>

        <div x-show="enabled" x-cloak class="mt-4 flex flex-col gap-4">
            <div>
                <div class="flex items-center gap-4">
                    <input type="range" min="0" max="100" x-model.number="value"
                           class="flex-1 h-2 cursor-pointer rounded-full">
                    <div class="flex items-center gap-1">
                        <input type="number" min="0" max="100" x-model.number="value"
                               class="w-16 rounded-md border border-zinc-300 px-2 py-1 text-center text-sm font-medium focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                        <span class="text-sm text-zinc-500">%</span>
                    </div>
                </div>
                <div class="mt-1.5 flex justify-between text-xs text-zinc-400">
                    <span>0% — nobody</span>
                    <span x-text="`${value}% of eligible users`"></span>
                    <span>100% — everyone</span>
                </div>
            </div>

            <div class="rounded-lg border border-zinc-200 bg-zinc-50 p-4" x-init="buildSample()">

                <div class="mb-3 flex items-center justify-between">
                    <span class="text-xs font-semibold text-zinc-700">Distribution preview</span>
                    <span class="text-xs text-zinc-400">2 per bucket · 200 total</span>
                </div>

                <div class="grid gap-0.5" style="grid-template-columns: repeat(10, 1fr)">
                    <template x-for="b in 100" :key="b">
                        <div class="flex h-8 cursor-default flex-col items-center justify-center rounded-sm transition-colors"
                             :class="(b-1) < value ? 'bg-emerald-500 text-white' : 'bg-zinc-200 text-zinc-400'"
                             :title="`Bucket ${b-1} — ${(b-1) < value ? 'in rollout' : 'excluded'}`">
                            <span class="text-[8px] font-medium leading-none opacity-60" x-text="b-1"></span>
                            <div class="mt-0.5 flex gap-0.5">
                                <span class="h-1 w-1 rounded-full"
                                      :class="(b-1) < value ? 'bg-white/70' : 'bg-zinc-400/40'"></span>
                                <span class="h-1 w-1 rounded-full"
                                      :class="(b-1) < value ? 'bg-white/70' : 'bg-zinc-400/40'"></span>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="mt-3 mb-3 flex items-center justify-between border-b border-zinc-200 pb-3">
                    <div class="flex items-center gap-3 text-xs text-zinc-500">
                        <span class="flex items-center gap-1.5">
                            <span class="inline-block h-2.5 w-2.5 rounded-sm bg-emerald-500"></span>
                            In rollout
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="inline-block h-2.5 w-2.5 rounded-sm bg-zinc-200"></span>
                            Excluded
                        </span>
                    </div>
                    <p class="text-xs font-medium text-zinc-700">
                        <span class="text-emerald-700" x-text="value * 2"></span>
                        <span class="font-normal text-zinc-400"> of 200 users &mdash; exactly </span>
                        <span class="text-emerald-700" x-text="`${value}%`"></span>
                    </p>
                </div>

                <div class="max-h-56 overflow-y-auto rounded-lg border border-zinc-200 bg-white">
                    <table class="w-full text-xs">
                        <thead class="sticky top-0 bg-zinc-50 shadow-sm">
                            <tr class="border-b border-zinc-100">
                                <th class="px-3 py-2 text-left font-semibold text-zinc-500">User</th>
                                <th class="px-3 py-2 text-center font-semibold text-zinc-500">Bucket</th>
                                <th class="px-3 py-2 text-left font-semibold text-zinc-500">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-50">
                            <template x-for="u in sample" :key="u.name">
                                <tr :class="u.bucket < value ? 'bg-emerald-50/40' : ''">
                                    <td class="px-3 py-1.5 font-mono font-medium text-zinc-700" x-text="u.name"></td>
                                    <td class="px-3 py-1.5 text-center tabular-nums text-zinc-500" x-text="u.bucket"></td>
                                    <td class="px-3 py-1.5">
                                        <span class="inline-flex items-center gap-1 font-medium"
                                              :class="u.bucket < value ? 'text-emerald-700' : 'text-zinc-400'">
                                            <svg class="h-3 w-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path x-show="u.bucket < value"  stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                <path x-show="u.bucket >= value" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                            <span x-text="u.bucket < value ? 'In rollout' : 'Excluded'"></span>
                                        </span>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <div class="mt-3 flex items-start gap-2.5 rounded-lg border border-blue-200 bg-blue-50 px-3.5 py-3">
                    <svg class="mt-0.5 h-4 w-4 flex-shrink-0 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <div class="text-xs leading-relaxed text-blue-800">
                        <span class="font-semibold">Illustration only.</span>
                        These 200 users are synthetically generated — exactly 2 per bucket — to show how percentage rollouts distribute evenly across your user base.
                        In production, subjects are real user identifiers (UUIDs, emails, etc.) which distribute the same way naturally.
                    </div>
                </div>
            </div>
        </div>

        <div x-show="!enabled" class="mt-3 text-xs text-zinc-400">100% of eligible users (no percentage limit applied)</div>

        <input type="hidden" name="rollout_percentage" :value="enabled ? value : ''">
    </div>

</div>

<script>
const ATTRIBUTE_OPTIONS = {
    country: ['NL', 'BE', 'DE', 'FR', 'GB'],
    role: ['admin', 'mechanic', 'customer'],
};

function rulesBuilder(initial) {
    return {
        rules: [],
        init() {
            const data = Array.isArray(initial) ? initial : [];
            this.rules = data.map(r => ({
                attribute: r.attribute || 'country',
                values: Array.isArray(r.values) ? [...r.values] : [],
            }));
        },
        optionsFor(attribute) {
            return ATTRIBUTE_OPTIONS[attribute] || [];
        },
        addRule() {
            this.rules.push({ attribute: 'country', values: [] });
        },
        removeRule(i) {
            this.rules.splice(i, 1);
        },
        toggleValue(ruleIndex, val) {
            const idx = this.rules[ruleIndex].values.indexOf(val);
            if (idx === -1) {
                this.rules[ruleIndex].values.push(val);
            } else {
                this.rules[ruleIndex].values.splice(idx, 1);
            }
        },
        serialize() {
            return JSON.stringify(
                this.rules
                    .filter(r => r.values.length > 0)
                    .map(r => ({ attribute: r.attribute, values: r.values }))
            );
        },
    };
}

// CRC32 — PHP-compatible, used once to pre-generate the even sample.
const _t32 = (() => {
    const t = new Uint32Array(256);
    for (let i = 0; i < 256; i++) {
        let c = i;
        for (let j = 0; j < 8; j++) c = (c & 1) ? (0xEDB88320 ^ (c >>> 1)) : (c >>> 1);
        t[i] = c;
    }
    return t;
})();

function _crc32(str) {
    const bytes = new TextEncoder().encode(str);
    let crc = 0xFFFFFFFF;
    for (const b of bytes) crc = _t32[(crc ^ b) & 0xFF] ^ (crc >>> 8);
    const u = (crc ^ 0xFFFFFFFF) >>> 0;
    return u > 0x7FFFFFFF ? u - 0x100000000 : u;
}

function percentageSlider(initial) {
    return {
        enabled: initial !== null,
        value: initial ?? 100,
        sample: [],

        buildSample() {
            const items = [];
            const counts = new Array(100).fill(0);
            let i = 0;
            while (items.length < 200) {
                const name = `user-${i++}`;
                const b = Math.abs(_crc32(name)) % 100;
                if (counts[b] < 2) {
                    counts[b]++;
                    items.push({ name, bucket: b });
                }
            }
            this.sample = items.sort((a, b) => a.bucket - b.bucket);
        },
    };
}
</script>
