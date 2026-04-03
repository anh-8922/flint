<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />

  <title>{{ $title ?? config('app.name', 'Flint') }} — Ignite Your Watch List</title>

  {{-- Fonts --}}
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet" />

  @vite(['resources/scss/app.scss', 'resources/js/app.js'])
</head>
<body>

  <div class="overlay" id="overlay"></div>

  <x-sidebar />
  <x-search-bar />
  <x-topbar />

  <main>
    {{ $slot }}
  </main>

  <x-footer />

</body>
</html>
