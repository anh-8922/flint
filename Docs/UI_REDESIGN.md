# Flint — UI Redesign Documentation

> **Document type:** Design reference & handoff guide  
> **Version:** 1.0  
> **Status:** Homepage complete — further pages in progress

---

## 1. Design Concept

### The Idea — "Raw Spark"

The name **Flint** is a stone that makes fire. The entire design language extends that metaphor — dark, raw surfaces with sudden bursts of warm amber light. It's the feeling of sitting in a dark cinema right before the projector flicks on.

The aesthetic sits between **editorial cinema magazine** and **premium streaming interface** — serious enough to feel trustworthy, characterful enough to feel human. It avoids the clinical blue/grey palette most streaming apps default to, and instead leans into warmth, texture, and contrast.

**One thing users will remember:** The amber spark accent against near-black — it's immediately distinct from Netflix red, Prime blue, or Disney white.

---

## 2. Wireframe Reference

The homepage layout was designed from the following wireframe (two states — desktop and mobile nav open):

```
DESKTOP STATE
┌─────────────────────────────────────────────────┐
│ ☰  FLINT                           🔍  [Sign In]│  ← Topbar (fixed, fades in on scroll)
├─────────────────────────────────────────────────┤
│                                                 │
│         FULL-BLEED HERO IMAGE                   │
│                                                 │
│  TRENDING IN MOVIES                             │
│  Movie Title                                    │
│  Year · Genre · ★ Rating                        │
│  Overview text capped at 3 lines...             │
│                                                 │
│  [▶ Play]  [ℹ More Info]                        │
└─────────────────────────────────────────────────┘
 TRENDING
‹  [Card] [Card] [Card] [Card]  ›
         ○ ● ○ ○
┌─────────────────────────────────────────────────┐
│  FLINT       Help | Contact | About | Source    │
└─────────────────────────────────────────────────┘

MOBILE — NAV OPEN STATE
┌──────────┬──────────────────────────────────────┐
│ FLINT  ✕ │                                      │
│          │       (hero visible behind)           │
│ HOME     │                                      │
│ CATEGORIES│                                     │
│ NEWS     │  TRENDING                            │
│ LOGIN    │  [Card] [Card] [Card]                │
│          │       ○ ● ○ ○                        │
│          │                                      │
│          │  Help | Contact | About | Source     │
└──────────┴──────────────────────────────────────┘
```

### Wireframe Decisions Translated

| Wireframe Element | Implementation Decision |
|---|---|
| Hamburger top-left | Fixed topbar, left-aligned, opens sidebar |
| Sidebar with nav items | Slide-in from left, 280px wide, overlay behind |
| Full-bleed hero | `100svh`, backdrop image from TMDB, gradient overlay |
| "Trending in movies / Released - undefined" | Hero badge + meta line (year, rating) |
| Play + More Info buttons | Two-button CTA row, primary filled + secondary ghost |
| Trending row with chevrons | Horizontal scroll carousel, CSS scroll-snap |
| Dots below carousel | Live dot indicator synced to scroll position |
| Footer: Help / Contact / About / Source | Single-row footer, pipe-separated links |
| Mobile: sidebar overlaps content | Sidebar + darkened blur overlay, body scroll locked |

---

## 2. Color Scheme

```
PRIMARY PALETTE
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

  Background      #080808   Near-black. Not pure black — has warmth.
  Surface         #111111   Elevated surfaces (cards, panels)
  Card            #161616   Movie cards, secondary containers
  Border          rgba(255,255,255,0.07)   Hairline borders, barely-there

ACCENT
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

  Spark           #E8A020   Primary accent. Warm amber. The "flint" colour.
  Spark dim       #B8781A   Hover/pressed state for amber elements

TEXT
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

  Primary         #F0EDE8   Main text. Warm white, not pure #FFF.
  Muted           #7A7570   Secondary text, labels, metadata
  Dead            #3a3530   Footer copyright — barely visible, intentional
```

### CSS Variables

