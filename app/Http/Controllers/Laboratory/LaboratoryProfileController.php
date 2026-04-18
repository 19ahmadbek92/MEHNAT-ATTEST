<?php

namespace App\Http\Controllers\Laboratory;

use App\Http\Controllers\Controller;
use App\Models\Laboratory;
use Illuminate\Http\Request;

class LaboratoryProfileController extends Controller
{
    public function index()
    {
        $laboratory = auth()->user()->laboratory;

        return view('laboratory.profile.index', compact('laboratory'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'stir_inn' => 'required|string|max:14|unique:laboratories,stir_inn',
            'accreditation_certificate_number' => 'required|string|max:100',
            'accreditation_expiry_date' => 'required|date',
            'accreditation_scope' => 'required|string',
        ]);

        $validated['is_active'] = true;

        $laboratory = Laboratory::create($validated);

        $user = auth()->user();
        $user->laboratory_id = $laboratory->id;
        $user->save();

        return redirect()->route('laboratory.profile.index')->with('success', 'Laboratoriya ma\'lumotlari saqlandi.');
    }

    public function update(Request $request, Laboratory $laboratory)
    {
        if (auth()->user()->laboratory_id !== $laboratory->id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'stir_inn' => 'required|string|max:14|unique:laboratories,stir_inn,'.$laboratory->id,
            'accreditation_certificate_number' => 'required|string|max:100',
            'accreditation_expiry_date' => 'required|date',
            'accreditation_scope' => 'required|string',
        ]);

        $laboratory->update($validated);

        return redirect()->route('laboratory.profile.index')->with('success', 'Laboratoriya ma\'lumotlari yangilandi.');
    }
}
