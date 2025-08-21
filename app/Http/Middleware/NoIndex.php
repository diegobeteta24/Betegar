<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NoIndex
{
    /**
     * Add X-Robots-Tag header to all responses to prevent indexing.
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        // Apply for all content types to cover HTML, JSON, PDF, etc.
        $response->headers->set('X-Robots-Tag', 'noindex, nofollow, noarchive, nosnippet, noimageindex');

        return $response;
    }
}
