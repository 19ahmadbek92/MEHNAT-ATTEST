<?php

namespace App\Http\Controllers\Ministry;

use App\Http\Controllers\Controller;
use App\Models\StateExpertiseApplication;
use App\Services\AuditLogger;
use Illuminate\Http\Request;

class ExpertiseController extends Controller
{
    public function __construct(private readonly AuditLogger $auditLogger) {}

    public function index()
    {
        // Faqat Institut ma'qullagan arizalar ko'rinadi (Tartibnoma 28-band)
        $applications = StateExpertiseApplication::with(['organization', 'laboratory'])
            ->where('institute_status', 'approved')
            ->where('ministry_status', 'pending')
            ->where('is_auto_approved', false)
            ->orderBy('submitted_at')
            ->get()
            ->each(fn ($app) => $app->autoApproveIfOverdue()); // 33-band tekshiruv

        $history = StateExpertiseApplication::with(['organization', 'ministryExpert'])
            ->where(function ($query) {
                $query->whereIn('ministry_status', ['approved', 'returned'])
                    ->orWhere('is_auto_approved', true);
            })
            ->latest()
            ->get();

        return view('ministry.expertise.index', compact('applications', 'history'));
    }

    public function show(StateExpertiseApplication $expertise)
    {
        $expertise->load(['organization', 'laboratory', 'instituteExpert', 'ministryExpert']);

        return view('ministry.expertise.show', compact('expertise'));
    }

    public function process(Request $request, StateExpertiseApplication $expertise)
    {
        // Allaqachon tasdiqlangan bo'lsa
        if ($expertise->ministry_status !== 'pending') {
            return back()->with('error', 'Bu ariza allaqachon ko\'rib chiqilgan.');
        }

        $validated = $request->validate([
            'action' => 'required|in:approve,return',
            'comment' => 'nullable|string|max:2000',
            'return_reason' => 'required_if:action,return|string|max:2000',
            // Tartibnoma 31-band: Aniq huquqiy norma ko'rsatilishi MAJBURIY
            'return_legal_ref' => 'required_if:action,return|string|max:500',
            // Masalan: "SanQvaM 0069-24 Ilova №3, 5-band; Nizom 28-band"
            'return_days' => 'required_if:action,return|integer|min:10',
            // Min 10 ish kuni (Tartibnoma 31-band)
        ]);

        if ($validated['action'] === 'approve') {
            // Xulosa raqami va blank raqami generatsiya qilish
            $conclusionNo = StateExpertiseApplication::generateConclusionNumber();
            $blankNo = StateExpertiseApplication::generateBlankNumber();

            $expertise->update([
                'ministry_status' => 'approved',
                'ministry_expert_id' => auth()->id(),
                'ministry_comment' => $validated['comment'],
                'ministry_reviewed_at' => now(),
                'conclusion_number' => $conclusionNo,
                'conclusion_series' => 'DX',
                'conclusion_blank_no' => $blankNo,
            ]);

            $this->auditLogger->log($request, 'ministry.expertise.approved', $expertise, [
                'conclusion_number' => $conclusionNo,
                'blank_number' => $blankNo,
            ]);

            // Fake (Mock) API integratsiya chaqirig'i
            $apiService = new \App\Services\MockIntegrationService;
            $apiService->sendConclusionToStateServices([
                'conclusion_no' => $conclusionNo,
                'organization_stir' => $expertise->organization->stir_inn ?? '000000000',
                'status' => 'approved',
                'date' => now()->toDateString(),
            ]);

            return redirect()->route('ministry.expertise.index')
                ->with('success', "✅ Davlat ekspertizasi xulosasi berildi. Xulosa raqami: **{$conclusionNo}** (Blank: {$blankNo})");

        } else {
            // Qaytarish — Tartibnoma 31-band
            $returnDeadline = now()->addWeekdays($validated['return_days']);

            $expertise->update([
                'ministry_status' => 'returned',
                'ministry_expert_id' => auth()->id(),
                'ministry_comment' => $validated['comment'],
                'ministry_return_reason' => $validated['return_reason'],
                'ministry_return_legal_ref' => $validated['return_legal_ref'],
                'ministry_return_deadline' => $returnDeadline->toDateString(),
                'ministry_reviewed_at' => now(),
            ]);

            $this->auditLogger->log($request, 'ministry.expertise.returned', $expertise, [
                'return_deadline' => $returnDeadline->toDateString(),
                'return_legal_ref' => $validated['return_legal_ref'],
            ]);

            return redirect()->route('ministry.expertise.index')
                ->with('warning', '↩️ Hujjatlar qayta ishlash uchun qaytarildi. Muddat: '.$returnDeadline->format('d.m.Y'));
        }
    }
}
