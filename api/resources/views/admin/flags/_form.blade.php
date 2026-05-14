{{--
  Shared form fields for create / edit.
  Variables expected:
    $rulesJson   - JSON-encoded initial attribute_rules (e.g. "[]")
    $pct         - initial rollout_percentage value or null
    $startsAt    - initial starts_at formatted for datetime-local or null
    $endsAt      - initial ends_at formatted for datetime-local or null
    $description - initial description
    $enabled     - initial enabled boolean
    $flagName    - flag slug for the evaluate simulator (null on create)
--}}

<div class="flex flex-col gap-6">

    {{-- ── Basic info ──────────────────────────────────────────────── --}}
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

            {{-- Toggle --}}
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
            </div>
        </div>
    </div>

    {{-- ── Audience targeting ───────────────────────────────────────── --}}
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

                    {{-- Multi-select chips — predefined per attribute --}}
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

    {{-- ── Rollout percentage ───────────────────────────────────────── --}}
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

            {{-- ─ Distribution preview ────────────────────────────────── --}}
            {{--
                200 pre-computed subjects — exactly 2 per bucket — so the
                grid is always perfectly even. At N% exactly N*2 of 200 users
                are in rollout, making the percentage literal and unambiguous.
            --}}
            <div class="rounded-lg border border-zinc-200 bg-zinc-50 p-4">
                <div class="mb-3 flex items-center justify-between">
                    <span class="text-xs font-semibold text-zinc-700">Distribution preview</span>
                    <span class="text-xs text-zinc-400">200 evenly distributed users · 2 per bucket</span>
                </div>

                {{-- Grid: 100 buckets, each holding exactly 2 of the 200 users --}}
                <div class="grid gap-0.5" style="grid-template-columns: repeat(10, 1fr)">
                    <template x-for="b in 100" :key="b">
                        <div class="group relative flex h-8 cursor-default flex-col items-center justify-center rounded-sm transition-colors"
                             :class="(b-1) < value
                                 ? 'bg-emerald-500 text-white'
                                 : 'bg-zinc-200 text-zinc-400'"
                             :title="`Bucket ${b-1} — ${(b-1) < value ? '2 users in rollout' : 'excluded'}`">
                            <span class="text-[8px] font-medium leading-none opacity-60" x-text="b-1"></span>
                            {{-- Two user dots --}}
                            <div class="mt-0.5 flex gap-0.5">
                                <span class="h-1 w-1 rounded-full"
                                      :class="(b-1) < value ? 'bg-white/70' : 'bg-zinc-400/50'"></span>
                                <span class="h-1 w-1 rounded-full"
                                      :class="(b-1) < value ? 'bg-white/70' : 'bg-zinc-400/50'"></span>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Summary --}}
                <div class="mt-3 flex items-center justify-between">
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
                        <span class="font-normal text-zinc-500"> of 200 users · exactly </span>
                        <span class="text-emerald-700" x-text="`${value}%`"></span>
                    </p>
                </div>
            </div>
        </div>

        <div x-show="!enabled" class="mt-3 text-xs text-zinc-400">100% of eligible users (no percentage limit applied)</div>

        <input type="hidden" name="rollout_percentage" :value="enabled ? value : ''">
    </div>

    {{-- ── Schedule ─────────────────────────────────────────────────── --}}
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

</div>

<script>
// ---------------------------------------------------------------------------
// Attribute options — matches the allow-list in StoreFlagRequest / UpdateFlagRequest
// ---------------------------------------------------------------------------
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

// ---------------------------------------------------------------------------
// Percentage slider
// ---------------------------------------------------------------------------
function percentageSlider(initial) {
    return {
        enabled: initial !== null,
        value: initial ?? 100,
    };
}
</script>
