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
         x-data="percentageSlider({{ $pct ?? 'null' }}, '{{ $flagName ?? '' }}')">

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

            {{-- ─ Rollout simulator ───────────────────────────────────── --}}
            <div class="rounded-lg border border-zinc-200 bg-zinc-50 p-4">

                {{-- Header --}}
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 text-xs font-semibold text-zinc-700">
                        <svg class="h-3.5 w-3.5 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Rollout simulator
                    </div>
                    <button type="button" @click="generateSamples()"
                            class="text-xs font-medium text-emerald-600 hover:text-emerald-800 transition-colors">
                        + Generate sample users
                    </button>
                </div>
                <p class="mt-0.5 mb-3 text-xs text-zinc-500">
                    Add user IDs below — press <kbd class="rounded border border-zinc-300 bg-white px-1 py-0.5 font-mono text-[10px]">Enter</kbd>
                    or comma to add each one. The grid shows all 100 buckets; the highlighted ones are in this rollout.
                </p>

                {{-- Tag input --}}
                <div class="min-h-[2.5rem] flex flex-wrap gap-1.5 rounded-lg border border-zinc-300 bg-white p-2 focus-within:border-emerald-500 focus-within:ring-1 focus-within:ring-emerald-500">
                    <template x-for="(subject, i) in subjects" :key="i">
                        <span class="inline-flex items-center gap-1 rounded-md px-2 py-0.5 text-xs font-medium ring-1 ring-inset"
                              :class="isInRollout(subject)
                                  ? 'bg-emerald-50 text-emerald-800 ring-emerald-200'
                                  : 'bg-zinc-100 text-zinc-600 ring-zinc-200'">
                            <span x-text="subject"></span>
                            <span class="text-[10px] opacity-60" x-text="`#${getBucket(subject)}`"></span>
                            <button type="button" @click="removeSubject(i)"
                                    class="ml-0.5 opacity-50 hover:opacity-100 leading-none">&times;</button>
                        </span>
                    </template>
                    <input type="text" x-model="tagInput"
                           placeholder="user-id or email…"
                           @keydown.enter.prevent="addTag()"
                           @keydown.comma.prevent="addTag()"
                           @keydown.backspace="tagInput === '' && subjects.length && removeSubject(subjects.length - 1)"
                           class="min-w-[140px] flex-1 border-0 bg-transparent p-0.5 text-xs placeholder-zinc-400 focus:outline-none focus:ring-0">
                </div>

                {{-- Bucket grid --}}
                <div x-show="getSlug()" class="mt-4">
                    <div class="mb-2 flex items-center justify-between text-xs text-zinc-500">
                        <span>100 buckets — <span class="font-medium text-emerald-700" x-text="`${value} in rollout`"></span>, <span x-text="`${100 - value} excluded`"></span></span>
                        <div class="flex items-center gap-3">
                            <span class="flex items-center gap-1"><span class="inline-block h-2.5 w-2.5 rounded-sm bg-emerald-400"></span> in rollout</span>
                            <span class="flex items-center gap-1"><span class="inline-block h-2.5 w-2.5 rounded-sm bg-zinc-200"></span> excluded</span>
                            <span class="flex items-center gap-1"><span class="inline-block h-2.5 w-2.5 rounded-full bg-violet-500"></span> your users</span>
                        </div>
                    </div>
                    <div class="grid gap-0.5" style="grid-template-columns: repeat(10, 1fr)">
                        <template x-for="b in 100" :key="b">
                            <div class="group relative flex h-7 cursor-default items-center justify-center rounded-sm text-[9px] font-medium transition-colors"
                                 :class="(b-1) < value
                                     ? (subjectsInBucket(b-1).length ? 'bg-emerald-300 text-emerald-900' : 'bg-emerald-100 text-emerald-600')
                                     : (subjectsInBucket(b-1).length ? 'bg-violet-100 text-violet-700' : 'bg-zinc-200 text-zinc-400')"
                                 :title="subjectsInBucket(b-1).length ? subjectsInBucket(b-1).join(', ') + ` (bucket ${b-1})` : `Bucket ${b-1}`">
                                <span x-text="b-1"></span>
                                <span x-show="subjectsInBucket(b-1).length > 0"
                                      class="absolute right-0.5 top-0.5 h-1.5 w-1.5 rounded-full bg-violet-500"></span>
                                {{-- Tooltip for cells with users --}}
                                <div x-show="subjectsInBucket(b-1).length > 0"
                                     class="pointer-events-none absolute bottom-8 left-1/2 z-20 hidden -translate-x-1/2 whitespace-nowrap rounded-md bg-zinc-900 px-2 py-1 text-[10px] text-white group-hover:block">
                                    <template x-for="s in subjectsInBucket(b-1)" :key="s">
                                        <div x-text="s"></div>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Results table --}}
                <div x-show="subjects.length > 0 && getSlug()" x-cloak class="mt-4">
                    <div class="overflow-hidden rounded-lg border border-zinc-200">
                        <table class="w-full text-xs">
                            <thead>
                                <tr class="border-b border-zinc-100 bg-zinc-50">
                                    <th class="px-3 py-2 text-left font-medium text-zinc-500">User</th>
                                    <th class="px-3 py-2 text-center font-medium text-zinc-500">Bucket</th>
                                    <th class="px-3 py-2 text-left font-medium text-zinc-500">Result</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100 bg-white">
                                <template x-for="(subject, i) in subjects" :key="i">
                                    <tr>
                                        <td class="px-3 py-2 font-mono font-medium text-zinc-800" x-text="subject"></td>
                                        <td class="px-3 py-2 text-center tabular-nums text-zinc-500" x-text="getBucket(subject)"></td>
                                        <td class="px-3 py-2">
                                            <span class="inline-flex items-center gap-1 font-medium"
                                                  :class="isInRollout(subject) ? 'text-emerald-700' : 'text-zinc-400'">
                                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path x-show="isInRollout(subject)" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                    <path x-show="!isInRollout(subject)" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                                <span x-text="isInRollout(subject) ? 'In rollout' : 'Excluded'"></span>
                                                <span class="font-normal opacity-60"
                                                      x-text="isInRollout(subject) ? `(${getBucket(subject)} < ${value})` : `(${getBucket(subject)} ≥ ${value})`"></span>
                                            </span>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                    <p class="mt-2 text-right text-xs text-zinc-400"
                       x-text="`${subjects.filter(s => isInRollout(s)).length} of ${subjects.length} users in rollout (${value}% threshold)`"></p>
                </div>

                <p x-show="!getSlug()" x-cloak class="mt-3 text-xs text-amber-600">
                    Enter the flag name above to enable the simulator.
                </p>
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
// CRC32 matching PHP's crc32() — used to replicate the evaluator's bucket
// calculation client-side without a network call.
// ---------------------------------------------------------------------------
const _crc32Table = (() => {
    const t = new Uint32Array(256);
    for (let i = 0; i < 256; i++) {
        let c = i;
        for (let j = 0; j < 8; j++) {
            c = (c & 1) ? (0xEDB88320 ^ (c >>> 1)) : (c >>> 1);
        }
        t[i] = c;
    }
    return t;
})();

