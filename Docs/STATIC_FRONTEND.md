# Flint — Static Frontend

> **Phase:** 1 → 2 (Phase 2 complete)
> **Status:** Phase 2 backend integration done
> **Design source:** `Docs/flint.html` (single-file prototype)

---

## What Was Built

### Phase 1 — Static Frontend

The `flint.html` prototype was decomposed into a proper Laravel frontend: Blade layout, reusable components, scoped CSS, and modular JS — wired up for client-side TMDB fetches using a key served via `<meta>` tag.

### Phase 2 — Backend API Integration

All TMDB fetches moved server-side through `TmdbService`. The API key no longer reaches the browser. `flint.js` now calls Laravel proxy routes (`/tmdb/*`) instead of hitting TMDB directly.

---

## Files Created / Modified

| File | Action | Purpose |
|---|---|---|
| `config/services.php` | Modified | Added `tmdb` block — reads key/urls from `.env` |
| `resources/css/app.css` | Modified | Replaced Breeze defaults with full Flint CSS |
| `resources/js/flint.js` | Created | UI logic: topbar scroll, sidebar, search, carousel, API fetch |
| `resources/js/app.js` | Modified | Added `import './flint'` |
| `resources/views/layouts/app.blade.php` | Modified | Replaced Breeze layout with Flint base layout |
| `resources/views/components/topbar.blade.php` | Created | Fixed topbar — hamburger, logo, search, sign in |
| `resources/views/components/sidebar.blade.php` | Created | Slide-in nav — Home, Categories, News, My List, Login |
| `resources/views/components/search-bar.blade.php` | Created | Slide-down search input |
| `resources/views/components/footer.blade.php` | Created | Footer — links + TMDB attribution |
| `resources/views/components/movie-card.blade.php` | Created | Movie card (Phase 3 server-side use) |
| `resources/views/components/movie-row.blade.php` | Created | Carousel row — accepts `id` + `title` props |
| `resources/views/home/index.blade.php` | Created | Home page — hero skeleton + trending row |
| `routes/web.php` | Modified | `/` → `home.index`; `/tmdb/trending` + `/tmdb/search` proxy routes |
| `app/Services/TmdbService.php` | Created | All TMDB API calls, `api_key` as query param |
| `app/Http/Controllers/TmdbController.php` | Created | Thin controller delegating to TmdbService |

---

## Architecture

### Layout

```
layouts/app.blade.php
├── Google Fonts (Bebas Neue + Outfit)
├── @vite (app.css + app.js)
├── overlay#overlay
├── x-sidebar
├── x-search-bar
├── x-topbar
├── <main>{{ $slot }}</main>
└── x-footer
```

> Phase 1 had `<meta name="tmdb-key">` here — removed in Phase 2.

### JS Module Chain

```
app.js
├── import './bootstrap'
├── import './flint'           ← all Flint UI logic
└── Alpine.start()
```

### Component ID Convention

`movie-row` accepts an `id` prop and produces namespaced element IDs:

```
id="trending" →  trendingTrack  trendingDots  trendingPrevBtn  trendingNextBtn  trendingCount
```

`flint.js` calls `initRow({ id, endpoint })` which uses these IDs to wire up the carousel.

---

## Data Flow (Phase 2 — Current)

```
Browser → flint.js initRow({ endpoint: '/tmdb/trending' })
        → fetch /tmdb/trending  (same-origin, X-Requested-With header)
        → TmdbController@trending
        → TmdbService::trending()
        → TMDB API (api_key as query param, server-side)
        → JSON { results: [...] } returned to JS
        → renderHero(movies[0])
        → buildCard() × N → append to #trendingTrack
        → buildDots() → #trendingDots

Search  → flint.js doSearch(query)  [debounced 350ms]
        → fetch /tmdb/search?query=...
        → TmdbController@search
        → TmdbService::search($query)
        → results rendered as dropdown in .search-results

Failure → any fetch error falls back to 8 hardcoded demoMovies
```

---

## Phase 2 Migration — Completed Steps

- [x] Remove `<meta name="tmdb-key">` from `layouts/app.blade.php`
- [x] Create `TmdbService` — wraps all TMDB endpoints, `api_key` via `withQueryParameters`
- [x] Create `TmdbController` — proxy routes delegating to `TmdbService`
- [x] Add `/tmdb/trending` and `/tmdb/search` routes to `routes/web.php`
- [x] Replace `tmdb()` fetch in `flint.js` with `api()` targeting Laravel routes
- [x] Remove `TMDB_KEY`, `TMDB_BASE`, `DEMO_MODE` constants from `flint.js`
- [x] Wire live search: debounced input → `/tmdb/search` → dropdown results

---

## Phase 3 Migration Path (Next)

When controllers pass data server-side:

1. `HomeController` calls `TmdbService::trending()`, passes `$movies` to `home.index`
2. `movie-row.blade.php` gains a `$movies` prop and renders `<x-movie-card>` server-side
3. `movie-card.blade.php` already accepts a `$movie` array prop — ready to use
4. `flint.js` retains only UI interaction code (sidebar, topbar scroll, search, carousel)
5. `initRow()` can be removed once rows are server-rendered

---

## How to Run

```bash
# Start Docker
sail up -d

# Watch assets (keep running in a separate terminal)
sail npm run dev

# Visit
open http://localhost:8088
```

With `TMDB_API_KEY` set in `.env`, the hero and trending carousel load live data via the Laravel proxy. Without it (or on any API failure), demo mode activates — all UI interactions work identically.

---

*Static Frontend v2.0 — Phase 2 backend integration complete*
