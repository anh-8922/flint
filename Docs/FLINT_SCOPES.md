# Flint — Laravel Rebuild: Project Scope & Plan

> **Learning context:** Laravel From Scratch (Packt) — but instead of Workopia, this is our real project.  
> **Goal:** Rebuild Flint (a Netflix-style movie discovery app) in Laravel, with a real backend, database, and API integration — making it significantly more robust than the original vanilla JS version.

---

## 1. What Is Flint?

A movie discovery and personal tracking app. Users can browse movies (pulled live from TMDB), watch trailers, save a personal watchlist, and leave reviews. The name **Flint** references the spark that starts a fire — the moment a great film ignites something in you. Short, sharp, and memorable.

**Original app (vanilla JS, Netlify):**
- Hardcoded or TMDB-powered movie cards
- Nav: Home, Movies, News & Popular, My List, Browse by Languages
- Trailer modal (YouTube embed)
- No real backend — "My List" likely stored in localStorage
- No real auth — user identity was cosmetic

**Our rebuild (Laravel, Docker):**
- Full Laravel MVC backend
- Real database — users, watchlists, reviews stored in MySQL
- TMDB API integration (live data)
- Auth system (register, login, profile)
- Admin dashboard for content management
- Reviews & ratings per user

---

## 2. Tech Stack

| Layer | Technology |
|---|---|
| Framework | Laravel 11 |
| Frontend | Blade templates + Tailwind CSS + Alpine.js (light interactivity) |
| Database | MySQL 8 (via Docker) |
| External API | TMDB API (free tier) |
| Local environment | Laravel Sail (Docker) |
| Auth | Laravel Breeze (simple, Blade-based) |
| HTTP Client | Laravel's built-in `Http` facade (wraps Guzzle) |
| Queue (later) | Laravel Queue + Redis (for background jobs if needed) |

> **Why Breeze over Jetstream?** Breeze is Blade-based and minimal — perfect for learning and for this course scope. Jetstream adds too much complexity too early.

---

## 3. Pages & Features

> **Navigation (sidebar):** Home · Categories · News · My List · Login  
> These are the confirmed nav items from the UI design. Routes are structured accordingly.

### 3.1 Public Pages (no login required)

