<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') — Fixico</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        input[type="range"] { accent-color: #059669; }
    </style>
</head>
<body class="min-h-screen antialiased" style="background: #f8f9fa; color: #111;">

    <!-- Accent line -->
    <div class="h-0.5 bg-gradient-to-r from-emerald-500 via-teal-400 to-emerald-600"></div>

    <header class="sticky top-0 z-10 border-b border-zinc-200 bg-white/95 backdrop-blur-sm shadow-sm">
        <div class="mx-auto flex max-w-5xl items-center justify-between px-6 py-3.5">
            <div class="flex items-center gap-6">
                <!-- Logo -->
                <a href="{{ route('admin.flags.index') }}" class="group flex items-center gap-2.5">
                    <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-emerald-600 shadow-sm transition-transform group-hover:scale-105">
                        <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <div class="leading-none">
                        <div class="text-sm font-bold tracking-tight text-zinc-900">Fixico</div>
                        <div class="text-[10px] font-medium uppercase tracking-widest text-zinc-400">Admin</div>
                    </div>
                </a>

                <nav class="flex items-center gap-1">
                    <a href="{{ route('admin.flags.index') }}"
                       class="rounded-md px-3 py-1.5 text-sm font-medium transition-colors
                              {{ request()->routeIs('admin.flags.*') ? 'bg-emerald-50 text-emerald-700' : 'text-zinc-500 hover:bg-zinc-100 hover:text-zinc-700' }}">
                        Feature flags
                    </a>
                </nav>
            </div>

            <a href="http://localhost:3001" class="text-xs text-zinc-400 transition-colors hover:text-zinc-600">
                ← Client app
            </a>
        </div>
    </header>

    <main class="mx-auto max-w-5xl px-6 py-10">

        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-cloak
                 class="mb-6 flex items-center justify-between rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 shadow-sm">
                <div class="flex items-center gap-2.5 text-sm text-emerald-800">
                    <svg class="h-4 w-4 flex-shrink-0 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('success') }}
                </div>
                <button @click="show = false" class="text-emerald-400 hover:text-emerald-700 transition-colors">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        @endif

        @yield('content')

    </main>
</body>
</html>
