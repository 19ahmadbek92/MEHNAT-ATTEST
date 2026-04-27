<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\Workplace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkplaceController extends Controller
{
    public function index()
    {
        $organization = Auth::user()->organization;
        if (! $organization) {
            return redirect()->route('employer.organization.index')->with('error', 'Iltimos, avval tashkilot profilingizni tasdiqlang.');
        }

        $workplaces = $organization->workplaces()->latest()->paginate(10);

        return view('employer.workplaces.index', compact('workplaces'));
    }

    public function create()
    {
        return view('employer.workplaces.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'department' => 'nullable|string|max:255',
            'employees_count' => 'required|integer|min:1',
            'description' => 'nullable|string',
        ]);

        $organization = Auth::user()->organization;
        if (! $organization) {
            return redirect()->route('employer.organization.index')
                ->with('error', 'Iltimos, avval tashkilot profilingizni tasdiqlang.');
        }

        $organization->workplaces()->create($validated);

        return redirect()->route('employer.workplaces.index')->with('success', 'Ish o\'rni tizimga kiritildi.');
    }

    public function show(Workplace $workplace)
    {
        if ($workplace->organization_id !== Auth::user()->organization_id) {
            abort(403);
        }

        $workplace->load(['measurements.laboratory', 'employees']);

        return view('employer.workplaces.show', compact('workplace'));
    }

    public function print(Workplace $workplace)
    {
        if ($workplace->organization_id !== Auth::user()->organization_id) {
            abort(403);
        }

        $workplace->load(['measurements.laboratory', 'organization']);

        return view('employer.workplaces.print', compact('workplace'));
    }
}
