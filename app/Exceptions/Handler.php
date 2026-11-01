<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $e)
    {
        if ($e instanceof \Illuminate\Session\TokenMismatchException || ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException && $e->getStatusCode() === 419)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Sesi Anda telah kedaluwarsa. Silakan muat ulang halaman.',
                ], 419);
            }

            // Jika sedang logout saat sesi kedaluwarsa, selesaikan logout secara aman tanpa layar 419
            if ($request->is('logout') || $request->is('public/logout')) {
                \Illuminate\Support\Facades\Auth::guard('web')->logout();
                \Illuminate\Support\Facades\Auth::guard('admin')->logout();
                if ($request->hasSession()) {
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                }

                return redirect('/')->with('status', 'Anda telah berhasil keluar.');
            }

            // Jika form login admin
            $adminLoginPath = \App\Services\AdminLoginPathService::getPath();
            if ($request->is($adminLoginPath) || $request->is('login')) {
                return redirect()->route('login')
                    ->withInput($request->except('password', 'password_confirmation', '_token'))
                    ->with('error', 'Sesi halaman login telah kedaluwarsa karena tidak ada aktivitas. Silakan masukkan password kembali.');
            }

            // Jika form login publik
            if ($request->is('public/login')) {
                return redirect()->route('public.login')
                    ->withInput($request->except('password', 'password_confirmation', '_token'))
                    ->with('error', 'Sesi halaman login telah kedaluwarsa karena tidak ada aktivitas. Silakan masukkan password kembali.');
            }

            // Jika form registrasi publik
            if ($request->is('public/register')) {
                return redirect()->route('public.register')
                    ->withInput($request->except('password', 'password_confirmation', '_token'))
                    ->with('error', 'Sesi halaman pendaftaran telah kedaluwarsa. Silakan kirim ulang formulir.');
            }

            // Formulir lainnya
            return redirect()->back()
                ->withInput($request->except('password', 'password_confirmation', '_token'))
                ->with('error', 'Sesi halaman telah kedaluwarsa karena tidak ada aktivitas. Silakan coba kembali.');
        }

        return parent::render($request, $e);
    }
}