function phpCrc32(str) {
    const bytes = new TextEncoder().encode(str);
    let crc = 0xFFFFFFFF;
    for (const b of bytes) crc = _crc32Table[(crc ^ b) & 0xFF] ^ (crc >>> 8);
    const unsigned = (crc ^ 0xFFFFFFFF) >>> 0;
    // Replicate PHP's signed 32-bit integer return
    return unsigned > 0x7FFFFFFF ? unsigned - 0x100000000 : unsigned;
}

function getBucket(subject, flagName) {
    return Math.abs(phpCrc32(subject + ':' + flagName)) % 100;
}

// ---------------------------------------------------------------------------
// Percentage slider + batch rollout simulator
// ---------------------------------------------------------------------------
function percentageSlider(initial, serverFlagName) {
    return {
        enabled: initial !== null,
        value: initial ?? 100,
        _serverFlagName: serverFlagName,

        // Tag input state
        tagInput: '',
        subjects: [],

        // On create the name is typed live; on edit it's fixed.
        getSlug() {
            if (this._serverFlagName) return this._serverFlagName;
            return document.getElementById('name')?.value?.trim() || '';
        },

        getBucket(subject) {
            return getBucket(subject, this.getSlug());
        },

        isInRollout(subject) {
            return this.getBucket(subject) < this.value;
        },

        subjectsInBucket(b) {
            return this.subjects.filter(s => this.getBucket(s) === b);
        },

        addTag() {
            const val = this.tagInput.replace(/,\s*$/, '').trim();
            if (val && !this.subjects.includes(val)) {
                this.subjects.push(val);
            }
            this.tagInput = '';
        },

        removeSubject(i) {
            this.subjects.splice(i, 1);
        },

        generateSamples() {
            const samples = Array.from({ length: 20 }, (_, i) => `user-${i + 1}`);
            samples.forEach(s => {
                if (!this.subjects.includes(s)) this.subjects.push(s);
            });
        },
    };
}
</script>
