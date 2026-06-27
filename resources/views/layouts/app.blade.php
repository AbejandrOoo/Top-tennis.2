<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Top Tennis') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        :root {
            --green:      #15803d;
            --green-dark: #166534;
            --green-xd:   #14532d;
        }

        * { box-sizing: border-box; }
        body { font-family: 'Figtree', ui-sans-serif, sans-serif; background: #f5f7f5; }
        [x-cloak] { display: none !important; }

        /* ── Botones ─────────────────────────────────────────── */
        .btn-primary {
            display: inline-flex; align-items: center; gap: .35rem;
            background: var(--green); color: #fff; font-weight: 700;
            padding: .55rem 1.4rem; border-radius: 9999px;
            font-size: .875rem; border: none; cursor: pointer;
            text-decoration: none;
            transition: background .18s, transform .12s, box-shadow .18s;
            box-shadow: 0 2px 8px rgba(21,128,61,.25);
        }
        .btn-primary:hover {
            background: var(--green-dark);
            box-shadow: 0 4px 14px rgba(21,128,61,.35);
            transform: translateY(-1px);
        }
        .btn-primary:active { transform: translateY(0); box-shadow: none; }

        .btn-outline-sm {
            display: inline-flex; align-items: center;
            border: 1.5px solid #d1d5db; color: #4b5563; font-weight: 600;
            padding: .5rem 1.1rem; border-radius: 9999px; font-size: .875rem;
            text-decoration: none; background: #fff;
            transition: border-color .18s, color .18s, box-shadow .18s;
        }
        .btn-outline-sm:hover {
            border-color: var(--green); color: var(--green);
            box-shadow: 0 2px 8px rgba(21,128,61,.1);
        }

        .btn-danger {
            color: #dc2626; font-size: .875rem; font-weight: 600;
            background: none; border: none; cursor: pointer;
            text-decoration: none; transition: color .15s;
        }
        .btn-danger:hover { color: #991b1b; }

        /* ── Tarjetas ─────────────────────────────────────────── */
        .card {
            background: #fff;
            border-radius: 1.125rem;
            border: 1px solid #e9ede9;
            box-shadow: 0 1px 4px rgba(0,0,0,.04), 0 4px 16px rgba(0,0,0,.03);
        }

        /* ── Formularios ─────────────────────────────────────── */
        .form-input {
            width: 100%;
            padding: .65rem 1rem;
            border: 1.5px solid #e2e8e2;
            border-radius: .75rem;
            font-size: .95rem;
            color: #111827;
            background: #fff;
            outline: none;
            transition: border-color .18s, box-shadow .18s;
        }
        .form-input:focus {
            border-color: var(--green);
            box-shadow: 0 0 0 3px rgba(21,128,61,.1);
        }
        .form-input:disabled { background: #f9fafb; color: #9ca3af; }
        .form-label {
            display: block; font-size: .875rem; font-weight: 600;
            color: #374151; margin-bottom: .45rem;
        }

        /* ── Badges ─────────────────────────────────────────── */
        .badge {
            display: inline-flex; align-items: center;
            padding: .2rem .7rem; border-radius: 9999px;
            font-size: .72rem; font-weight: 700; letter-spacing: .02em;
        }
        .badge-green  { background: #dcfce7; color: #166534; }
        .badge-red    { background: #fee2e2; color: #991b1b; }
        .badge-yellow { background: #fef3c7; color: #92400e; }
        .badge-gray   { background: #f3f4f6; color: #4b5563; }
        .badge-blue   { background: #dbeafe; color: #1e40af; }

        /* ── Tablas ─────────────────────────────────────────── */
        .table-header { background: #f9faf9; }
        th {
            font-size: .72rem; font-weight: 700; color: #6b7280;
            text-transform: uppercase; letter-spacing: .07em;
        }
        tbody tr { border-bottom: 1px solid #f0f2f0; }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover td { background: #f9fbf9; }

        /* ── Page Header ─────────────────────────────────────── */
        .page-header {
            background: linear-gradient(135deg, #0d3d22 0%, #155e36 50%, #166534 100%);
            padding: 1.25rem 0;
            border-bottom: 1px solid rgba(255,255,255,.06);
        }

        /* ── Flash messages ─────────────────────────────────── */
        .flash-success { background:#f0fdf4; border-color:#86efac; color:#15803d; }
        .flash-error   { background:#fef2f2; border-color:#fca5a5; color:#dc2626; }
        .flash-warning { background:#fffbeb; border-color:#fcd34d; color:#b45309; }

        /* ── Stat cards ─────────────────────────────────────── */
        .stat-card {
            background: #fff;
            border-radius: 1.125rem;
            border: 1px solid #e9ede9;
            padding: 1.5rem;
            transition: box-shadow .2s, transform .2s;
        }
        .stat-card:hover {
            box-shadow: 0 4px 20px rgba(0,0,0,.08);
            transform: translateY(-2px);
        }

        /* ── Scrollbar sutil ─────────────────────────────────── */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 9999px; }
        ::-webkit-scrollbar-thumb:hover { background: #9ca3af; }
    </style>
</head>
<body class="font-sans antialiased">

    @include('layouts.navigation')

    {{-- Page Header --}}
    @isset($header)
        <div class="page-header">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </div>
    @endisset

    {{-- Flash Messages --}}
    @foreach([
        'success' => ['flash-success', '<svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>'],
        'error'   => ['flash-error',   '<svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>'],
        'warning' => ['flash-warning', '<svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>'],
    ] as $type => [$classes, $icon])
        @if(session($type))
            <div x-data="{ show: true }" x-show="show"
                 x-init="setTimeout(() => show = false, {{ $type === 'error' ? 6000 : 4000 }})"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                <div class="flex items-center justify-between border rounded-xl px-4 py-3 {{ $classes }}">
                    <div class="flex items-center gap-2.5">
                        {!! $icon !!}
                        <span class="font-semibold text-sm">{{ session($type) }}</span>
                    </div>
                    <button @click="show = false" class="ml-4 opacity-50 hover:opacity-100 transition-opacity">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
        @endif
    @endforeach

    <main class="pb-16">
        {{ $slot }}
    </main>

</body>
</html>
