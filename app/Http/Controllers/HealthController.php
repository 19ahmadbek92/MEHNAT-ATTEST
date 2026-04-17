<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class HealthController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $checks = [
            'database' => $this->databaseOk(),
            'cache' => $this->cacheOk(),
        ];

        $ok = !in_array(false, $checks, true);

        return response()->json([
            'status' => $ok ? 'ok' : 'degraded',
            'checks' => $checks,
            'timestamp' => now()->toIso8601String(),
        ], $ok ? 200 : 503);
    }

    private function databaseOk(): bool
    {
        try {
            DB::select('select 1');
            return true;
        } catch (\Throwable) {
            return false;
        }
    }

    private function cacheOk(): bool
    {
        try {
            $key = 'health:'.str()->random(8);
            Cache::put($key, 'ok', 10);
            return Cache::get($key) === 'ok';
        } catch (\Throwable) {
            return false;
        }
    }
}
