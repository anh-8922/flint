{{--
  movie-row component
  Props:
    $id     — unique identifier used to namespace element IDs (e.g. 'trending')
    $title  — section heading text

  IDs produced (consumed by flint.js initRow()):
    {id}Track   — carousel track container
    {id}Dots    — dots indicator container
    {id}PrevBtn — left chevron button
    {id}NextBtn — right chevron button
    {id}Count   — movie count label

  Phase 1: JS populates the track via TMDB fetch or demo data.
  Phase 2: $movies prop added; cards rendered server-side via x-movie-card.
--}}
@props(['id', 'title'])

<section class="movie-row">
  <div class="section-header">
    <h2 class="section-title">{{ $title }}</h2>
    <span class="section-count" id="{{ $id }}Count">Loading...</span>
  </div>

  <div class="carousel-wrap">
    <button class="carousel-btn prev" id="{{ $id }}PrevBtn" aria-label="Scroll left">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
        <polyline points="15 18 9 12 15 6"/>
      </svg>
    </button>

    <div class="carousel-track" id="{{ $id }}Track">
      {{-- Populated by flint.js in Phase 1; server-rendered cards in Phase 2 --}}
    </div>

    <button class="carousel-btn next" id="{{ $id }}NextBtn" aria-label="Scroll right">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
        <polyline points="9 18 15 12 9 6"/>
      </svg>
    </button>
  </div>

  <div class="carousel-dots" id="{{ $id }}Dots"></div>
</section>
