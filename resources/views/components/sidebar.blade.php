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
      <li>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" style="all:unset;display:block;font-family:var(--font-head);font-size:1.6rem;letter-spacing:0.08em;color:var(--muted);padding:0.5rem 0;border-bottom:1px solid var(--border);width:100%;cursor:pointer;transition:color 0.2s,padding-left 0.2s;" onmouseover="this.style.color='var(--text)';this.style.paddingLeft='0.5rem'" onmouseout="this.style.color='var(--muted)';this.style.paddingLeft='0'">Sign Out</button>
        </form>
      </li>
    @else
      <li><a href="{{ route('login') }}" class="{{ request()->routeIs('login') ? 'active' : '' }}">Sign In</a></li>
    @endauth
  </ul>
  <p class="sidebar-footer">© {{ date('Y') }} Flint · Built with TMDB</p>
</aside>
