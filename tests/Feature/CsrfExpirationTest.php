<?php

namespace Tests\Feature;

use App\Services\AdminLoginPathService;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class CsrfExpirationTest extends TestCase
{
    use RefreshDatabase;

    public function test_expired_csrf_on_public_login_redirects_gracefully_without_419(): void
    {
        $handler = app(ExceptionHandler::class);
        $request = Request::create('/public/login', 'POST', [
            'email' => 'user@example.com',
            'password' => 'secret123',
        ]);

        $response = $handler->render($request, new TokenMismatchException());

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals(route('public.login'), $response->headers->get('Location'));
    }

    public function test_expired_csrf_on_admin_login_redirects_gracefully_without_419(): void
    {
        $adminPath = AdminLoginPathService::getPath();
        $handler = app(ExceptionHandler::class);
        $request = Request::create('/' . $adminPath, 'POST', [
            'username' => 'admin_test',
            'password' => 'secret123',
        ]);

        $response = $handler->render($request, new TokenMismatchException());

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals(route('login'), $response->headers->get('Location'));
    }

    public function test_expired_csrf_on_logout_gracefully_logs_out_and_redirects_to_home(): void
    {
        $handler = app(ExceptionHandler::class);
        $request = Request::create('/logout', 'POST');

        $response = $handler->render($request, new HttpException(419, 'Page expired'));

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals(url('/'), $response->headers->get('Location'));
    }

    public function test_expired_csrf_on_public_logout_gracefully_logs_out_and_redirects_to_home(): void
    {
        $handler = app(ExceptionHandler::class);
        $request = Request::create('/public/logout', 'POST');

        $response = $handler->render($request, new HttpException(419, 'Page expired'));

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals(url('/'), $response->headers->get('Location'));
    }

    public function test_expired_csrf_on_register_redirects_gracefully_without_419(): void
    {
        $handler = app(ExceptionHandler::class);
        $request = Request::create('/public/register', 'POST', [
            'name' => 'Budi',
            'email' => 'budi@example.com',
        ]);

        $response = $handler->render($request, new TokenMismatchException());

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals(route('public.register'), $response->headers->get('Location'));
    }
}
