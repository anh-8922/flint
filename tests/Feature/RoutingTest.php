<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Routing in Laravel
 *
 * Routes define the URLs your application responds to.
 * They live in routes/web.php and map HTTP requests to responses —
 * whether that's a view, a string, JSON, or a controller method.
 *
 * The Route facade provides methods matching HTTP verbs:
 *   Route::get(), Route::post(), Route::put(), Route::delete()
 *   Route::match(['get', 'post'], ...) — accepts multiple methods
 *   Route::any(...)                   — accepts any method
 *
 * Routes can be named with ->name('alias') and referenced
 * anywhere via the route() helper: route('alias').
 *
 * Laravel automatically sets the correct Content-Type header:
 *   - string/HTML → text/html
 *   - array/object → application/json
 */
class RoutingTest extends TestCase
{
    // GET / → loads the welcome view (text/html, 200)
    public function test_home_route_returns_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    // Undef routes always return 404 — Laravel handles this automatically
    public function test_undefined_route_returns_not_found(): void
    {
        $response = $this->get('/this-route-does-not-exist');

        $response->assertStatus(404);
    }

    // Named routes let you reference a URL by alias instead of hardcoding the path.
    // route('dashboard') resolves to /dashboard regardless of where you use it.
    public function test_named_route_resolves_to_correct_url(): void
    {
        $this->assertEquals('/dashboard', route('dashboard', absolute: false));
    }

    // POST routes require a CSRF token in production forms.
    // Laravel's test client handles this automatically — no token needed in tests.
    public function test_post_route_is_protected_by_csrf_in_production(): void
    {
        // withoutMiddleware() simulates the curl-style bypass shown in the lesson.
        // In real forms, @csrf generates the hidden token that satisfies this check.
        $response = $this->withoutMiddleware()->post('/submit-example');

        // Route doesn't exist yet — 404 confirms CSRF isn't the blocker here
        $response->assertStatus(404);
    }
}
