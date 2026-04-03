<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />

  <title>{{ config('app.name', 'Flint') }} — Ignite Your Watch List</title>

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet" />

  @vite(['resources/scss/app.scss', 'resources/js/app.js'])
</head>
<body>
  <div class="auth-page">
    <a href="{{ route('home') }}" class="auth-logo">FL<span>I</span>NT</a>
    {{ $slot }}
  </div>
</body>
</html>
