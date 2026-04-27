<?php

namespace App\Http\Controllers;

use App\Models\AttestationApplication;
use App\Models\AttestationProtocol;
use App\Models\AuditLog;
use App\Models\StateExpertiseApplication;
use App\Models\Workplace;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = Auth::user();
        $role = $user->role;

        return view('dashboard', [
            'user' => $user,
            'role' => $role,
            'stats' => $this->statsForRole($role, $user),
            'chart' => $this->chartForRole($role, $user),
            'recent' => $this->recentActivity($role, $user),
        ]);
    }

    /**
     * Build the four-tile statistic strip for the current role.
     *
     * @return array<int, array{icon:string, value:int|string, label:string, color:string}>
     */
    private function statsForRole(string $role, $user): array
    {
        return match ($role) {
            'admin', 'expert' => [
                ['icon' => '▣', 'value' => AttestationApplication::count(),                                          'label' => 'Jami arizalar',     'color' => 'teal'],
                ['icon' => '◷', 'value' => AttestationApplication::where('status', 'submitted')->count(),            'label' => 'Yangi arizalar',    'color' => 'gold'],
                ['icon' => '✓', 'value' => AttestationApplication::where('status', 'finalized')->count(),            'label' => 'Yakunlangan',       'color' => 'green'],
                ['icon' => '◉', 'value' => StateExpertiseApplication::where('ministry_status', 'pending')->count(),  'label' => 'Vazirlikda',        'color' => 'red'],
            ],
            'hr' => [
                ['icon' => '▣', 'value' => AttestationApplication::count(),                                  'label' => 'Jami arizalar',  'color' => 'teal'],
                ['icon' => '◷', 'value' => AttestationApplication::where('status', 'submitted')->count(),    'label' => 'Tekshiruvda',    'color' => 'gold'],
                ['icon' => '✓', 'value' => AttestationApplication::where('status', 'hr_approved')->count(),  'label' => 'Tasdiqlangan',   'color' => 'green'],
                ['icon' => '✗', 'value' => AttestationApplication::where('status', 'hr_rejected')->count(),  'label' => 'Rad etilgan',    'color' => 'red'],
            ],
            'employer' => $this->employerStats($user),
            'laboratory' => [
                ['icon' => '▣', 'value' => AttestationProtocol::count(),                              'label' => 'Jami protokollar', 'color' => 'teal'],
                ['icon' => '◷', 'value' => AttestationProtocol::where('status', 'draft')->count(),    'label' => 'Tayyorlanmoqda',   'color' => 'gold'],
                ['icon' => '✓', 'value' => AttestationProtocol::where('status', 'completed')->count(), 'label' => 'Yakunlangan',      'color' => 'green'],
                ['icon' => '⌬', 'value' => Workplace::where('status', 'in_progress')->count(),        'label' => 'O‘lchov jarayoni', 'color' => 'blue'],
            ],
            'institute_expert' => [
                ['icon' => '◷', 'value' => StateExpertiseApplication::where('institute_status', 'pending')->count(),  'label' => 'Kutilayotgan',  'color' => 'gold'],
                ['icon' => '✓', 'value' => StateExpertiseApplication::where('institute_status', 'approved')->count(), 'label' => 'Tasdiqlangan',  'color' => 'green'],
                ['icon' => '✗', 'value' => StateExpertiseApplication::where('institute_status', 'rejected')->count(), 'label' => 'Rad etilgan',   'color' => 'red'],
                ['icon' => '▣', 'value' => StateExpertiseApplication::count(),                                        'label' => 'Jami',          'color' => 'teal'],
            ],
            'commission' => [
                ['icon' => '◷', 'value' => AttestationApplication::where('status', 'hr_approved')->count(), 'label' => 'Baholash uchun', 'color' => 'gold'],
                ['icon' => '✓', 'value' => AttestationApplication::where('status', 'finalized')->count(),   'label' => 'Yakunlangan',    'color' => 'green'],
                ['icon' => '▣', 'value' => AttestationApplication::count(),                                 'label' => 'Jami arizalar',  'color' => 'teal'],
                ['icon' => '⌬', 'value' => Workplace::count(),                                              'label' => 'Ish o‘rinlari',  'color' => 'blue'],
            ],
            default => [],
        };
    }

    /**
     * @return array<int, array{icon:string, value:int|string, label:string, color:string}>
     */
    private function employerStats($user): array
    {
        $orgId = $user->organization_id;
        if (! $orgId) {
            return [];
        }

        return [
            ['icon' => '⌂', 'value' => Workplace::where('organization_id', $orgId)->count(),                                'label' => 'Ish o‘rinlari',     'color' => 'teal'],
            ['icon' => '◷', 'value' => Workplace::where('organization_id', $orgId)->where('status', 'pending')->count(),    'label' => 'Kutilmoqda',        'color' => 'gold'],
            ['icon' => '⚙', 'value' => Workplace::where('organization_id', $orgId)->where('status', 'in_progress')->count(), 'label' => 'Jarayonda',         'color' => 'blue'],
            ['icon' => '✓', 'value' => Workplace::where('organization_id', $orgId)->where('status', 'attested')->count(),   'label' => 'Attestatsiyalangan', 'color' => 'green'],
        ];
    }

    /**
     * @return array{labels: array<int,string>, data: array<int,int>, colors: array<int,string>, title: string}|null
     */
    private function chartForRole(string $role, $user): ?array
    {
        return match ($role) {
            'admin', 'expert' => [
                'title' => 'Davlat ekspertizasi taqsimoti',
                'labels' => ['Kutayotgan', 'Qaytarilgan', 'Tasdiqlangan'],
                'data' => [
                    StateExpertiseApplication::where('ministry_status', 'pending')->count(),
                    StateExpertiseApplication::where('ministry_status', 'returned')->count(),
                    StateExpertiseApplication::where('ministry_status', 'approved')->count(),
                ],
                'colors' => ['#c9952a', '#1a4db0', '#176b3a'],
            ],
            'hr' => [
                'title' => 'Arizalar holati',
                'labels' => ['Yangi', 'Tasdiqlangan', 'Rad etilgan', 'Yakunlangan'],
                'data' => [
                    AttestationApplication::where('status', 'submitted')->count(),
                    AttestationApplication::where('status', 'hr_approved')->count(),
                    AttestationApplication::where('status', 'hr_rejected')->count(),
                    AttestationApplication::where('status', 'finalized')->count(),
                ],
                'colors' => ['#c9952a', '#1a4db0', '#b83232', '#176b3a'],
            ],
            'employer' => $this->employerChart($user),
            'laboratory' => [
                'title' => 'Protokollar holati',
                'labels' => ['Tayyorlanmoqda', 'Tekshirilmoqda', 'Yakunlangan'],
                'data' => [
                    AttestationProtocol::where('status', 'draft')->count(),
                    AttestationProtocol::where('status', 'submitted')->count(),
                    AttestationProtocol::where('status', 'completed')->count(),
                ],
                'colors' => ['#c9952a', '#1a4db0', '#176b3a'],
            ],
            default => null,
        };
    }

    /**
     * @return array{labels: array<int,string>, data: array<int,int>, colors: array<int,string>, title: string}|null
     */
    private function employerChart($user): ?array
    {
        $orgId = $user->organization_id;
        if (! $orgId) {
            return null;
        }

        return [
            'title' => 'Ish o‘rinlari holati',
            'labels' => ['Kutilmoqda', 'Jarayonda', 'Attestatsiyalangan'],
            'data' => [
                Workplace::where('organization_id', $orgId)->where('status', 'pending')->count(),
                Workplace::where('organization_id', $orgId)->where('status', 'in_progress')->count(),
                Workplace::where('organization_id', $orgId)->where('status', 'attested')->count(),
            ],
            'colors' => ['#c9952a', '#1a4db0', '#176b3a'],
        ];
    }

    /**
     * Recent audit-log entries for display in the activity feed.
     *
     * @return array<int, array{title:string, sub:string, when:string, icon:string, color:string}>
     */
    private function recentActivity(string $role, $user): array
    {
        if (! Schema::hasTable('audit_logs')) {
            return [];
        }

        $query = AuditLog::query()->latest()->limit(8);

        if (! in_array($role, ['admin', 'expert'], true)) {
            $query->where('user_id', $user->id);
        }

        return $query->get()->map(function (AuditLog $log) {
            $action = $log->action;
            [$icon, $color] = $this->iconForAction($action);

            return [
                'title' => $this->humanizeAction($action),
                'sub' => trim(($log->subject_type ? class_basename($log->subject_type) : '').' #'.($log->subject_id ?? '')),
                'when' => optional($log->created_at)->diffForHumans() ?? '',
                'icon' => $icon,
                'color' => $color,
            ];
        })->all();
    }

    /**
     * @return array{0:string,1:string}
     */
    private function iconForAction(string $action): array
    {
        return match (true) {
            str_contains($action, 'approve') => ['✓', 'green'],
            str_contains($action, 'reject') => ['✗', 'red'],
            str_contains($action, 'finalize') => ['◉', 'green'],
            str_contains($action, 'submit') => ['↑', 'blue'],
            str_contains($action, 'create') => ['+', 'teal'],
            str_contains($action, 'update') => ['✎', 'gold'],
            str_contains($action, 'delete') => ['✗', 'red'],
            str_contains($action, 'login') => ['→', 'blue'],
            default => ['◷', 'teal'],
        };
    }

    private function humanizeAction(string $action): string
    {
        return ucfirst(str_replace(['_', '.', '-'], ' ', $action));
    }
}
