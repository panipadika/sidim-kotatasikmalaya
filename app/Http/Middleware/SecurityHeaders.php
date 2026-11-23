<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request and attach recommended security headers.
     *
     * @param  \Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        // Mencegah Clickjacking: Hanya izinkan framing dari domain yang sama
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Mencegah browser menebak (sniffing) tipe MIME di luar Content-Type yang dideklarasikan
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Filter proteksi XSS pada browser lama
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Melindungi privasi referer saat user berpindah halaman
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Menonaktifkan fitur perangkat keras yang tidak dibutuhkan aplikasi dashboard
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');

        // Aktifkan HSTS (HTTP Strict Transport Security) jika request berjalan di atas HTTPS
        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}

