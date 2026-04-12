<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureDemoSsoEnabled
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->demoSsoAllowed()) {
            abort(404);
        }

        return $next($request);
    }

    private function demoSsoAllowed(): bool
    {
        return app()->isLocal() || (bool) config('demo.sso', false);
    }
}
