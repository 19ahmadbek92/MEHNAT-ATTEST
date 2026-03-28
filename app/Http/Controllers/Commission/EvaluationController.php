<?php

namespace App\Http\Controllers\Commission;

use App\Http\Controllers\Controller;
use App\Models\AttestationApplication;
use App\Models\AttestationEvaluation;
use Illuminate\Http\Request;

class EvaluationController extends Controller
{
    public function index()
    {
        $applications = AttestationApplication::with(['user', 'campaign'])
            ->where('status', 'hr_approved')
            ->latest()
            ->paginate(10);

        return view('commission.evaluations.index', compact('applications'));
    }

    public function evaluateForm(AttestationApplication $application)
    {
        if ($application->status !== 'hr_approved') {
            abort(403, 'Ushbu ish o\'rni tekshirish uchun ruxsat etilmagan.');
        }

        $application->load(['user', 'campaign', 'evaluations']);

        return view('commission.evaluations.form', compact('application'));
    }

    public function storeOrUpdate(Request $request, AttestationApplication $application)
    {
        if ($application->status !== 'hr_approved') {
            abort(403, 'Ushbu ish o\'rni tekshirish uchun ruxsat etilmagan.');
        }

        $data = $request->validate([
            'noise_level' => ['nullable', 'numeric', 'min:0', 'max:200'],
            'dust_level' => ['nullable', 'numeric', 'min:0'],
            'vibration_level' => ['nullable', 'numeric', 'min:0'],
            'lighting_level' => ['nullable', 'numeric', 'min:0'],
            'microclimate' => ['nullable', 'string', 'max:255'],
            'chemical_factors' => ['nullable', 'string'],
            'equipment_hazard_score' => ['nullable', 'integer', 'min:0', 'max:100'],
            'protective_equipment_status' => ['nullable', 'in:yetarli,qisman,yetarli_emas'],
            'score' => ['required', 'integer', 'min:0', 'max:100'],
            'comment' => ['nullable', 'string'],
        ]);

        AttestationEvaluation::updateOrCreate(
            [
                'application_id' => $application->id,
                'evaluator_id' => $request->user()->id,
            ],
            $data
        );

        $application->recalculateFinalScore();

        return redirect()->route('commission.evaluations.index')
            ->with('status', 'Tekshiruv natijalari saqlandi.');
    }
}
