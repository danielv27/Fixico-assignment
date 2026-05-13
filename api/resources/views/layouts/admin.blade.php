<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') — Fixico</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-zinc-50 text-zinc-900 antialiased">

    <header class="border-b border-zinc-200 bg-white px-6 py-3">
        <div class="mx-auto flex max-w-3xl items-center gap-6 text-sm font-medium">
            <span class="text-zinc-400">Fixico Admin</span>
            <a href="{{ route('admin.flags.index') }}" class="hover:text-emerald-600">Feature flags</a>
        </div>
    </header>

    <main class="mx-auto max-w-3xl px-6 py-12">

        @if (session('success'))
            <div class="mb-6 rounded border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        @yield('content')

    </main>

</body>
</html>