| Page | Route | Description |
|---|---|---|
| Home | `/` | Hero banner (trending #1 or admin-featured), trending carousel, popular row |
| Categories | `/categories` | Browse by genre — grid of genre tiles |
| Category Detail | `/categories/{genre}` | Movies filtered by genre (e.g. `/categories/action`) |
| Movies | `/movies` | Full browse grid — filterable by genre, language, year, sort |
| Movie Detail | `/movies/{id}` | Full detail: backdrop, poster, overview, cast row, trailer, reviews |
| News | `/news` | TMDB "Now Playing" + "Trending This Week" — two-section layout |
| Browse by Language | `/movies?language=xx` | Query param filter on the Movies grid |
| Search | `/search?q=...` | Search results grid via TMDB search endpoint |

### 3.2 Auth Pages (handled by Laravel Breeze)

| Page | Route | Description |
|---|---|---|
| Register | `/register` | Name, email, password |
| Login | `/login` | Email + password |
| Logout | `POST /logout` | Destroys session, redirects home |
| Forgot Password | `/forgot-password` | Sends reset link (Breeze default) |
| Reset Password | `/reset-password/{token}` | Password reset form (Breeze default) |

### 3.3 Authenticated User Pages

| Page | Route | Description |
|---|---|---|
| My List | `/my-list` | Personal watchlist — card grid + empty state |
| My Reviews | `/my-reviews` | All reviews written by this user, with edit/delete |
| Profile | `/profile` | View/edit display name, avatar, bio |

### 3.4 Admin Pages (role-gated: `admin` middleware)

| Page | Route | Description |
|---|---|---|
| Admin Dashboard | `/admin` | Stats: user count, review count, featured movie |
| Manage Users | `/admin/users` | List all users, toggle active/disabled, promote to admin |
| Manage Reviews | `/admin/reviews` | List all reviews, delete any |
| Featured Movie | `/admin/featured` | Set hero banner by TMDB ID — preview before saving |
| Curated Lists | `/admin/lists` | Create/edit named collections (e.g. "Staff Picks") |
| Curated List Detail | `/admin/lists/{list}` | Add/remove/reorder movies in a list |

---

## 4. Database Schema

### `users`
```
id, name, email, password, avatar, bio, role (user|admin), created_at, updated_at
```

### `watchlists`
```
id, user_id (FK), tmdb_id, media_type (movie|tv), added_at
```
> `tmdb_id` is the TMDB movie/show ID. We store just the ID — we don't duplicate TMDB data in our DB.

### `reviews`
```
id, user_id (FK), tmdb_id, media_type, rating (1-10), body (text), created_at, updated_at
```

### `featured_movies`
```
id, tmdb_id, set_by (user_id FK), active (boolean), created_at
```
> Only one active at a time. Admin sets which TMDB movie ID appears as the hero.

### `curated_lists`
```
id, name, slug, description, created_by (user_id FK), created_at
```

### `curated_list_items`
```
id, curated_list_id (FK), tmdb_id, sort_order
```

> **Key design decision:** We are not mirroring the full TMDB database. We store only IDs and user-generated data. All movie metadata (title, poster, overview, etc.) is fetched live from TMDB and cached in Laravel's cache layer.

---

## 5. TMDB API Integration Plan

### 5.1 Free API Access
Sign up at https://www.themoviedb.org/settings/api — the free tier gives you full access to all endpoints we need.

### 5.2 Endpoints We'll Use

| Feature | TMDB Endpoint |
|---|---|
| Trending movies | `GET /trending/movie/week` |
| Popular movies | `GET /movie/popular` |
| Now playing | `GET /movie/now_playing` |
| Movie detail | `GET /movie/{id}` |
| Movie credits | `GET /movie/{id}/credits` |
| Trailers/videos | `GET /movie/{id}/videos` |
| Search | `GET /search/movie?query=...` |
| By language | `GET /discover/movie?with_original_language=xx` |
| Movie images | `GET /movie/{id}/images` |

### 5.3 Laravel Service Class

All TMDB calls go through `TmdbService`:

```
app/Services/TmdbService.php
```

This class:
- Reads the API key from `.env` via `config('services.tmdb.key')`
- Sends `api_key` as a query parameter on every request (`withQueryParameters`)
- Wraps all API calls using Laravel's `Http` facade
- Returns raw TMDB response arrays

**Browser access via `TmdbController`:**

```
app/Http/Controllers/TmdbController.php
```

Routes at `/tmdb/trending` and `/tmdb/search` delegate to `TmdbService`. The API key never leaves the server.

> **Caching:** `Cache::remember()` is planned but not yet implemented. All calls currently hit TMDB on every request.

### 5.4 Caching Strategy

| Endpoint | Cache TTL |
|---|---|
| Trending / Popular | 60 minutes |
| Movie detail | 24 hours |
| Search results | 15 minutes |
| Trailers | 24 hours |

Cache key pattern: `tmdb.movie.{id}`, `tmdb.trending.week`, etc.

---

## 6. Feature Breakdown by Phase

### Phase 1 — Foundation (Course: S01–S04)
- [x] Laravel Sail (Docker) project setup
- [x] CSS + Alpine.js configured via Vite
- [x] Base layout: `layouts/app.blade.php`
- [x] Navbar component with nav links
- [x] Footer component
- [x] `.env` configured with TMDB API key + DB credentials
- [x] `TmdbService` created (`app/Services/TmdbService.php`)
- [x] `TmdbController` created — proxy routes for browser → Laravel → TMDB
- [x] TMDB API key removed from browser (Phase 2 security fix)

### Phase 2 — Browse & Discovery (Course: S05–S06)
- [ ] Home page with hero banner (featured movie or trending #1)
- [ ] Horizontal movie row component (reusable)
- [ ] Movie card component (poster, title, rating badge)
- [ ] Trailer modal component (YouTube embed via Alpine.js)
- [ ] Movies browse page (grid layout)
- [ ] Movie detail page (full info, cast, trailer)
- [ ] Browse by Language filter
- [ ] Search page + results

### Phase 3 — Database & Models (Course: S07)
- [ ] Migrations: `users`, `watchlists`, `reviews`, `featured_movies`, `curated_lists`, `curated_list_items`
- [ ] Eloquent models with relationships
- [ ] Factories & seeders (seed test users, sample reviews, sample featured movie)

### Phase 4 — Auth System (Course: S08–S09)
- [ ] Install Laravel Breeze
- [ ] Register / Login / Logout
- [ ] Profile page (edit name, bio, avatar upload)
- [ ] Route middleware: protect authenticated routes
- [ ] Auth-aware navbar (show "My List", user name when logged in)

### Phase 5 — Watchlist / My List (Course: S09–S10)
- [ ] Add to / remove from My List (AJAX with Alpine.js or simple form POST)
- [ ] My List page (fetch TMDB data for each saved `tmdb_id`)
- [ ] Heart/bookmark icon state on movie cards (filled if in list)

### Phase 6 — Reviews & Ratings (Course: S10–S11)
- [ ] Review form on movie detail page (rating 1–10 + text body)
- [ ] Display reviews section on movie detail page
- [ ] Average rating display (calculated from DB)
- [ ] My Reviews page (user's own reviews, with edit/delete)
- [ ] Validation: one review per user per movie

### Phase 7 — Admin Dashboard (Course: S11–S12)
- [ ] Admin role on `users` table + `IsAdmin` middleware
- [ ] Admin dashboard page (user count, review count, etc.)
- [ ] Manage Users: list all users, toggle active/disabled
- [ ] Moderate Reviews: list all reviews, delete any
- [ ] Set Featured Movie: input TMDB ID → preview → save as hero
- [ ] Curated Lists: create lists, add/remove movies by TMDB ID

### Phase 8 — Polish (Post-course)
- [ ] Pagination on browse pages and admin tables
- [ ] Toast notifications (success/error feedback)
- [ ] 404 / error pages styled to match the app
- [ ] SEO meta tags per movie detail page
- [ ] Responsive design pass (mobile nav, card grids)
- [ ] Basic rate limiting on review submissions

---

## 7. Routes Overview

All routes live in `routes/web.php`. Named routes are used throughout so views and redirects never hardcode URLs.

```php
<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\WatchlistController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminReviewController;
use App\Http\Controllers\Admin\FeaturedController;
use App\Http\Controllers\Admin\AdminListController;

// ── TMDB Proxy (browser → Laravel → TMDB API) ───────────────────
Route::prefix('tmdb')->name('tmdb.')->group(function () {
    Route::get('/trending', [TmdbController::class, 'trending'])->name('trending');
    Route::get('/search',   [TmdbController::class, 'search'])->name('search');
});

// ── Public ──────────────────────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])
    ->name('home');

Route::get('/movies', [MovieController::class, 'index'])
    ->name('movies.index');

Route::get('/movies/{id}', [MovieController::class, 'show'])
    ->whereNumber('id')
    ->name('movies.show');

Route::get('/categories', [CategoryController::class, 'index'])
    ->name('categories.index');

Route::get('/categories/{genre}', [CategoryController::class, 'show'])
    ->name('categories.show');

Route::get('/news', [NewsController::class, 'index'])
    ->name('news.index');

Route::get('/search', [SearchController::class, 'index'])
    ->name('search');

// ── Auth (Breeze auto-registers these — listed here for reference) ──
// GET  /register            → RegisteredUserController@create   (name: register)
// POST /register            → RegisteredUserController@store
// GET  /login               → AuthenticatedSessionController@create  (name: login)
// POST /login               → AuthenticatedSessionController@store
// POST /logout              → AuthenticatedSessionController@destroy (name: logout)
// GET  /forgot-password     → PasswordResetLinkController@create     (name: password.request)
// POST /forgot-password     → PasswordResetLinkController@store      (name: password.email)
// GET  /reset-password/{token} → NewPasswordController@create        (name: password.reset)
// POST /reset-password      → NewPasswordController@store            (name: password.store)

// ── Authenticated users ─────────────────────────────────────────
Route::middleware('auth')->group(function () {

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    // Watchlist (My List)
    Route::get('/my-list', [WatchlistController::class, 'index'])
        ->name('watchlist.index');
    Route::post('/my-list/{tmdbId}', [WatchlistController::class, 'store'])
        ->whereNumber('tmdbId')
        ->name('watchlist.store');
    Route::delete('/my-list/{tmdbId}', [WatchlistController::class, 'destroy'])
        ->whereNumber('tmdbId')
        ->name('watchlist.destroy');

    // Reviews
    Route::get('/my-reviews', [ReviewController::class, 'index'])
        ->name('reviews.index');
    Route::post('/movies/{id}/reviews', [ReviewController::class, 'store'])
        ->whereNumber('id')
        ->name('reviews.store');
    Route::patch('/reviews/{review}', [ReviewController::class, 'update'])
        ->name('reviews.update');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])
        ->name('reviews.destroy');
});

// ── Admin ───────────────────────────────────────────────────────
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Dashboard
        Route::get('/', [AdminController::class, 'index'])
            ->name('index');

        // Users
        Route::get('/users', [AdminUserController::class, 'index'])
            ->name('users.index');
        Route::patch('/users/{user}', [AdminUserController::class, 'update'])
            ->name('users.update');
        Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])
            ->name('users.destroy');

        // Reviews
        Route::get('/reviews', [AdminReviewController::class, 'index'])
            ->name('reviews.index');
        Route::delete('/reviews/{review}', [AdminReviewController::class, 'destroy'])
            ->name('reviews.destroy');

        // Featured movie (hero banner)
        Route::get('/featured', [FeaturedController::class, 'edit'])
            ->name('featured.edit');
        Route::post('/featured', [FeaturedController::class, 'update'])
            ->name('featured.update');

        // Curated lists
        Route::resource('lists', AdminListController::class)
            ->names('lists');
        // Generates:
        // GET    /admin/lists              admin.lists.index
        // GET    /admin/lists/create       admin.lists.create
        // POST   /admin/lists              admin.lists.store
        // GET    /admin/lists/{list}       admin.lists.show
        // GET    /admin/lists/{list}/edit  admin.lists.edit
        // PATCH  /admin/lists/{list}       admin.lists.update
        // DELETE /admin/lists/{list}       admin.lists.destroy
    });
```

### Named Route Reference

| Name | Method | URI | Controller |
|---|---|---|---|
| `home` | GET | `/` | `HomeController@index` |
| `movies.index` | GET | `/movies` | `MovieController@index` |
| `movies.show` | GET | `/movies/{id}` | `MovieController@show` |
| `categories.index` | GET | `/categories` | `CategoryController@index` |
| `categories.show` | GET | `/categories/{genre}` | `CategoryController@show` |
| `news.index` | GET | `/news` | `NewsController@index` |
| `search` | GET | `/search` | `SearchController@index` |
| `profile.edit` | GET | `/profile` | `ProfileController@edit` |
| `profile.update` | PATCH | `/profile` | `ProfileController@update` |
| `profile.destroy` | DELETE | `/profile` | `ProfileController@destroy` |
| `watchlist.index` | GET | `/my-list` | `WatchlistController@index` |
| `watchlist.store` | POST | `/my-list/{tmdbId}` | `WatchlistController@store` |
| `watchlist.destroy` | DELETE | `/my-list/{tmdbId}` | `WatchlistController@destroy` |
| `reviews.index` | GET | `/my-reviews` | `ReviewController@index` |
| `reviews.store` | POST | `/movies/{id}/reviews` | `ReviewController@store` |
| `reviews.update` | PATCH | `/reviews/{review}` | `ReviewController@update` |
| `reviews.destroy` | DELETE | `/reviews/{review}` | `ReviewController@destroy` |
| `admin.index` | GET | `/admin` | `Admin\AdminController@index` |
| `admin.users.index` | GET | `/admin/users` | `Admin\AdminUserController@index` |
| `admin.users.update` | PATCH | `/admin/users/{user}` | `Admin\AdminUserController@update` |
| `admin.users.destroy` | DELETE | `/admin/users/{user}` | `Admin\AdminUserController@destroy` |
| `admin.reviews.index` | GET | `/admin/reviews` | `Admin\AdminReviewController@index` |
| `admin.reviews.destroy` | DELETE | `/admin/reviews/{review}` | `Admin\AdminReviewController@destroy` |
| `admin.featured.edit` | GET | `/admin/featured` | `Admin\FeaturedController@edit` |
| `admin.featured.update` | POST | `/admin/featured` | `Admin\FeaturedController@update` |
| `admin.lists.*` | — | `/admin/lists/...` | `Admin\AdminListController` (resource) |

---

## 8. Folder Structure (Key Files)

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── HomeController.php
│   │   ├── MovieController.php
│   │   ├── CategoryController.php       ← new (was PopularController)
│   │   ├── NewsController.php           ← new (replaces /popular)
│   │   ├── SearchController.php
│   │   ├── WatchlistController.php
│   │   ├── ReviewController.php
│   │   ├── ProfileController.php
│   │   └── Admin/
│   │       ├── AdminController.php
│   │       ├── AdminUserController.php
│   │       ├── AdminReviewController.php
│   │       ├── FeaturedController.php
│   │       └── AdminListController.php
│   └── Middleware/
│       └── IsAdmin.php
├── Models/
│   ├── User.php
│   ├── Watchlist.php
│   ├── Review.php
│   ├── FeaturedMovie.php
│   ├── CuratedList.php
│   └── CuratedListItem.php
└── Services/
    └── TmdbService.php

resources/views/
├── layouts/
│   └── app.blade.php                   ← base layout: topbar + sidebar + slot + footer
├── components/
│   ├── topbar.blade.php                ← fixed top bar (hamburger, logo, search, sign in)
│   ├── sidebar.blade.php               ← slide-in nav (Home, Categories, News, My List, Login)
│   ├── search-bar.blade.php            ← slide-down search input
│   ├── footer.blade.php                ← Help | Contact | About | Source
│   ├── movie-card.blade.php            ← poster card with hover overlay + rating badge
│   ├── movie-row.blade.php             ← carousel row (title + scrollable cards + chevrons + dots)
│   ├── trailer-modal.blade.php         ← YouTube embed modal (Alpine.js)
│   ├── review-form.blade.php           ← star rating + text body form
│   └── watchlist-button.blade.php      ← add/remove My List toggle (Alpine.js)
├── home/
│   └── index.blade.php                 ← hero + trending row + popular row
├── movies/
│   ├── index.blade.php                 ← browse grid + filters (genre, language, year)
│   └── show.blade.php                  ← movie detail: backdrop, cast, trailer, reviews
├── categories/
│   ├── index.blade.php                 ← genre tile grid
│   └── show.blade.php                  ← movies filtered by genre
├── news/
│   └── index.blade.php                 ← Now Playing + Trending This Week
├── search/
│   └── index.blade.php                 ← search results grid
├── watchlist/
│   └── index.blade.php                 ← My List page + empty state
├── reviews/
│   └── index.blade.php                 ← My Reviews page
├── profile/
│   └── edit.blade.php                  ← edit name, bio, avatar
└── admin/
    ├── index.blade.php                 ← dashboard stats
    ├── users/
    │   └── index.blade.php
    ├── reviews/
    │   └── index.blade.php
    ├── featured/
    │   └── edit.blade.php
    └── lists/
        ├── index.blade.php
        ├── create.blade.php
        └── edit.blade.php
```

---

## 9. Course Curriculum Alignment

| Course Section | What We Build |
|---|---|
| S01 – Introduction | Project setup, Sail, .env, TMDB key |
| S02 – Routing | All public routes, basic controller stubs |
| S03 – Blade Basics | Layout, movie card, navbar, footer |
| S04 – Forms & Validation | Search form, review form |
| S05 – Components & Styling | All reusable Blade components, Tailwind |
| S06 – Controllers & Views | HomeController, MovieController, TmdbService |
| S07 – Models, Eloquent, Factories, Seeders | All migrations, models, relationships, seeders |
| S08 – Auth | Breeze install, login, register, middleware |
| S09 – User Features | Watchlist, profile |
| S10 – CRUD | Reviews (full CRUD) |
| S11 – Advanced | Admin middleware, admin controllers |
| S12 – Polish | Pagination, error pages, meta tags |

---

## 10. Environment Setup (Quick Reference)

```bash
# 1. Create the project
curl -s "https://laravel.build/flint" | bash
cd flint

# 2. Start Docker
./vendor/bin/sail up -d

# 3. Add alias (once)
alias sail='./vendor/bin/sail'

# 4. Install Breeze (after project is running)
sail composer require laravel/breeze --dev
sail artisan breeze:install blade
sail npm install && sail npm run dev

# 5. Run migrations
sail artisan migrate

# 6. Add to .env
TMDB_API_KEY=your_key_here
TMDB_BASE_URL=https://api.themoviedb.org/3
TMDB_IMAGE_BASE_URL=https://image.tmdb.org/t/p/w500
```

---

## 11. What This Is NOT (Scope Boundaries)

To keep this in scope for the course:

- **No TV shows** — movies only (can be added post-course)
- **No payment / subscriptions** — this is not a real streaming service
- **No actual video streaming** — trailers are YouTube embeds via TMDB
- **No real-time features** — no WebSockets, no live notifications
- **No mobile app** — Blade/web only
- **No social features** — no following other users, no activity feed (yet)

---

## 12. Build Order (Recommended)

1. Docker + project setup → confirm `http://localhost` works
2. TMDB API key → test one API call returns data
3. `TmdbService` → wrap all needed endpoints
4. Blade layout + navbar + Tailwind
5. Home page with live TMDB data
6. Movies browse + Movie detail
7. Search + Browse by Language
8. Migrations + Models
9. Breeze auth (register, login, logout)
10. Watchlist (My List)
11. Reviews & ratings
12. Profile page
13. Admin middleware + dashboard
14. Admin: users, reviews, featured movie
15. Curated Lists
16. Polish pass

---

---

## Changelog

| Version | Changes |
|---|---|
| 1.0 | Initial scope document created |
| 1.1 | Routes fully rewritten with named routes, whereNumber constraints, admin prefix/name grouping, and named route reference table. Pages updated to match UI design nav (Home, Categories, News, My List). `PopularController` replaced by `CategoryController` + `NewsController`. Folder structure updated to reflect actual Blade components (topbar, sidebar, search-bar, watchlist-button) and new view directories (categories/, news/, search/). |
| 1.2 | Phase 1 Foundation marked complete. `TmdbService` and `TmdbController` created. TMDB proxy routes (`/tmdb/trending`, `/tmdb/search`) added. API key removed from browser — all fetches now go server-side. Section 5.3 updated to reflect actual implementation (no caching yet). Phase 1 checklist updated. |

*Document version: 1.2 — Phase 1 Foundation complete, Phase 2 TMDB proxy in place.*
