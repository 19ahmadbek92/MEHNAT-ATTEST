<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttestationCampaign;
use Illuminate\Http\Request;

class AttestationCampaignController extends Controller
{
    public function index()
    {
        $campaigns = AttestationCampaign::latest()->paginate(10);

        return view('admin.campaigns.index', compact('campaigns'));
    }

    public function show(AttestationCampaign $campaign)
    {
        $campaign->load('applications');

        return view('admin.campaigns.show', compact('campaign'));
    }

    public function create()
    {
        return view('admin.campaigns.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'status' => ['required', 'in:draft,open,closed'],
        ]);

        AttestationCampaign::create($data);

        return redirect()->route('admin.campaigns.index')
            ->with('status', 'Kampaniya muvaffaqiyatli yaratildi.');
    }

    public function edit(AttestationCampaign $campaign)
    {
        return view('admin.campaigns.edit', compact('campaign'));
    }

    public function update(Request $request, AttestationCampaign $campaign)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'status' => ['required', 'in:draft,open,closed'],
        ]);

        $campaign->update($data);

        return redirect()->route('admin.campaigns.index')
            ->with('status', 'Kampaniya yangilandi.');
    }

    public function destroy(AttestationCampaign $campaign)
    {
        $campaign->delete();

        return redirect()->route('admin.campaigns.index')
            ->with('status', 'Kampaniya o‘chirildi.');
    }
}
