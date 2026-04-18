<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\AttestationApplication;
use App\Services\AuditLogger;
use Illuminate\Http\Request;

class ApplicationReviewController extends Controller
{
    public function __construct(private readonly AuditLogger $auditLogger) {}

    public function index(Request $request)
    {
        $status = $request->query('status', 'submitted');

        $allowed = [
            AttestationApplication::STATUS_SUBMITTED,
            AttestationApplication::STATUS_HR_APPROVED,
            AttestationApplication::STATUS_HR_REJECTED,
            AttestationApplication::STATUS_FINALIZED,
        ];
        if (! in_array($status, $allowed, true)) {
            $status = AttestationApplication::STATUS_SUBMITTED;
        }

        $applications = AttestationApplication::with(['user', 'campaign'])
            ->where('status', $status)
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('hr.applications.index', compact('applications', 'status'));
    }

    public function show(AttestationApplication $application)
    {
        $application->load(['user', 'campaign', 'evaluations.evaluator']);

        return view('hr.applications.show', compact('application'));
    }

    public function approve(Request $request, AttestationApplication $application)
    {
        if (! $application->canTransitionTo(AttestationApplication::STATUS_HR_APPROVED)) {
            return back()->with('status', 'Bu ariza allaqachon ko\'rib chiqilgan.');
        }

        $data = $request->validate([
            'hr_comment' => ['nullable', 'string'],
        ]);

        $application->transitionTo(AttestationApplication::STATUS_HR_APPROVED, [
            'hr_reviewed_by' => $request->user()->id,
            'hr_reviewed_at' => now(),
            'hr_comment' => $data['hr_comment'] ?? null,
        ]);

        $this->auditLogger->log($request, 'hr.application.approved', $application, [
            'status' => $application->status,
        ]);

        return redirect()->route('hr.applications.index', ['status' => AttestationApplication::STATUS_SUBMITTED])
            ->with('status', 'Ariza tasdiqlandi (Ekspertiza).');
    }

    public function reject(Request $request, AttestationApplication $application)
    {
        if (! $application->canTransitionTo(AttestationApplication::STATUS_HR_REJECTED)) {
            return back()->with('status', 'Bu ariza allaqachon ko\'rib chiqilgan.');
        }

        $data = $request->validate([
            'hr_comment' => ['required', 'string'],
        ]);

        $application->transitionTo(AttestationApplication::STATUS_HR_REJECTED, [
            'hr_reviewed_by' => $request->user()->id,
            'hr_reviewed_at' => now(),
            'hr_comment' => $data['hr_comment'],
            'final_decision' => 'fail',
        ]);

        $this->auditLogger->log($request, 'hr.application.rejected', $application, [
            'status' => $application->status,
            'comment' => $data['hr_comment'],
        ]);

        return redirect()->route('hr.applications.index', ['status' => AttestationApplication::STATUS_SUBMITTED])
            ->with('status', 'Ariza rad etildi (Ekspertiza).');
    }

    public function finalize(Request $request, AttestationApplication $application)
    {
        if (! $application->canTransitionTo(AttestationApplication::STATUS_FINALIZED)) {
            abort(403, 'Yakunlash uchun ruxsat yo\'q.');
        }

        $data = $request->validate([
            'workplace_class' => ['required', 'in:optimal,ruxsat_etilgan,zararli_xavfli'],
        ]);

        $application->transitionTo(AttestationApplication::STATUS_FINALIZED, [
            'workplace_class' => $data['workplace_class'],
            'final_decision' => $data['workplace_class'],
            'finalized_by' => $request->user()->id,
            'finalized_at' => now(),
        ]);

        $this->auditLogger->log($request, 'hr.application.finalized', $application, [
            'workplace_class' => $data['workplace_class'],
        ]);

        return redirect()->route('hr.applications.show', $application)
            ->with('status', 'Yakuniy natija saqlandi.');
    }
}