```css
:root {
  --bg:        #080808;
  --surface:   #111111;
  --card:      #161616;
  --border:    rgba(255,255,255,0.07);
  --spark:     #E8A020;
  --spark-dim: #B8781A;
  --text:      #F0EDE8;
  --muted:     #7A7570;
}
```

### Colour Usage Rules

- **Amber `#E8A020`** is used sparingly — logo accent letter, active nav item, rating badges, dot indicators, section title underlines, hover states. Overusing it kills the impact.
- **Never use amber as a background** for large areas — only as an accent on small elements or the Sign In button.
- **Text is warm white `#F0EDE8`**, not pure white `#FFFFFF` — pure white reads as harsh against the near-black background.
- **Borders are invisible at rest** (`0.07` opacity) — they define structure without competing visually.

---

## 3. Typography

### Typeface Pairing

| Role | Font | Weight | Usage |
|---|---|---|---|
| Display / Headings | **Bebas Neue** | 400 (only weight) | Logo, section titles, movie titles, nav items |
| Body / UI | **Outfit** | 300, 400, 500, 600 | Paragraphs, buttons, labels, metadata |

```html
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet" />
```

### Why This Pairing

**Bebas Neue** is condensed, tall, and commanding — it reads like a film poster or a marquee sign. It brings cinematic authority to every heading.

**Outfit** is geometric but warm — it's modern without being cold. Its lighter weights (300, 400) contrast well against Bebas Neue's boldness.

### Type Scale

```
Logo            Bebas Neue   1.8rem    letter-spacing: 0.1em
Sidebar logo    Bebas Neue   2rem      letter-spacing: 0.05em
Hero title      Bebas Neue   clamp(3rem, 7vw, 5.5rem)   line-height: 0.95
Section title   Bebas Neue   1.4rem    letter-spacing: 0.12em
Nav items       Bebas Neue   1.6rem    letter-spacing: 0.08em
Hero badge      Outfit 600   0.72rem   letter-spacing: 0.12em   uppercase
Hero meta       Outfit 400   0.80rem   letter-spacing: 0.06em   uppercase
Hero overview   Outfit 400   0.92rem   line-height: 1.65
Buttons         Outfit 600   0.85rem   letter-spacing: 0.06em   uppercase
Card title      Outfit 500   0.82rem
Card year       Outfit 400   0.72rem
Footer links    Outfit 400   0.75rem   letter-spacing: 0.06em
```

---

## 4. Layout & Spacing

### Grid Approach

No CSS Grid framework. Layout is intentionally structural:

- **Hero:** `100svh`, full-bleed, content anchored bottom-left with `padding: 0 3rem 4rem`
- **Sections:** `padding: 0 3rem` on desktop, `0 1.5rem` on mobile
- **Carousel:** Horizontal flexbox with `overflow-x: auto` and `scroll-snap-type: x mandatory`
- **Cards:** Fixed `flex: 0 0 200px` (desktop) / `150px` (tablet) / `130px` (mobile)

### Spacing System

```
xs    0.25rem    4px    Dot gaps, small nudges
sm    0.5rem     8px    Icon gaps, tight padding
md    0.75rem   12px    Button padding, card gaps
lg    1rem      16px    Card gap in carousel
xl    1.5rem    24px    Section padding (mobile)
2xl   2rem      32px    Section padding (desktop)
3xl   3rem      48px    Hero content padding
```

---

## 5. Components

### 5.1 Topbar

```
[☰]  [FLINT]                     [🔍]  [Sign In]
```

- Fixed position, `z-index: 100`
- **Default state:** `linear-gradient(to bottom, rgba(8,8,8,0.95), transparent)` — blends into hero
- **Scrolled state** (after 60px): `rgba(8,8,8,0.98)` + bottom border — `.scrolled` class toggled via scroll listener
- Hamburger is 3 `<span>` elements — hover turns them amber
- Sign In button: amber fill, dark text, no border-radius (sharp corners throughout)

### 5.2 Sidebar Navigation

