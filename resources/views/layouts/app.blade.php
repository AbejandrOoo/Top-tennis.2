<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Top Tennis Digital') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Figtree', ui-sans-serif, sans-serif; }
        .btn-primary {
            background: linear-gradient(135deg, #4ade80, #22c55e);
            color: #14532d; font-weight: 700;
            padding: .5rem 1.25rem; border-radius: 9999px;
            font-size: .875rem; border: none; cursor: pointer;
            transition: opacity .2s; display: inline-flex; align-items: center; gap: .3rem;
            text-decoration: none;
        }
        .btn-primary:hover { opacity: .85; }
        .btn-outline-sm {
            border: 1.5px solid #d1d5db; color: #374151; font-weight: 600;
            padding: .45rem 1rem; border-radius: 9999px; font-size: .875rem;
            transition: border-color .2s; text-decoration: none; display: inline-flex; align-items: center;
        }
        .btn-outline-sm:hover { border-color: #22c55e; color: #15803d; }
        .btn-danger {
            color: #dc2626; font-size: .875rem; font-weight: 500;
            background: none; border: none; cursor: pointer; text-decoration: none;
        }
        .btn-danger:hover { text-decoration: underline; }
        .card { background: #fff; border-radius: 1rem; box-shadow: 0 1px 8px rgba(0,0,0,.06); }
        .form-input {
            width: 100%; padding: .6rem .85rem;
            border: 1.5px solid #e5e7eb; border-radius: .6rem;
            font-size: .95rem; outline: none; transition: border-color .2s, box-shadow .2s;
        }
        .form-input:focus { border-color: #22c55e; box-shadow: 0 0 0 3px rgba(34,197,94,.12); }
        .form-label { display: block; font-size: .875rem; font-weight: 600; color: #374151; margin-bottom: .4rem; }
        .badge-green  { background:#dcfce7; color:#15803d; }
        .badge-red    { background:#fee2e2; color:#991b1b; }
        .badge-yellow { background:#fef9c3; color:#854d0e; }
        .badge-gray   { background:#f3f4f6; color:#4b5563; }
        .badge { padding:.25rem .65rem; border-radius:9999px; font-size:.75rem; font-weight:600; }
        .page-header {
            background: linear-gradient(135deg, #0d3d22, #14532d);
            padding: 1.5rem 0; margin-bottom: 0;
        }
        .table-header { background: #f0fdf4; }
        th { font-size:.75rem; font-weight:700; color:#15803d; text-transform:uppercase; letter-spacing:.05em; }
        tr:hover td { background:#fafffe; }
        /* Nav active link */
        .nav-active { color:#15803d !important; border-bottom:2.5px solid #22c55e; }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50">

    @include('layouts.navigation')

    {{-- Page Header con degradado verde --}}
    @isset($header)
        <div class="page-header">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </div>
    @endisset

    {{-- Flash messages --}}
    @foreach(['success' => ['bg-green-50 border-green-300 text-green-800', '✓'], 'error' => ['bg-red-50 border-red-300 text-red-800', '✕'], 'warning' => ['bg-yellow-50 border-yellow-300 text-yellow-800', '⚠']] as $type => [$classes, $icon])
        @if(session($type))
            <div x-data="{ show: true }" x-show="show"
                 x-init="setTimeout(() => show = false, {{ $type === 'error' ? 6000 : 4000 }})"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                <div class="flex items-center justify-between border rounded-xl px-4 py-3 {{ $classes }}">
                    <span class="font-medium">{{ $icon }} {{ session($type) }}</span>
                    <button @click="show = false" class="ml-4 opacity-60 hover:opacity-100 text-lg leading-none">✕</button>
                </div>
            </div>
        @endif
    @endforeach

    <main class="pb-12">
        {{ $slot }}
    </main>

</body>
</html>
