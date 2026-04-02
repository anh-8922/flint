{{--
  movie-card component
  Props:
    $movie  — array with keys: id, title, poster_path, release_date, vote_average
    $size   — image size (default 'w342')

  Phase 1: JS builds cards dynamically via buildCard() in flint.js.
  Phase 2: This component will be used server-side in movie-row and browse grids.
--}}
@props(['movie', 'size' => 'w342'])

@php
  $poster  = $movie['poster_path']
    ? config('services.tmdb.image_url') . "/{$size}" . $movie['poster_path']
    : null;
  $year    = isset($movie['release_date']) ? substr($movie['release_date'], 0, 4) : '—';
  $rating  = isset($movie['vote_average']) ? number_format($movie['vote_average'], 1) : '—';
  $bgStyle = $poster ? '' : 'background:linear-gradient(135deg,#1a1008,#111);';
@endphp

<a class="movie-card" href="{{ route('movie.show', $movie['id']) }}" style="text-decoration:none;color:inherit;">
  <div class="card-poster {{ $poster ? '' : 'skeleton' }}" style="{{ $bgStyle }}">
    @if ($poster)
      <img src="{{ $poster }}" alt="{{ $movie['title'] }}" loading="lazy" />
    @endif
    <div class="card-rating">★ {{ $rating }}</div>
    <div class="card-overlay">
      <div class="card-overlay-title">{{ $movie['title'] }}</div>
      <div class="card-overlay-meta">{{ $year }}</div>
    </div>
  </div>
  <div class="card-info">
    <div class="card-title">{{ $movie['title'] }}</div>
    <div class="card-year">{{ $year }}</div>
  </div>
</a>
