<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $titulo ?? 'Error' }} — Top Tennis</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: ui-sans-serif, system-ui, sans-serif;
            background: #f9fafb;
            color: #1f2937;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .card {
            background: #fff;
            border-radius: 0.75rem;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            padding: 3rem 2.5rem;
            max-width: 480px;
            width: 100%;
            text-align: center;
        }
        .code {
            font-size: 5rem;
            font-weight: 800;
            color: #4f46e5;
            line-height: 1;
            margin-bottom: 0.5rem;
        }
        .titulo { font-size: 1.5rem; font-weight: 700; margin-bottom: 0.75rem; }
        .mensaje { color: #6b7280; margin-bottom: 1.75rem; line-height: 1.6; }
        .btn {
            display: inline-block;
            padding: 0.625rem 1.5rem;
            background: #4f46e5;
            color: #fff;
            border-radius: 0.375rem;
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 600;
            margin: 0.25rem;
        }
        .btn:hover { background: #4338ca; }
        .btn-outline {
            background: transparent;
            border: 1px solid #d1d5db;
            color: #374151;
        }
        .btn-outline:hover { background: #f3f4f6; }
        .emoji { font-size: 3rem; margin-bottom: 1rem; display: block; }
    </style>
</head>
<body>
    <div class="card">
        {{ $slot }}
    </div>
    <p style="margin-top:1.5rem; font-size:0.8rem; color:#9ca3af;">
        🎾 Top Tennis &mdash; Sistema de Reservas
    </p>
</body>
</html>
