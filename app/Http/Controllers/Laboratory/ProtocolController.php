<?php

namespace App\Http\Controllers\Laboratory;

use App\Http\Controllers\Controller;
use App\Models\AttestationApplication;
use App\Models\AttestationProtocol;
use App\Models\AttestationTender;
use Illuminate\Http\Request;

class ProtocolController extends Controller
{
    public function index()
    {
        $laboratoryId = auth()->user()->laboratory_id;
        if (!$laboratoryId) {
            return redirect()->route('laboratory.profile.index')
                ->with('error', 'Iltimos, avvalo laboratoriya profilingizni to\'ldiring.');
        }

        // Get organization IDs from awarded/completed tenders of this laboratory
        $organizationIds = AttestationTender::where('laboratory_id', $laboratoryId)
            ->whereIn('status', ['awarded', 'completed', 'open'])
            ->pluck('organization_id');

        // Get applications belonging to those organizations that don't have a protocol yet
        $applications = AttestationApplication::with(['protocol', 'organization'])
            ->whereIn('organization_id', $organizationIds)
            ->latest()
            ->get();

        return view('laboratory.protocols.index', compact('applications'));
    }

    public function create(AttestationApplication $application)
    {
        if (!auth()->user()->laboratory_id) abort(403);

        // Verify this lab is assigned to this org via a tender
        $laboratoryId = auth()->user()->laboratory_id;
        $hasTender = AttestationTender::where('laboratory_id', $laboratoryId)
            ->where('organization_id', $application->organization_id)
            ->exists();

        if (!$hasTender) abort(403, 'Bu ariza sizning laboratoriyangizga tegishli emas.');

        $protocol = AttestationProtocol::where('application_id', $application->id)->first();
        return view('laboratory.protocols.create', compact('application', 'protocol'));
    }

    public function store(Request $request, AttestationApplication $application)
    {
        $validated = $request->validate([
            // ── 18 SanQvaM 0069-24 omillari ──
            'chemical_factors'        => 'nullable|array',
            'fibrogenic_aerosols'     => 'nullable|array',
            'biological_factors'      => 'nullable|array',
            'noise_vibration_factors' => 'nullable|array',
            'emf_factors'             => 'nullable|array',
            'optical_radiation'       => 'nullable|array',
            'microclimate_factors'    => 'nullable|array',
            'lighting_factors'        => 'nullable|array',
            'ionizing_radiation'      => 'nullable|array',
            'atmospheric_pressure'    => 'nullable|array',
            'work_severity_class'     => 'required|string|in:1,2,3.1,3.2,3.3,3.4,4',
            'work_intensity_class'    => 'required|string|in:1,2,3.1,3.2,3.3,3.4,4',
            'overall_class'           => 'required|string|in:1,2,3.1,3.2,3.3,3.4,4',
            // ── Jarohatlanish + YaTHV ──
            'injury_hazard_class'     => 'required|string|in:low,medium,high,critical',
            // ── Kafolatlar ──
            'requires_benefits'       => 'nullable|boolean',
            'additional_leave_days'   => 'nullable|integer|min:0|max:30',
            'reduced_work_hours'      => 'nullable|numeric|min:24|max:40',
            'has_medical_food'        => 'nullable|boolean',
            'has_therapeutic_nutrition' => 'nullable|boolean',
            // ── XALIKK-2024 ──
            'profession_code'         => 'nullable|string|max:50',
            'profession_name'         => 'nullable|string|max:255',
            // ── O'xshash ish o'rinlari ──
            'similar_workplaces_count'=> 'nullable|integer|min:1',
            'is_representative_sample'=> 'nullable|boolean',
        ]);

        // Boolean/nullable castlar
        $validated['requires_benefits']       = $request->boolean('requires_benefits');
        $validated['has_medical_food']        = $request->boolean('has_medical_food');
        $validated['has_therapeutic_nutrition']= $request->boolean('has_therapeutic_nutrition');
        $validated['is_representative_sample']= $request->boolean('is_representative_sample');

        // YaTHV (PPE) assessment JSON
        $validated['ppe_assessment'] = [
            'provided'  => $request->boolean('ppe_provided'),
            'certified' => $request->boolean('ppe_certified'),
            'condition' => $request->input('ppe_condition', 'satisfactory'),
        ];

        AttestationProtocol::updateOrCreate(
            ['application_id' => $application->id],
            array_merge($validated, ['laboratory_id' => auth()->user()->laboratory_id])
        );

        $application->update(['status' => 'commission_reviewed']);

        return redirect()->route('laboratory.protocols.index')
            ->with('success', "✅ #{$application->id} ({$application->workplace_name}) protokoli 18-omil sifatida saqlandi.");
    }
}
