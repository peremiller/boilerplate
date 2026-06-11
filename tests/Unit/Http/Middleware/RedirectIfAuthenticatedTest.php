<?php

namespace Tests\Unit\Http\Middleware;

use App\Http\Middleware\RedirectIfAuthenticated;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;

class RedirectIfAuthenticatedTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_request_passes_through()
    {
        $middleware = new RedirectIfAuthenticated();
        $request = Request::create('/register', 'GET');
        $called = false;

        $middleware->handle($request, function ($req) use (&$called) {
            $called = true;
            return response('next');
        });

        $this->assertTrue($called);
    }

    public function test_authenticated_user_is_redirected_to_home()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $middleware = new RedirectIfAuthenticated();
        $request = Request::create('/register', 'GET');

        $response = $middleware->handle($request, function ($req) {
            return response('next');
        });

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals(url('/home'), $response->headers->get('Location'));
    }

    public function test_authenticated_user_with_explicit_guard_is_redirected()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user, 'web');

        $middleware = new RedirectIfAuthenticated();
        $request = Request::create('/register', 'GET');

        $response = $middleware->handle($request, function ($req) {
            return response('next');
        }, 'web');

        $this->assertEquals(302, $response->getStatusCode());
    }
}
