<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $query = AuditLog::query()
            ->with('user:id,name,role')
            ->latest('created_at');

        if ($action = $request->query('action')) {
            $query->where('action', 'like', '%'.$action.'%');
        }

        if ($userId = $request->query('user_id')) {
            $query->where('user_id', $userId);
        }

        if ($from = $request->query('from')) {
            $query->whereDate('created_at', '>=', $from);
        }

        if ($to = $request->query('to')) {
            $query->whereDate('created_at', '<=', $to);
        }

        $logs = $query->paginate(30)->withQueryString();

        $availableActions = AuditLog::query()
            ->select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        $users = User::query()
            ->select('id', 'name', 'role')
            ->orderBy('name')
            ->get();

        return view('admin.audit-log.index', [
            'logs' => $logs,
            'availableActions' => $availableActions,
            'users' => $users,
            'filters' => [
                'action' => $request->query('action', ''),
                'user_id' => $request->query('user_id', ''),
                'from' => $request->query('from', ''),
                'to' => $request->query('to', ''),
            ],
        ]);
    }
}
