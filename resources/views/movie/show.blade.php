@php
  $backdrop  = $movie['backdrop_path']
    ? config('services.tmdb.image_url') . '/original' . $movie['backdrop_path']
    : null;
  $poster    = $movie['poster_path']
    ? config('services.tmdb.image_url') . '/w500' . $movie['poster_path']
    : null;
  $year      = isset($movie['release_date']) ? substr($movie['release_date'], 0, 4) : '—';
  $rating    = isset($movie['vote_average']) ? number_format($movie['vote_average'], 1) : '—';
  $runtime   = isset($movie['runtime']) && $movie['runtime']
    ? floor($movie['runtime'] / 60) . 'h ' . ($movie['runtime'] % 60) . 'm'
    : null;
  $genres    = collect($movie['genres'] ?? [])->pluck('name')->join(' · ');
@endphp

<x-app-layout>

  <x-breadcrumb :items="[
    ['label' => 'Home',   'url' => route('home')],
    ['label' => 'Movies', 'url' => route('home')],
    ['label' => $movie['title'], 'url' => '#'],
  ]" />

  {{-- ── Backdrop Hero ─────────────────────────────────────── --}}
  <section class="detail-hero" @if($backdrop) style="--backdrop:url('{{ $backdrop }}')" @endif>
    <div class="detail-hero-bg"></div>
    <div class="detail-hero-gradient"></div>
    <div class="detail-hero-content">

      @if($poster)
        <img class="detail-poster" src="{{ $poster }}" alt="{{ $movie['title'] }}" />
      @endif

      <div class="detail-info">
        <p class="hero-badge">{{ $genres ?: 'Movie' }}</p>
        <h1 class="detail-title">{{ $movie['title'] }}</h1>

        @if(!empty($movie['tagline']))
          <p class="detail-tagline">{{ $movie['tagline'] }}</p>
        @endif

        <div class="detail-meta">
          <span>★ {{ $rating }}</span>
          <span>{{ $year }}</span>
          @if($runtime) <span>{{ $runtime }}</span> @endif
          @if($director) <span>Dir. {{ $director['name'] }}</span> @endif
        </div>

        <p class="detail-overview">{{ $movie['overview'] }}</p>

        <div class="hero-actions">
          @if($trailer)
            <a href="#trailer" class="btn-play">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"/></svg>
              Play Trailer
            </a>
          @endif
          <a href="{{ route('home') }}" class="btn-info">← Back</a>
        </div>
      </div>

    </div>
  </section>

  {{-- ── Trailer ───────────────────────────────────────────── --}}
  @if($trailer)
    <section class="detail-section" id="trailer">
      <div class="section-header">
        <h2 class="section-title">Trailer</h2>
      </div>
      <div class="detail-player-wrap">
        <iframe
          class="detail-player"
          src="https://www.youtube.com/embed/{{ $trailer['key'] }}?rel=0&modestbranding=1"
          title="{{ $trailer['name'] }}"
          allowfullscreen
          allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture">
        </iframe>
      </div>
    </section>
  @endif

  {{-- ── Cast ─────────────────────────────────────────────── --}}
  @if(!empty($cast))
    <section class="detail-section">
      <div class="section-header">
        <h2 class="section-title">Cast</h2>
      </div>
      <div class="cast-track">
        @foreach($cast as $person)
          @php
            $photo = $person['profile_path']
              ? config('services.tmdb.image_url') . '/w185' . $person['profile_path']
              : null;
          @endphp
          <div class="cast-card">
            <div class="cast-photo" @unless($photo) style="background:linear-gradient(135deg,#1a1008,#111)" @endunless>
              @if($photo)
                <img src="{{ $photo }}" alt="{{ $person['name'] }}" loading="lazy" />
              @else
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" opacity="0.3"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
              @endif
            </div>
            <p class="cast-name">{{ $person['name'] }}</p>
            <p class="cast-character">{{ $person['character'] }}</p>
          </div>
        @endforeach
      </div>
    </section>
  @endif

  {{-- ── Recommendations ──────────────────────────────────── --}}
  @if(!empty($recommendations))
    <section class="detail-section">
      <div class="section-header">
        <h2 class="section-title">You May Also Like</h2>
      </div>
      <div class="carousel-wrap">
        <div class="carousel-track" id="recoTrack">
          @foreach($recommendations as $rec)
            <x-movie-card :movie="$rec" />
          @endforeach
        </div>
      </div>
    </section>
  @endif

</x-app-layout>
