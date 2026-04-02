// ─────────────────────────────────────────────────────────
// CONFIG
// ─────────────────────────────────────────────────────────
const IMG_BASE = 'https://image.tmdb.org/t/p';

// ─────────────────────────────────────────────────────────
// DEMO DATA (shown when no API key is set)
// ─────────────────────────────────────────────────────────
const demoMovies = [
  { id:1, title:'The Wild Kingdom',  overview:'In a remote wilderness, a team of researchers uncovers a hidden ecosystem teeming with creatures never seen before.', vote_average:8.2, release_date:'2024-03-15', poster_path:null, backdrop_path:null },
  { id:2, title:'Neon Requiem',      overview:'A jazz musician in a futuristic city gets tangled in a conspiracy that blurs the line between dreams and reality.',   vote_average:7.6, release_date:'2024-06-01', poster_path:null, backdrop_path:null },
  { id:3, title:'Shattered Tides',   overview:'Two estranged siblings reunite on a sailing voyage and confront long-buried family secrets threatening to tear them apart.', vote_average:7.9, release_date:'2024-09-20', poster_path:null, backdrop_path:null },
  { id:4, title:'Epoch',             overview:'Humanity\'s last archive satellite is hijacked. One technician has 48 hours to recover it before all recorded history is lost.', vote_average:8.4, release_date:'2025-01-10', poster_path:null, backdrop_path:null },
  { id:5, title:'The Glass Bridge',  overview:'A cold-case detective follows a trail of cryptic clues across three continents, each one more dangerous than the last.',  vote_average:7.3, release_date:'2025-02-14', poster_path:null, backdrop_path:null },
  { id:6, title:'Bloom',             overview:'A young botanist\'s discovery of a prehistoric plant specimen triggers a chain of events that could rewrite evolutionary history.', vote_average:8.0, release_date:'2025-03-01', poster_path:null, backdrop_path:null },
  { id:7, title:'Iron & Ash',        overview:'Set in a post-industrial frontier, a lone wanderer seeks justice in a land ruled by corrupt rail barons and hired guns.', vote_average:7.7, release_date:'2024-11-05', poster_path:null, backdrop_path:null },
  { id:8, title:'After the Static',  overview:'A radio operator in an isolated mountain station begins picking up transmissions from a station that shut down thirty years ago.', vote_average:8.1, release_date:'2024-08-22', poster_path:null, backdrop_path:null },
];

// ─────────────────────────────────────────────────────────
// FETCH HELPERS
// ─────────────────────────────────────────────────────────
async function api(path, params = {}) {
  const url = new URL(path, window.location.origin);
  Object.entries(params).forEach(([k, v]) => url.searchParams.set(k, v));
  const r = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
  if (!r.ok) throw new Error(`API ${r.status}`);
  return r.json();
}

function posterUrl(path, size = 'w342') {
  return path ? `${IMG_BASE}/${size}${path}` : null;
}

function backdropUrl(path) {
  return path ? `${IMG_BASE}/original${path}` : null;
}

// ─────────────────────────────────────────────────────────
// HERO
// ─────────────────────────────────────────────────────────
function renderHero(movie) {
  const heroBg   = document.getElementById('heroBg');
  const backdrop = backdropUrl(movie.backdrop_path);

  if (backdrop) {
    const img  = new Image();
    img.onload = () => {
      heroBg.style.backgroundImage = `url('${backdrop}')`;
      heroBg.classList.remove('loading');
    };
    img.src = backdrop;
  } else {
    heroBg.style.background = 'linear-gradient(135deg, #1a1008 0%, #0d0d0d 60%, #0a0a12 100%)';
    heroBg.classList.remove('loading');
  }

  const year   = movie.release_date?.split('-')[0] ?? '—';
  const rating = movie.vote_average ? movie.vote_average.toFixed(1) : '—';

  document.getElementById('heroTitle').textContent    = movie.title;
  document.getElementById('heroOverview').textContent = movie.overview;

  const btnPlay     = document.getElementById('btnPlay');
  const btnMoreInfo = document.getElementById('btnMoreInfo');
  if (btnPlay)     btnPlay.onclick     = () => window.location.href = `/movie/${movie.id}`;
  if (btnMoreInfo) btnMoreInfo.onclick = () => window.location.href = `/movie/${movie.id}`;

  const meta = document.getElementById('heroMeta');
  if (meta) {
    const dot = () => Object.assign(document.createElement('span'), { textContent: ' · ' });
    meta.replaceChildren('Trending in Movies', dot(), year, dot(), `★ ${rating}`);
  }
}

