<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

/**
 * Lightweight in-app notification feed.
 *
 * The platform doesn't currently persist a dedicated `notifications` table —
 * instead we surface the latest entries from `audit_logs` that relate to
 * the current user. This keeps the UX immediate without adding a second
 * pipeline; richer functionality (read state, push) can be layered on later.
 */
class NotificationController extends Controller
{
    public function recent(Request $request): JsonResponse
    {
        if (! Schema::hasTable('audit_logs')) {
            return response()->json(['items' => []]);
        }

        $user = Auth::user();
        $role = $user->role;

        $query = AuditLog::query()
            ->with('user:id,name')
            ->latest()
            ->limit(8);

        if (! in_array($role, ['admin', 'expert'], true)) {
            $query->where(function ($q) use ($user) {
                $q->where('user_id', $user->id);

                if ($user->organization_id) {
                    $q->orWhereJsonContains('meta->organization_id', $user->organization_id);
                }
            });
        }

        $items = $query->get()->map(fn (AuditLog $log) => [
            'id' => $log->id,
            'title' => $this->humanize($log->action),
            'subject' => $log->subject_type ? class_basename($log->subject_type).' #'.$log->subject_id : null,
            'when' => optional($log->created_at)->diffForHumans(),
            'actor' => optional($log->user)->name,
        ])->all();

        return response()->json(['items' => $items]);
    }

    private function humanize(string $action): string
    {
        return ucfirst(str_replace(['_', '.', '-'], ' ', $action));
    }
}
