<?php

namespace App\Http\Controllers\Laboratory;

use App\Http\Controllers\Controller;
use App\Models\Workplace;
use App\Models\MeasurementResult;
use App\Models\AttestationTender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MeasurementController extends Controller
{
    public function index()
    {
        $laboratory = Auth::user()->laboratory;
        if (!$laboratory) {
            return redirect()->route('laboratory.profile.index')
                ->with('error', 'Iltimos, avvalo laboratoriya profilingizni to\'ldiring.');
        }

        // Tender orqali ulangan tashkilotlarning ish o'rinlarini ko'rsatish
        $organizationIds = AttestationTender::where('laboratory_id', $laboratory->id)
            ->whereIn('status', ['awarded', 'completed'])
            ->pluck('organization_id');

        $workplaces = Workplace::with('organization')
            ->whereIn('organization_id', $organizationIds)
            ->latest()
            ->paginate(15);

        return view('laboratory.workplaces.index', compact('workplaces'));
    }

    public function create(Workplace $workplace)
    {
        $laboratory = Auth::user()->laboratory;
        if (!$laboratory) {
            return redirect()->route('laboratory.profile.index')
                ->with('error', 'Sizda laboratoriya profili mavjud emas.');
        }

        // Tender orqali munosabatni tekshirish
        $hasTender = AttestationTender::where('laboratory_id', $laboratory->id)
            ->where('organization_id', $workplace->organization_id)
            ->whereIn('status', ['awarded', 'completed'])
            ->exists();

        if (!$hasTender) {
            abort(403, 'Bu ish o\'rni sizning laboratoriyangizga tegishli emas.');
        }

        // 18 ta faktor SanQvaM 0069-24
        $factors = [
            'Mikroiqlim (Harorat, namlik, havo harakati)',
            'Shovqin',
            'Vibratsiya (umumiy va lokal)',
            'Yoritilganlik',
            'Elektromagnit maydonlar',
            'Lazer nurlanishi',
            'Ultrabinafsha nurlanish',
            'Zaharli moddalar (Kimyoviy omil)',
            'Sanoat changi (Fibrogen ta\'sirli aerozollar)',
            'Biologik omil',
            'Lokal mushak zorriqishi',
            'Og\'ir yuk ko\'tarish (Jismoniy og\'irlik)',
            'Ish holati va harakatlanish',
            'Aqliy zo\'riqish',
            'Emotsional zo\'riqish',
            'Ish rejimi (Smena va dam olish)',
            'Monotonlik',
            'Ionlashtiruvchi nurlanish'
        ];

        return view('laboratory.measurements.create', compact('workplace', 'factors'));
    }

    public function store(Request $request, Workplace $workplace)
    {
        $data = $request->validate([
            'measurements' => 'required|array',
            'measurements.*.factor_name' => 'required|string',
            'measurements.*.measured_value' => 'nullable|string',
            'measurements.*.norm_value' => 'nullable|string',
            'measurements.*.danger_class' => 'required|string',
        ]);

        $laboratory = Auth::user()->laboratory;
        if (!$laboratory) {
            return back()->with('error', 'Sizda laboratoriya profili mavjud emas.');
        }

        // Tender orqali munosabatni tekshirish
        $hasTender = AttestationTender::where('laboratory_id', $laboratory->id)
            ->where('organization_id', $workplace->organization_id)
            ->whereIn('status', ['awarded', 'completed'])
            ->exists();

        if (!$hasTender) {
            return back()->with('error', 'Bu ish o\'rni uchun sizda shartnoma mavjud emas.');
        }

        $savedCount = 0;
        foreach ($data['measurements'] as $measurement) {
            $workplace->measurements()->create([
                'laboratory_id' => $laboratory->id,
                'factor_name' => $measurement['factor_name'],
                'measured_value' => $measurement['measured_value'],
                'norm_value' => $measurement['norm_value'],
                'danger_class' => $measurement['danger_class'],
                'measured_at' => now(),
                'protocol_number' => 'PRT-' . now()->format('ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
            ]);
            $savedCount++;
        }

        // Agar barcha o'lchovlar kiritilgan bo'lsa
        $workplace->update(['status' => 'attested']);

        return redirect()->route('laboratory.workplaces.index')
            ->with('success', "O'lchov natijalari muvaffaqiyatli saqlandi ({$savedCount} ta faktor).");
    }
}
