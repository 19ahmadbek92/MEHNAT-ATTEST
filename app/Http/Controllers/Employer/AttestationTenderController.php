<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\AttestationTender;
use App\Models\Laboratory;
use Illuminate\Http\Request;

class AttestationTenderController extends Controller
{
    public function index()
    {
        $organizationId = auth()->user()->organization_id;
        if (!$organizationId) {
            return redirect()->route('employer.organization.index')
                ->with('error', 'Iltimos, avvalo korxona ma\'lumotlarini kiriting.');
        }
        $tenders = AttestationTender::with('laboratory')
            ->where('organization_id', $organizationId)
            ->latest()->get();
        return view('employer.tenders.index', compact('tenders'));
    }

    public function create()
    {
        $organizationId = auth()->user()->organization_id;
        if (!$organizationId) {
            return redirect()->route('employer.organization.index')
                ->with('error', 'Iltimos, avvalo korxona ma\'lumotlarini kiriting.');
        }
        $laboratories = Laboratory::where('is_active', true)->get();
        return view('employer.tenders.create', compact('laboratories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'laboratory_id'    => 'required|exists:laboratories,id',
            'start_date'       => 'required|date',
            'end_date'         => 'required|date|after_or_equal:start_date',
            'contract_details' => 'nullable|string',
        ]);

        $validated['organization_id'] = auth()->user()->organization_id;
        $validated['status'] = 'open';

        AttestationTender::create($validated);

        return redirect()->route('employer.tenders.index')
            ->with('success', 'Tender va shartnoma muvaffaqiyatli yaratildi. Laboratoriya bilan bog\'liq bo\'ldi.');
    }

    public function show(AttestationTender $tender)
    {
        if ($tender->organization_id !== auth()->user()->organization_id) abort(403);
        $tender->load('laboratory');
        return view('employer.tenders.show', compact('tender'));
    }

    /**
     * Tenderni "awarded" holatiga o'tkazish — laboratoriya ishlashni boshlaydi
     */
    public function award(AttestationTender $tender)
    {
        if ($tender->organization_id !== auth()->user()->organization_id) abort(403);
        if ($tender->status !== 'open') {
            return back()->with('error', 'Bu tender allaqachon qayta holat o\'zgartira olmaydi.');
        }

        $tender->update(['status' => 'awarded']);
        return redirect()->route('employer.tenders.show', $tender)
            ->with('success', 'Tender muvaffaqiyatli tasdiqlandi. Laboratoriya endi o\'lchov natijalarini kirita oladi.');
    }

    /**
     * Tenderni "completed" deb belgilash — barcha protokollar tayyor
     */
    public function complete(AttestationTender $tender)
    {
        if ($tender->organization_id !== auth()->user()->organization_id) abort(403);

        $tender->update(['status' => 'completed']);
        return redirect()->route('employer.tenders.show', $tender)
            ->with('success', 'Tender yakunlandi. Endi davlat ekspertizasiga yuborishingiz mumkin.');
    }
}
