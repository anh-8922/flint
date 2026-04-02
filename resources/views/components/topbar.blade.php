<header class="topbar" id="topbar">
  <div class="topbar-left">
    <button class="btn-hamburger" id="btnHamburger" aria-label="Open menu">
      <span></span><span></span><span></span>
    </button>
    <a href="{{ route('home') }}" class="logo">FL<span>I</span>NT</a>
  </div>
  <div class="topbar-right">
    <button class="btn-search" id="btnSearch" aria-label="Search">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
      </svg>
    </button>
    @auth
      <a href="{{ route('profile.edit') }}" class="btn-username">{{ Auth::user()->name }}</a>
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn-signout">Sign Out</button>
      </form>
    @else
      <a href="{{ route('login') }}" class="btn-login">Sign In</a>
    @endauth
  </div>
</header>
