<aside class="sidebar" id="sidebar" role="navigation" aria-label="Main navigation">
  <div class="sidebar-header">
    <span class="sidebar-logo">FL<span style="color:var(--text)">INT</span></span>
    <button class="btn-close" id="btnClose" aria-label="Close menu">✕</button>
  </div>
  <ul class="sidebar-nav">
    <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Home</a></li>
    <li><a href="#" class="{{ request()->routeIs('categories.*') ? 'active' : '' }}">Categories</a></li>
    <li><a href="#" class="{{ request()->routeIs('news.*') ? 'active' : '' }}">News</a></li>
    <li><a href="#" class="{{ request()->routeIs('watchlist.*') ? 'active' : '' }}">My List</a></li>
    @auth
      <li><a href="{{ route('profile.edit') }}">{{ Auth::user()->name }}</a></li>
    @else
      <li><a href="{{ route('login') }}" class="{{ request()->routeIs('login') ? 'active' : '' }}">Login</a></li>
    @endauth
  </ul>
  <p class="sidebar-footer">© {{ date('Y') }} Flint · Built with TMDB</p>
</aside>
