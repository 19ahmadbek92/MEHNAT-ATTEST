<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\AttestationApplication;
use App\Models\AttestationCampaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ApplicationController extends Controller
{
    public function index()
    {
        $applications = AttestationApplication::with(['campaign', 'protocol'])
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(15);

        return view('employee.applications.index', compact('applications'));
    }

    public function show(AttestationApplication $application)
    {
        // Only owner can view
        if ($application->user_id !== auth()->id()) abort(403);

        $application->load(['campaign', 'protocol', 'evaluations']);
        return view('employee.applications.show', compact('application'));
    }

    public function create()
    {
        if (!auth()->user()->organization_id) {
            return redirect()->route('employer.organization.index')
                ->with('error', 'Iltimos, avvalo korxona ma\'lumotlarini kiriting.');
        }

        $openCampaigns = AttestationCampaign::where('status', 'open')->get();
        return view('employee.applications.create', compact('openCampaigns'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'campaign_id'           => ['required', 'exists:attestation_campaigns,id'],
            'workplace_name'        => ['required', 'string', 'max:255'],
            'department'            => ['nullable', 'string', 'max:255'],
            'employee_count'        => ['nullable', 'integer', 'min:1'],
            'workplace_description' => ['nullable', 'string'],
            'hazard_factors'        => ['nullable', 'array'],
            'hazard_factors.*'      => ['string'],
            'equipment_list'        => ['nullable', 'string'],
            'protective_equipment'  => ['nullable', 'string'],
            'workplace_photo'       => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:10240'],
            'documents'             => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
        ]);

        $data['user_id']         = auth()->id();
        $data['organization_id'] = auth()->user()->organization_id; // ← Fix: set organization_id
        $data['status']          = 'submitted';

        if ($request->hasFile('workplace_photo')) {
            $data['workplace_photo_path'] = $request->file('workplace_photo')->store('workplace_photos', 'public');
        }
        if ($request->hasFile('documents')) {
            $data['documents_path'] = $request->file('documents')->store('documents', 'public');
        }

        $application = AttestationApplication::create($data);

        return redirect()->route('employee.applications.index')
            ->with('success', 'Ish o\'rni attestatsiyaga muvaffaqiyatli taqdim etildi.');
    }
}
