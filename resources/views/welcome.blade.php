<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="refresh" content="0; url={{ route('home') }}">
    <title>{{ config('app.name', 'SIPERMAS') }}</title>
</head>
<body style="font-family: system-ui, -apple-system, sans-serif; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; background-color: #0e1726; color: #e0e6ed;">
    <div style="text-align: center;">
        <h2 style="margin-bottom: 8px;">SIPERMAS</h2>
        <p style="color: #888ea8;">Mengalihkan ke sistem...</p>
        <p><a href="{{ route('home') }}" style="color: #4361ee; text-decoration: none;">Klik di sini jika tidak dialihkan secara otomatis.</a></p>
    </div>
</body>
</html>
