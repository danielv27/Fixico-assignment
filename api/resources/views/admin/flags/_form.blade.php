{{--
  Shared form fields for create / edit.
  Variables expected:
    $rulesJson   - JSON-encoded initial attribute_rules (e.g. "[]")
    $pct         - initial rollout_percentage value or null
    $startsAt    - initial starts_at formatted for datetime-local or null
    $endsAt      - initial ends_at formatted for datetime-local or null
--}}

<div class="flex flex-col gap-6">

    {{-- ── Description ─────────────────────────────────────────────── --}}
    <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm">
        <h2 class="text-sm font-semibold text-zinc-800">Basic info</h2>

        <div class="mt-4 flex flex-col gap-4">
            <div class="flex flex-col gap-1.5">
                <label for="description" class="text-sm font-medium text-zinc-700">Description</label>
                <textarea id="description" name="description" rows="2"
                          placeholder="What does this flag control?"
                          class="rounded-lg border border-zinc-300 px-3 py-2 text-sm text-zinc-900 placeholder-zinc-400 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 @error('description') border-red-400 @enderror">{{ old('description', $description ?? '') }}</textarea>
                @error('description')
                    <p class="text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="inline-flex cursor-pointer items-center gap-3">
                    <input type="hidden" name="enabled" value="0">
                    <div class="relative" x-data="{ on: {{ old('enabled', $enabled ?? false) ? 'true' : 'false' }} }">
                        <input type="checkbox" name="enabled" value="1"
                               x-model="on"
                               class="peer sr-only"
                               {{ old('enabled', $enabled ?? false) ? 'checked' : '' }}>
                        <div class="h-5 w-9 rounded-full bg-zinc-300 transition-colors peer-checked:bg-emerald-500 cursor-pointer"
                             @click="on = !on"></div>
                        <div class="absolute left-0.5 top-0.5 h-4 w-4 rounded-full bg-white shadow transition-transform"
                             :class="on ? 'translate-x-4' : 'translate-x-0'"></div>
                    </div>
                    <span class="text-sm font-medium text-zinc-700">Enabled</span>
                </label>
            </div>
        </div>
    </div>

    {{-- ── Attribute rules ──────────────────────────────────────────── --}}
    <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm"
         x-data="rulesBuilder({{ $rulesJson }})"
         x-init="init()">

        <div class="flex items-start justify-between">
            <div>
                <h2 class="text-sm font-semibold text-zinc-800">Audience targeting</h2>
                <p class="mt-0.5 text-xs text-zinc-500">All clauses must match (AND logic). Leave empty to target everyone.</p>
            </div>
            <button type="button" @click="addRule()"
                    class="inline-flex items-center gap-1 rounded-md bg-zinc-100 px-2.5 py-1.5 text-xs font-medium text-zinc-700 hover:bg-zinc-200 transition-colors">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add rule
            </button>
        </div>

        <div class="mt-4 flex flex-col gap-2">
            <template x-for="(rule, index) in rules" :key="index">
                <div class="flex items-center gap-2 rounded-lg border border-zinc-200 bg-zinc-50 px-3 py-2.5">
                    <span class="text-xs font-medium text-zinc-400 w-8 flex-shrink-0" x-text="index === 0 ? 'IF' : 'AND'"></span>

                    <select x-model="rule.attribute"
                            class="rounded-md border border-zinc-300 bg-white px-2.5 py-1.5 text-xs font-medium text-zinc-700 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                        <option value="country">country</option>
                        <option value="role">role</option>
                    </select>

                    <span class="text-xs text-zinc-400 flex-shrink-0">is one of</span>

                    <div class="flex flex-1 flex-wrap items-center gap-1">
                        <template x-for="(val, vi) in rule.values" :key="vi">
                            <span class="inline-flex items-center gap-1 rounded-md bg-violet-100 px-2 py-0.5 text-xs font-medium text-violet-800">
                                <span x-text="val"></span>
                                <button type="button" @click="removeValue(index, vi)" class="text-violet-400 hover:text-violet-700 leading-none">×</button>
                            </span>
                        </template>
                        <input type="text"
                               :placeholder="rule.attribute === 'country' ? 'NL, BE, DE…' : 'admin, mechanic…'"
                               class="min-w-0 w-24 border-0 bg-transparent text-xs text-zinc-700 placeholder-zinc-400 focus:outline-none focus:ring-0"
                               @keydown.enter.prevent="addValue(index, $event.target); $event.target.value = ''"
                               @keydown.comma.prevent="addValue(index, $event.target); $event.target.value = ''"
                               @blur="addValue(index, $event.target); $event.target.value = ''">
                    </div>

                    <button type="button" @click="removeRule(index)"
                            class="ml-auto flex-shrink-0 rounded p-0.5 text-zinc-300 hover:bg-zinc-200 hover:text-zinc-600 transition-colors">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </template>

            <div x-show="rules.length === 0" x-cloak
                 class="rounded-lg border border-dashed border-zinc-200 py-4 text-center text-xs text-zinc-400">
                No targeting rules — flag applies to all users.
            </div>
        </div>

        {{-- Hidden serialised input read by the server --}}
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
                <p class="mt-0.5 text-xs text-zinc-500">Fraction of eligible users who see this feature. Bucketing is sticky per user.</p>
            </div>
            <label class="inline-flex cursor-pointer items-center gap-2 text-xs text-zinc-600">
                <input type="checkbox" x-model="enabled" class="h-3.5 w-3.5 rounded border-zinc-300 text-emerald-600 focus:ring-emerald-500">
                Limit rollout
            </label>
        </div>

        <div x-show="enabled" x-cloak class="mt-4">
            <div class="flex items-center gap-4">
                <input type="range" min="0" max="100" x-model.number="value"
                       class="flex-1 h-2 rounded-full cursor-pointer">
                <div class="flex items-center gap-1 w-20">
                    <input type="number" min="0" max="100" x-model.number="value"
                           class="w-16 rounded-md border border-zinc-300 px-2 py-1 text-center text-sm font-medium focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                    <span class="text-sm text-zinc-500">%</span>
                </div>
            </div>
            <div class="mt-2 flex justify-between text-xs text-zinc-400">
                <span>0% — nobody</span>
                <span x-text="`${value}% of eligible users`"></span>
                <span>100% — everyone</span>
            </div>
        </div>
        <div x-show="!enabled" class="mt-3 text-xs text-zinc-400">100% of eligible users (no limit applied)</div>

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
        addRule() {
            this.rules.push({ attribute: 'country', values: [] });
        },
        removeRule(i) {
            this.rules.splice(i, 1);
        },
        addValue(ruleIndex, input) {
            const val = input.value.replace(/,$/, '').trim();
            if (val && !this.rules[ruleIndex].values.includes(val)) {
                this.rules[ruleIndex].values.push(val);
            }
        },
        removeValue(ruleIndex, valueIndex) {
            this.rules[ruleIndex].values.splice(valueIndex, 1);
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

function percentageSlider(initial) {
    return {
        enabled: initial !== null,
        value: initial ?? 100,
    };
}
</script>
