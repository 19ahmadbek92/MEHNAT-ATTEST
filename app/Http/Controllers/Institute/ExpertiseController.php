<?php

namespace App\Http\Controllers\Institute;

use App\Http\Controllers\Controller;
use App\Models\StateExpertiseApplication;
use Illuminate\Http\Request;

class ExpertiseController extends Controller
{
    public function index()
    {
        // Muddat o'tgan arizalarni avtomatik tasdiqlash — §33
        StateExpertiseApplication::where('institute_status', 'pending')
            ->where('ministry_status', 'pending')
            ->where('is_auto_approved', false)
            ->get()
            ->each(fn($app) => $app->autoApproveIfOverdue());

        // Institut ko'rishi kerak bo'lgan arizalar (15 kun muddat bilan)
        $applications = StateExpertiseApplication::with(['organization', 'laboratory'])
            ->where('institute_status', 'pending')
            ->where('is_auto_approved', false)
            ->orderBy('submitted_at')
            ->get();

        $history = StateExpertiseApplication::with(['organization', 'instituteExpert'])
            ->whereIn('institute_status', ['approved', 'returned'])
            ->latest('institute_reviewed_at')
            ->get();

        return view('institute.expertise.index', compact('applications', 'history'));
    }

    public function show(StateExpertiseApplication $expertise)
    {
        $expertise->load(['organization', 'laboratory', 'instituteExpert']);
        return view('institute.expertise.show', compact('expertise'));
    }

    public function process(Request $request, StateExpertiseApplication $expertise)
    {
        if ($expertise->institute_status !== 'pending') {
            return back()->with('error', 'Bu ariza allaqachon ko\'rib chiqilgan.');
        }

        $validated = $request->validate([
            'action'          => 'required|in:approve,return',
            'comment'         => 'nullable|string|max:2000',
            // Tartibnoma 31-band: Qaytarishda ANIQ NORMA ko'rsatilishi shart
            'return_reason'   => 'required_if:action,return|string|max:2000',
            'return_legal_ref'=> 'required_if:action,return|string|max:500',
            // Masalan: "Nizom ilova №1, 17-band; SanQvaM 0069-24 16-ilova 3.2-band"
            'return_days'     => 'required_if:action,return|integer|min:10',
            // Minimum 10 ish kuni (Tartibnoma 31-band)
        ]);

        if ($validated['action'] === 'approve') {
            $expertise->update([
                'institute_status'      => 'approved',
                'institute_expert_id'   => auth()->id(),
                'institute_comment'     => $validated['comment'],
                'institute_reviewed_at' => now(),
            ]);
            // booted() hook Vazirlik muddatini (10 kun) avtomatik o'rnatadi

            return redirect()->route('institute.expertise.index')
                ->with('success', '✅ Dastlabki baholash ma\'qullandi. Ariza Vazirlik davlat ekspertizasiga yo\'llantirildi.');

        } else {
            // Qaytarish — Tartibnoma 31-band
            $returnDeadline = now()->addWeekdays($validated['return_days']);

            $expertise->update([
                'institute_status'          => 'returned',
                'institute_expert_id'       => auth()->id(),
                'institute_comment'         => $validated['comment'],
                'institute_return_reason'   => $validated['return_reason'],
                'institute_return_legal_ref'=> $validated['return_legal_ref'],
                'institute_return_deadline' => $returnDeadline->toDateString(),
                'institute_reviewed_at'     => now(),
            ]);

            return redirect()->route('institute.expertise.index')
                ->with('warning', '↩️ Hujjatlar qayta ishlash uchun qaytarildi. Qayta topshirish muddati: ' . $returnDeadline->format('d.m.Y'));
        }
    }
}
