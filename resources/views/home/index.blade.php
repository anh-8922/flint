<x-app-layout>

  {{-- ── Hero ──────────────────────────────────────────────── --}}
  <section class="hero" id="hero">
    <div class="hero-bg loading" id="heroBg"></div>
    <div class="hero-grain"></div>
    <div class="hero-gradient"></div>
    <div class="hero-content" id="heroContent">
      <p class="hero-badge" id="heroBadge">Trending in Movies</p>
      <h1 class="hero-title" id="heroTitle">—</h1>
      <p class="hero-meta" id="heroMeta">&nbsp;</p>
      <p class="hero-overview" id="heroOverview">&nbsp;</p>
      <div class="hero-actions">
        <button class="btn-play" id="btnPlay">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
            <polygon points="5 3 19 12 5 21 5 3"/>
          </svg>
          Play
        </button>
        <button class="btn-info" id="btnMoreInfo">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8" x2="12" y2="12"/>
            <line x1="12" y1="16" x2="12.01" y2="16"/>
          </svg>
          More Info
        </button>
      </div>
    </div>
  </section>

  {{-- ── Trending Row ──────────────────────────────────────── --}}
  <x-movie-row id="trending" title="Trending" />

</x-app-layout>