// ─────────────────────────────────────────────────────────
// LAZY IMAGE LOADER
// Uses IntersectionObserver so images visible inside the
// horizontal carousel load immediately, while far-scrolled
// cards are deferred. Avoids the browser bug where
// loading="lazy" defers ALL images in overflow containers.
// ─────────────────────────────────────────────────────────
// CARD BUILDER (XSS-safe: no innerHTML for user data)
// ─────────────────────────────────────────────────────────
function buildCard(movie) {
  const poster = posterUrl(movie.poster_path);
  const year   = movie.release_date?.split('-')[0] ?? '—';
  const rating = movie.vote_average ? movie.vote_average.toFixed(1) : '—';

  const posterEl = document.createElement('div');
  posterEl.className = poster ? 'card-poster skeleton' : 'card-poster';
  if (!poster) posterEl.style.background = 'linear-gradient(135deg,#1a1008,#111)';

  if (poster) {
    const img = document.createElement('img');
    img.src = poster;
    img.alt = movie.title;
    img.addEventListener('load',  () => posterEl.classList.remove('skeleton'));
    img.addEventListener('error', () => img.remove());
    posterEl.appendChild(img);
  }

  const ratingEl = Object.assign(document.createElement('div'), { className: 'card-rating', textContent: `★ ${rating}` });

  const overlayTitle = Object.assign(document.createElement('div'), { className: 'card-overlay-title', textContent: movie.title });
  const overlayMeta  = Object.assign(document.createElement('div'), { className: 'card-overlay-meta',  textContent: year });
  const overlayEl    = document.createElement('div');
  overlayEl.className = 'card-overlay';
  overlayEl.append(overlayTitle, overlayMeta);

  posterEl.append(ratingEl, overlayEl);

  const titleEl = Object.assign(document.createElement('div'), { className: 'card-title', textContent: movie.title });
  const yearEl  = Object.assign(document.createElement('div'), { className: 'card-year',  textContent: year });
  const infoEl  = document.createElement('div');
  infoEl.className = 'card-info';
  infoEl.append(titleEl, yearEl);

  const card = document.createElement('a');
  card.className = 'movie-card';
  card.href = `/movie/${movie.id}`;
  card.append(posterEl, infoEl);
  return card;
}

// ─────────────────────────────────────────────────────────
// CAROUSEL HELPERS
// ─────────────────────────────────────────────────────────
function cardWidth(track) {
  return (track.firstElementChild?.offsetWidth || 200) + 16;
}

function buildDots(count, track, dotsContainer) {
  dotsContainer.innerHTML = '';
  const dots = [];

  for (let i = 0; i < count; i++) {
    const btn = document.createElement('button');
    btn.className = `dot${i === 0 ? ' active' : ''}`;
    btn.setAttribute('aria-label', `Go to movie ${i + 1}`);
    btn.addEventListener('click', () => scrollToCard(i, track, dots));
    dotsContainer.appendChild(btn);
    dots.push(btn);
  }

  track.addEventListener('scroll', () => {
    const idx = Math.round(track.scrollLeft / cardWidth(track));
    dots.forEach((d, i) => d.classList.toggle('active', i === idx));
  }, { passive: true });

  return dots;
}

function scrollToCard(idx, track, dots) {
  track.scrollTo({ left: idx * cardWidth(track), behavior: 'smooth' });
  dots.forEach((d, i) => d.classList.toggle('active', i === idx));
}

// ─────────────────────────────────────────────────────────
// SKELETON PLACEHOLDERS
// ─────────────────────────────────────────────────────────
function addSkeletons(track, count = 6) {
  for (let i = 0; i < count; i++) {
    const titlePlaceholder = Object.assign(document.createElement('div'), { className: 'card-title' });
    Object.assign(titlePlaceholder.style, { background: '#1e1e1e', height: '12px', borderRadius: '2px', width: '70%' });

    const info = document.createElement('div');
    info.className = 'card-info';
    info.appendChild(titlePlaceholder);

    const card = document.createElement('div');
    card.className = 'movie-card';
    card.append(Object.assign(document.createElement('div'), { className: 'card-poster skeleton' }), info);
    track.appendChild(card);
  }
}

// ─────────────────────────────────────────────────────────
// INIT ROW — reusable per carousel section
// ─────────────────────────────────────────────────────────
async function initRow({ id, endpoint }) {
  const track   = document.getElementById(`${id}Track`);
  const dotsEl  = document.getElementById(`${id}Dots`);
  const countEl = document.getElementById(`${id}Count`);
  const prevBtn = document.getElementById(`${id}PrevBtn`);
  const nextBtn = document.getElementById(`${id}NextBtn`);

  if (!track) return;

  addSkeletons(track);

  let movies = [];
  let isDemo = false;

  try {
    const data = await api(endpoint);
    movies = data.results || [];
  } catch {
    movies = demoMovies;
    isDemo = true;
  }

  track.innerHTML = '';

  if (movies.length > 0 && id === 'trending') renderHero(movies[0]);

  movies.forEach(m => track.appendChild(buildCard(m)));

  if (countEl) countEl.textContent = isDemo ? 'Demo mode' : `${movies.length} this week`;

  if (dotsEl) buildDots(movies.length, track, dotsEl);

  prevBtn?.addEventListener('click', () => track.scrollBy({ left: -cardWidth(track) * 3, behavior: 'smooth' }));
  nextBtn?.addEventListener('click', () => track.scrollBy({ left:  cardWidth(track) * 3, behavior: 'smooth' }));
}

