<x-guest-layout>
  <div class="auth-card">
    <h1 class="auth-title">Sign In</h1>
    <p class="auth-subtitle">Welcome back. Pick up where you left off.</p>

    @if (session('status'))
      <p class="auth-status">{{ session('status') }}</p>
    @endif

    <form method="POST" action="{{ route('login') }}">
      @csrf

      <div class="auth-field">
        <label class="auth-label" for="email">Email</label>
        <input class="auth-input" id="email" type="email" name="email"
               value="{{ old('email') }}" required autofocus autocomplete="username"
               placeholder="you@example.com" />
        @error('email')
          <p class="auth-error">{{ $message }}</p>
        @enderror
      </div>

      <div class="auth-field">
        <label class="auth-label" for="password">Password</label>
        <input class="auth-input" id="password" type="password" name="password"
               required autocomplete="current-password" placeholder="••••••••" />
        @error('password')
          <p class="auth-error">{{ $message }}</p>
        @enderror
      </div>

      <div class="auth-row">
        <label class="auth-remember">
          <input type="checkbox" name="remember" />
          Remember me
        </label>
        @if (Route::has('password.request'))
          <a class="auth-forgot" href="{{ route('password.request') }}">Forgot password?</a>
        @endif
      </div>

      <button type="submit" class="auth-submit">Sign In</button>
    </form>
  </div>
</x-guest-layout>
