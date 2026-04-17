<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuditLogger
{
    public function log(Request $request, string $action, ?Model $subject = null, array $meta = []): void
    {
        try {
            AuditLog::create([
                'user_id' => optional($request->user())->id,
                'action' => $action,
                'subject_type' => $subject ? $subject::class : null,
                'subject_id' => $subject?->getKey(),
                'ip_address' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 1000),
                'meta' => $meta,
            ]);
        } catch (\Throwable $e) {
            // Audit yozuvi yiqilsa ham asosiy biznes jarayon to'xtamasin.
            Log::warning('audit_log_write_failed', [
                'action' => $action,
                'subject_type' => $subject ? $subject::class : null,
                'subject_id' => $subject?->getKey(),
                'error' => $e->getMessage(),
            ]);
        }
    }
}
