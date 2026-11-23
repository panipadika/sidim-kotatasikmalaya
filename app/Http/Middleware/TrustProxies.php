<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     *
     * @var array<int, string>|string|null
     */
    protected $proxies;

    /**
     * The headers that should be used to detect proxies.
     *
     * @var int
     */
    protected $headers =
        Request::HEADER_X_FORWARDED_FOR |
        Request::HEADER_X_FORWARDED_HOST |
        Request::HEADER_X_FORWARDED_PORT |
        Request::HEADER_X_FORWARDED_PROTO |
        Request::HEADER_X_FORWARDED_AWS_ELB;

    /**
     * Get the trusted proxies.
     *
     * @return array<int, string>|string|null
     */
    protected function proxies()
    {
        $trusted = env('TRUSTED_PROXIES');

        if ($trusted === '*' || $trusted === '**') {
            return $trusted;
        }

        if (!empty($trusted)) {
            return array_values(array_filter(array_map('trim', explode(',', $trusted))));
        }

        // Secara default, hanya percaya reverse proxy lokal (127.0.0.1 / ::1)
        // Jika di belakang reverse proxy lain, daftarkan IP-nya di TRUSTED_PROXIES (.env)
        return [
            '127.0.0.1',
            '::1',
        ];
    }
}