```
┌──────────────────┐
│ FLINT         ✕  │
│                  │
│ HOME             │  ← active state (amber)
│ CATEGORIES       │
│ NEWS             │
│ MY LIST          │
│ LOGIN            │
│                  │
│ © 2025 Flint     │  ← footer inside sidebar
└──────────────────┘
```

- Width: `280px` (`--nav-w`)
- Slide in from left: `transform: translateX(-280px)` → `translateX(0)`
- Transition: `0.35s cubic-bezier(0.4, 0, 0.2, 1)` (standard easing)
- Overlay behind: `rgba(0,0,0,0.6)` + `backdrop-filter: blur(2px)`
- On open: `document.body.style.overflow = 'hidden'` prevents background scroll
- Close triggers: ✕ button, overlay click, `Escape` key

### 5.3 Hero Section

Layers (bottom to top):

```
1. .hero-bg        — TMDB backdrop image, background-size: cover
2. .hero-grain     — SVG noise texture, opacity: 0.03 (subtle film grain)
3. .hero-gradient  — dual gradient: bottom-to-top (dark) + left-to-right (dark)
4. .hero-content   — text + buttons, positioned bottom-left
```

**Gradient formula:**
```css
background:
  linear-gradient(to top,   rgba(8,8,8,1) 0%, rgba(8,8,8,0.7) 30%, transparent 60%),
  linear-gradient(to right, rgba(8,8,8,0.8) 0%, transparent 60%);
```

**Loading state:** `.hero-bg.loading` uses shimmer animation until backdrop loads. Image preloaded via `new Image()` — shimmer removed only on `img.onload`.

**Hero badge:** Small uppercase label above title with a 20px amber line before it (`::before`).

**Overview text:** Capped at 3 lines with `-webkit-line-clamp: 3`.

### 5.4 Movie Card

```
┌─────────────┐  ← border turns amber on hover
│             │
│  [poster]   │  ← scale(1.06) on hover, brightness(0.7)
│             │
│          ★7.8│  ← top-right rating badge, amber border
│             │
│  [overlay: title + year on hover]
└─────────────┘
  Movie Title    ← truncated with text-overflow: ellipsis
  2024           ← muted colour
```

**Skeleton state:** Shimmer animation before image loads. `.skeleton` removed on `img.onload`.

**Hover sequence:**
1. Border turns amber
2. Image scales up 6% and dims to 70% brightness
3. Overlay fades in with title and year

### 5.5 Carousel

- **Track:** `display: flex`, `overflow-x: auto`, `scroll-snap-type: x mandatory`, `scrollbar-width: none`
- **Cards:** `scroll-snap-align: start`
- **Chevrons:** Absolute positioned, `height: 80px`. Hidden on mobile (swipe instead).
- **Dots:** One per movie. Active dot is amber + `scale(1.3)`. Synced via `scroll` event using `Math.round(scrollLeft / cardWidth)`.
- **Chevron scroll:** 3 cards at a time

### 5.6 Search Bar

- Slides down from top: `translateY(-100%)` → `translateY(0)`
- `z-index: 300` — above everything including sidebar
- Auto-focuses input after 300ms (post-animation)
- Close: ✕ button, `Escape` key, or overlay click

### 5.7 Footer

```
FLINT    Help | Contact | About | Source    Data provided by TMDB
```

- Single row, `justify-content: space-between`
- Pipe separators via CSS `border-right` on anchor tags (not literal `|` characters)
- Logo in muted colour — footer is intentionally low-energy

---

## 6. Motion & Animation

| Element | Animation | Duration | Easing |
|---|---|---|---|
| Hero content | `fadeUp` (opacity + translateY 24px) | 0.8s | ease |
| Hero content delay | Starts after | 0.2s | — |
| Sidebar | `translateX` | 0.35s | cubic-bezier(0.4,0,0.2,1) |
| Overlay | `opacity` | 0.35s | linear |
| Search bar | `translateY` | 0.3s | cubic-bezier(0.4,0,0.2,1) |
| Card image hover | `scale(1.06)` + `brightness(0.7)` | 0.4s | ease |
| Card border hover | colour to amber | 0.25s | linear |
| Card overlay | `opacity` 0→1 | 0.3s | linear |
| Shimmer skeleton | background-position loop | 1.5s | infinite linear |
| Dot active | `scale(1.3)` + colour | 0.2s | linear |
| Topbar scrolled | background colour | 0.3s | linear |

