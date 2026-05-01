<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * SecurityHeaders — adds security-related HTTP headers to all responses.
 *
 * Mitigates: clickjacking, MIME sniffing, XSS, and content injection attacks.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Prevent clickjacking — page cannot be embedded in iframes
        $response->headers->set('X-Frame-Options', 'DENY');

        // Prevent MIME type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Enable XSS filter in older browsers
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Control referrer information
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Restrict browser features
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');

        // Content Security Policy — restrict resource loading

        // these block the tarwind css 
        // $response->headers->set('Content-Security-Policy', implode('; ', [
        //     "default-src 'self'",
        //     "script-src 'self' 'unsafe-inline' 'unsafe-eval'",  
        //     "style-src 'self' 'unsafe-inline'", 
        //     "img-src 'self' data: blob:",
        //     "font-src 'self' data:",
        //     "connect-src 'self'",
        //     "frame-ancestors 'none'",
        //     "base-uri 'self'",
        //     "form-action 'self'",
        // ]));

        // Content Security Policy — restrict resource loading
        $response->headers->set('Content-Security-Policy', implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.tailwindcss.com https://cdn.jsdelivr.net",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
            "img-src 'self' data: blob: https:",
            "font-src 'self' data: https://fonts.gstatic.com",
            "connect-src 'self'",
            "frame-ancestors 'none'",
            "base-uri 'self'",
            "form-action 'self'",
        ]));

        // Allow same-origin framing for print previews only.
        if ($request->routeIs('procurement.lpo.print', 'procurement.grn.print')) {
            $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

            $csp = $response->headers->get('Content-Security-Policy');
            if ($csp) {
                $response->headers->set(
                    'Content-Security-Policy',
                    str_replace("frame-ancestors 'none'", "frame-ancestors 'self'", $csp)
                );
            }
        }

        // HSTS — enforce HTTPS (only in production)
        if (config('app.env') === 'production') {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        return $response;
    }
}
