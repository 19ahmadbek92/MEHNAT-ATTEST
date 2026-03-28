<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $organization = auth()->user()->organization;
        return view('employer.organization.index', compact('organization'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'stir_inn' => 'required|string|max:14|unique:organizations,stir_inn',
            'ifut_code' => 'nullable|string|max:20',
            'mhobt_code' => 'nullable|string|max:20',
            'parent_organization' => 'nullable|string|max:255',
            'activity_type' => 'nullable|string|max:255',
            'legal_address' => 'nullable|string|max:500',
            'total_employees' => 'required|integer|min:0',
            'disabled_employees' => 'required|integer|min:0',
            'women_employees' => 'required|integer|min:0',
        ]);

        $organization = Organization::create($validated);
        
        $user = auth()->user();
        $user->organization_id = $organization->id;
        $user->save();

        return redirect()->route('employer.organization.index')->with('success', 'Korxona ma\'lumotlari saqlandi.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Organization $organization)
    {
        if (auth()->user()->organization_id !== $organization->id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'stir_inn' => 'required|string|max:14|unique:organizations,stir_inn,' . $organization->id,
            'ifut_code' => 'nullable|string|max:20',
            'mhobt_code' => 'nullable|string|max:20',
            'parent_organization' => 'nullable|string|max:255',
            'activity_type' => 'nullable|string|max:255',
            'legal_address' => 'nullable|string|max:500',
            'total_employees' => 'required|integer|min:0',
            'disabled_employees' => 'required|integer|min:0',
            'women_employees' => 'required|integer|min:0',
        ]);

        $organization->update($validated);

        return redirect()->route('employer.organization.index')->with('success', 'Korxona ma\'lumotlari yangilandi.');
    }
}
