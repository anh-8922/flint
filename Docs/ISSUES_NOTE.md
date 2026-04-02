# Issues & Notes

## TMDB API Key Review (2026-02-24)

**Key location:** `.env` → `TMDB_API_KEY`
**API version in use:** v3

### Findings

| Request type | Result |
|---|---|
| `GET ?api_key=...` | Works — confirmed against `/configuration` and `/movie/popular` |
| `GET Authorization: Bearer` | Fails — v3 keys cannot be used as Bearer tokens; v4 Read Access Token (JWT) required |
| `POST ?api_key=...` | Key is recognized, but write endpoints require a user or guest session |

### Decision

v3 with `?api_key=` as a query parameter is sufficient for this project. All read endpoints (popular movies, search, movie details, genres, cast, etc.) are covered.

v4 / Bearer auth is only needed for user account actions (ratings, watchlists) — out of scope for now.

### Implementation

`TmdbService` uses `Http::withQueryParameters(['api_key' => ..., 'language' => 'en-US'])` so the key is appended to every request as a query param automatically.

> **Note:** An earlier version of `TmdbService` used `->withToken($key, 'Bearer')` — this was incorrect for v3 keys and has been corrected.

---

## Phase 2 API Key Exposure (2026-03-04)

**Issue:** Phase 1 served the TMDB API key to the browser via `<meta name="tmdb-key">`, exposing it in page source.

**Fix:** Removed the meta tag. All TMDB calls now go through Laravel proxy routes (`/tmdb/trending`, `/tmdb/search`) backed by `TmdbService`. The API key stays server-side in `.env` only.

**Current data flow:** `Browser → /tmdb/* (Laravel) → TmdbService → TMDB API`

---

## Card Images Loading Slowly — `loading="lazy"` in Overflow Container (2026-03-04)

**Symptom:** Movie card posters in the trending carousel remained as skeletons for a long time after data loaded.

**Root cause:** `loading="lazy"` on `<img>` elements inside an `overflow-x: auto` carousel. Browsers calculate lazy-load eligibility against the main viewport scroll position, not the scroll position of an inner container. As a result, all carousel images — including the initially visible ones — were treated as off-screen and deferred.

**Attempted fix (failed):** Replaced `loading="lazy"` with an `IntersectionObserver`. This introduced a second bug: `observe()` was called inside `buildCard()` before the card was appended to the DOM. The observer fired once immediately with `isIntersecting: false` (disconnected element), then never again — images kept `data-src` and never got a real `src`.

**Actual fix:** Removed `loading="lazy"` and the observer entirely. Set `img.src = poster` directly. For a 20-card carousel of small posters (~20 KB each), eager loading is correct — there is nothing to defer.

**Code location:** `resources/js/flint.js` — `buildCard()`.

---

## Stale Vite Bundle — App Loading Demo Data (2026-03-04)

**Symptom:** App was displaying demo movies despite the TMDB API key being set and `/tmdb/trending` returning valid data.

**Root cause:** `npm run dev` was not running and the production build in `public/build/` predated the creation of `flint.js`. The bundle contained none of the fetch logic, so no API call was ever made.

**Confirmed:** `grep` on the built JS found no references to `tmdb/trending`, `initRow`, or `demoMovies`.

**Fix:** Ran `npm run build` inside the container:

```bash
docker exec flint-laravel.test-1 bash -c "cd /var/www/html && npm run build"
```

**Going forward — development workflow:**

```bash
# Keep Vite HMR running while developing (run in a separate terminal)
docker exec -it flint-laravel.test-1 npm run dev

# Or do a one-off production build before testing
docker exec flint-laravel.test-1 bash -c "cd /var/www/html && npm run build"
```

> After any JS/CSS change, a hard refresh (`Cmd+Shift+R`) is required if running the production build.
