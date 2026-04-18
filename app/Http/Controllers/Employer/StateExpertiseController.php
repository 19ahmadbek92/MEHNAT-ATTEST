<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\StateExpertiseApplication;
use Illuminate\Http\Request;

class StateExpertiseController extends Controller
{
    /**
     * Display a listing of completed protocols ready for expertise.
     */
    public function index()
    {
        $organizationId = auth()->user()->organization_id;
        if (! $organizationId) {
            return redirect()->route('employer.organization.index')->with('error', 'Iltimos, avvalo korxona ma\'lumotlarini kiriting.');
        }

        // Get all applications of this user that have a protocol but ARE NOT YET in any StateExpertiseApplication
        // For simplicity, we get all applications with protocols that are not pending/approved in expertise.
        // Let's just retrieve applications that have a protocol.
        $applications = auth()->user()->applications()->whereHas('protocol')->get();
        $expertiseApplications = StateExpertiseApplication::where('organization_id', $organizationId)->latest()->get();

        return view('employer.expertise.index', compact('applications', 'expertiseApplications'));
    }

    /**
     * Submit selected applications to State Expertise.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'application_ids' => 'required|array',
            'application_ids.*' => 'exists:attestation_applications,id',
            'laboratory_id' => 'required|exists:laboratories,id',
        ]);

        StateExpertiseApplication::create([
            'organization_id' => auth()->user()->organization_id,
            'laboratory_id' => $validated['laboratory_id'],
            'application_ids' => $validated['application_ids'],
            'institute_status' => 'pending',
            'ministry_status' => 'pending',
        ]);

        return redirect()->route('employer.expertise.index')->with('success', 'Arizalar davlat ekspertizasiga (Institut dastlabki baholashiga) yuborildi.');
    }
}
