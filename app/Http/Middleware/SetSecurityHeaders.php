<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetSecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        // Tightened CSP after migrating Chart.js to bundled Vite output and the
        // landing page to Bunny Fonts. We keep googleapis as a fallback for any
        // legacy templates that still pull from there.
        $response->headers->set(
            'Content-Security-Policy',
            "default-src 'self'; "
            ."img-src 'self' data: https:; "
            ."style-src 'self' 'unsafe-inline' https://fonts.bunny.net https://fonts.googleapis.com; "
            ."font-src 'self' data: https://fonts.bunny.net https://fonts.gstatic.com; "
            ."script-src 'self' 'unsafe-inline'; "
            ."connect-src 'self'; "
            ."frame-ancestors 'none'; "
            ."base-uri 'self'; "
            ."form-action 'self'"
        );

        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}
