<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSsoEntryEnabled
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->ssoEntryAllowed()) {
            abort(404);
        }

        return $next($request);
    }

    private function ssoEntryAllowed(): bool
    {
        if (app()->isLocal()) {
            return true;
        }

        if ((bool) config('demo.sso', false)) {
            return true;
        }

        return (bool) config('identity.sso_routes_enabled', true);
    }
}
