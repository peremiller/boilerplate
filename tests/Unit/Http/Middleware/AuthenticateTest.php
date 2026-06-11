<?php

namespace Tests\Unit\Http\Middleware;

use App\Http\Middleware\Authenticate;
use Tests\TestCase;
use Illuminate\Http\Request;

class AuthenticateTest extends TestCase
{
    protected function redirectTo(Request $request): ?string
    {
        $middleware = new Authenticate($this->app['auth']);

        $reflection = new \ReflectionClass($middleware);
        $method = $reflection->getMethod('redirectTo');
        $method->setAccessible(true);

        return $method->invoke($middleware, $request);
    }

    public function test_non_json_request_redirects_to_login_route()
    {
        $request = Request::create('/dashboard', 'GET');
        $request->headers->set('Accept', 'text/html');

        $result = $this->redirectTo($request);

        $this->assertEquals(route('login'), $result);
    }

    public function test_json_request_returns_null_redirect()
    {
        $request = Request::create('/api/users', 'GET');
        $request->headers->set('Accept', 'application/json');

        $result = $this->redirectTo($request);

        $this->assertNull($result);
    }
}