// ─────────────────────────────────────────────────────────
// TOPBAR SCROLL EFFECT
// ─────────────────────────────────────────────────────────
window.addEventListener('scroll', () => {
  document.getElementById('topbar')?.classList.toggle('scrolled', window.scrollY > 60);
}, { passive: true });

// ─────────────────────────────────────────────────────────
// SIDEBAR
// ─────────────────────────────────────────────────────────
const sidebar = document.getElementById('sidebar');
const overlay = document.getElementById('overlay');

function openSidebar() {
  sidebar?.classList.add('open');
  overlay?.classList.add('show');
  document.body.style.overflow = 'hidden';
}

function closeSidebar() {
  sidebar?.classList.remove('open');
  overlay?.classList.remove('show');
  document.body.style.overflow = '';
}

document.getElementById('btnHamburger')?.addEventListener('click', openSidebar);
document.getElementById('btnClose')?.addEventListener('click', closeSidebar);

// ─────────────────────────────────────────────────────────
// SEARCH
// ─────────────────────────────────────────────────────────
const searchBar   = document.getElementById('searchBar');
const searchInput = document.getElementById('searchInput');

// Lazy-created results dropdown
let searchResultsEl = null;
function getResultsEl() {
  if (!searchResultsEl) {
    searchResultsEl = Object.assign(document.createElement('div'), { className: 'search-results' });
    searchBar?.appendChild(searchResultsEl);
  }
  return searchResultsEl;
}

function openSearch() {
  searchBar?.classList.add('open');
  requestAnimationFrame(() => searchInput?.focus());
}

function closeSearch() {
  searchBar?.classList.remove('open');
  if (searchResultsEl) searchResultsEl.replaceChildren();
}

function debounce(fn, ms) {
  let t;
  return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), ms); };
}

async function doSearch(query) {
  const resultsEl = getResultsEl();
  resultsEl.replaceChildren();
  if (!query.trim()) return;

  resultsEl.append(Object.assign(document.createElement('p'), { className: 'search-loading', textContent: 'Searching…' }));

  try {
    let movies;
    try {
      const data = await api('/tmdb/search', { query });
      movies = data.results?.slice(0, 8) || [];
    } catch {
      const q = query.toLowerCase();
      movies = demoMovies.filter(m => m.title.toLowerCase().includes(q));
    }

    resultsEl.replaceChildren();

    if (!movies.length) {
      const empty = document.createElement('p');
      empty.className = 'search-empty';
      empty.textContent = `No results for "${query}"`;
      resultsEl.appendChild(empty);
      return;
    }

    movies.forEach(m => {
      const item = document.createElement('a');
      item.className = 'search-result-item';
      item.href = `/movie/${m.id}`;

      const poster = posterUrl(m.poster_path, 'w92');
      if (poster) {
        item.appendChild(Object.assign(document.createElement('img'), { src: poster, alt: m.title, width: 40 }));
      }

      const title = Object.assign(document.createElement('span'), { className: 'search-result-title', textContent: m.title });
      const year  = Object.assign(document.createElement('span'), { className: 'search-result-year',  textContent: m.release_date?.split('-')[0] ?? '' });

      const text = document.createElement('div');
      text.className = 'search-result-text';
      text.append(title, year);
      item.appendChild(text);
      resultsEl.appendChild(item);
    });
  } catch {
    resultsEl.replaceChildren(Object.assign(document.createElement('p'), { className: 'search-empty', textContent: 'Search failed. Please try again.' }));
  }
}

searchInput?.addEventListener('input', debounce(e => doSearch(e.target.value), 350));

document.getElementById('btnSearch')?.addEventListener('click', openSearch);
document.getElementById('btnSearchClose')?.addEventListener('click', closeSearch);

overlay?.addEventListener('click', () => { closeSidebar(); closeSearch(); });

document.addEventListener('keydown', e => {
  if (e.key === 'Escape') { closeSidebar(); closeSearch(); }
});

// ─────────────────────────────────────────────────────────
// RUN
// ─────────────────────────────────────────────────────────
initRow({ id: 'trending', endpoint: '/tmdb/trending' });
