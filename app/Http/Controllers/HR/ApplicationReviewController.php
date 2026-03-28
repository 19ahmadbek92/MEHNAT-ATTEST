<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\AttestationApplication;
use Illuminate\Http\Request;

class ApplicationReviewController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'submitted');

        $allowed = ['submitted', 'hr_approved', 'hr_rejected', 'finalized'];
        if (!in_array($status, $allowed, true)) {
            $status = 'submitted';
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
        if ($application->status !== 'submitted') {
            return back()->with('status', 'Bu ariza allaqachon ko\'rib chiqilgan.');
        }

        $data = $request->validate([
            'hr_comment' => ['nullable', 'string'],
        ]);

        $application->update([
            'status' => 'hr_approved',
            'hr_reviewed_by' => $request->user()->id,
            'hr_reviewed_at' => now(),
            'hr_comment' => $data['hr_comment'] ?? null,
        ]);

        return redirect()->route('hr.applications.index', ['status' => 'submitted'])
            ->with('status', 'Ariza tasdiqlandi (Ekspertiza).');
    }

    public function reject(Request $request, AttestationApplication $application)
    {
        if ($application->status !== 'submitted') {
            return back()->with('status', 'Bu ariza allaqachon ko\'rib chiqilgan.');
        }

        $data = $request->validate([
            'hr_comment' => ['required', 'string'],
        ]);

        $application->update([
            'status' => 'hr_rejected',
            'hr_reviewed_by' => $request->user()->id,
            'hr_reviewed_at' => now(),
            'hr_comment' => $data['hr_comment'],
            'final_decision' => 'fail',
        ]);

        return redirect()->route('hr.applications.index', ['status' => 'submitted'])
            ->with('status', 'Ariza rad etildi (Ekspertiza).');
    }

    public function finalize(Request $request, AttestationApplication $application)
    {
        if (!in_array($application->status, ['hr_approved', 'finalized'], true)) {
            abort(403, 'Yakunlash uchun ruxsat yo\'q.');
        }

        $data = $request->validate([
            'workplace_class' => ['required', 'in:optimal,ruxsat_etilgan,zararli_xavfli'],
        ]);

        $application->update([
            'status' => 'finalized',
            'workplace_class' => $data['workplace_class'],
            'final_decision' => $data['workplace_class'],
            'finalized_by' => $request->user()->id,
            'finalized_at' => now(),
        ]);

        return redirect()->route('hr.applications.show', $application)
            ->with('status', 'Yakuniy natija saqlandi.');
    }
}