**Keyframes:**
```css
@keyframes fadeUp {
  from { opacity: 0; transform: translateY(24px); }
  to   { opacity: 1; transform: translateY(0); }
}

@keyframes shimmer {
  0%   { background-position: -200% 0; }
  100% { background-position:  200% 0; }
}
```

---

## 7. TMDB Integration (Prototype)

The HTML prototype connects to TMDB directly from the browser. In the final Laravel app, all API calls move server-side through `TmdbService`.

### Endpoints used in prototype

| Data | Endpoint |
|---|---|
| Hero + trending cards | `GET /trending/movie/week` |
| Poster images | `https://image.tmdb.org/t/p/w342{poster_path}` |
| Backdrop (hero) | `https://image.tmdb.org/t/p/original{backdrop_path}` |

### Demo Mode

When no API key is set, the app automatically falls back to 8 hardcoded demo movies. The carousel, dots, hero, shimmer states, and hover effects all work identically in demo mode — useful for UI development without needing a key.

---

## 8. Responsive Behaviour

| Breakpoint | Changes |
|---|---|
| `> 768px` | Full layout. Chevrons visible. 200px cards. `padding: 0 3rem`. |
| `≤ 768px` | Chevrons hidden (touch swipe). 150px cards. `padding: 0 1.5rem`. Footer stacks vertically. |
| `≤ 480px` | 130px cards. Sign In button hidden (login via sidebar only). |

### Mobile Navigation Pattern

On mobile, the primary navigation is the sidebar. The topbar only shows hamburger, logo, and search icon — no inline nav links.

---

## 9. Design Decisions & Rationale

| Decision | Why |
|---|---|
| Sharp corners throughout | Feels editorial and cinematic, not app-like or bubbly |
| Warm white `#F0EDE8` not `#FFFFFF` | Pure white against near-black is harsh. Warm white is easier on the eye. |
| Amber accent not red or blue | Red is Netflix. Blue is Prime. Amber is warm, ownable, and unique to Flint. |
| Bebas Neue for headings | Immediately cinematic — reads like a movie poster or theatre sign |
| Film grain on hero | Adds texture and warmth. Stops the hero feeling like a screenshot. |
| `100svh` not `100vh` for hero | `svh` accounts for the mobile browser address bar — avoids content being cut off |
| Image preloaded via `new Image()` | Prevents a flash of broken/half-loaded backdrop before applying to background |
| Skeleton shimmer on load | Gives immediate visual feedback — nothing feels broken during network requests |
| `body.overflow = hidden` on sidebar | Prevents background scrolling behind the open sidebar on mobile |
| Dual gradient on hero | Vertical: legibility at bottom. Horizontal: text readable regardless of backdrop image content. |

---

## 10. File Reference

| File | Purpose |
|---|---|
| `flint.html` | Full homepage prototype (self-contained, single file) |
| `flint-scope.md` | Full project scope, phases, DB schema, routes |
| `flint-docker-setup.md` | Docker Compose / Laravel Sail setup guide |
| `UI_REDESIGN.md` | This document |

---

## 11. Pages Still To Design

| Page | Notes |
|---|---|
| `/movies` | Browse grid — filterable by genre, language, year. Reuses card component. |
| `/movies/{id}` | Detail page — large backdrop, cast row, trailer embed, reviews section |
| `/popular` | Two sections: Now Playing + Trending This Week |
| `/search` | Prominent search input, results grid below |
| `/my-list` | User's saved movies — card grid + empty state |
| `/register` + `/login` | Minimal centred auth forms on dark card |
| `/profile` | Avatar, name, bio, stats (saved movies, reviews written) |
| `/admin/*` | Data-dense dashboard — tables, same colour system |

---

*UI Redesign Documentation v1.0 — Flint project*
