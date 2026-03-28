<?php

namespace App\Http\Controllers\Laboratory;

use App\Http\Controllers\Controller;
use App\Models\Workplace;
use App\Models\MeasurementResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MeasurementController extends Controller
{
    public function index()
    {
        // Laboratoriya tizimdagi kutayotgan ish o'rinlarini ko'radi
        // Aslida tender orqali ulangan tashkilotlar ish o'rinlari bo'lishi kerak.
        // Hozir soddalashtirilgan variantda barchasini ko'rsatamiz.
        $workplaces = Workplace::with('organization')->latest()->paginate(15);
        return view('laboratory.workplaces.index', compact('workplaces'));
    }

    public function create(Workplace $workplace)
    {
        // 18 ta faktor SanQvaM
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

        foreach ($data['measurements'] as $measurement) {
            if (!empty($measurement['danger_class']) && $measurement['danger_class'] !== 'Optimal') {
                $workplace->measurements()->create([
                    'laboratory_id' => $laboratory->id,
                    'factor_name' => $measurement['factor_name'],
                    'measured_value' => $measurement['measured_value'],
                    'norm_value' => $measurement['norm_value'],
                    'danger_class' => $measurement['danger_class'],
                    'measured_at' => now(),
                    'protocol_number' => 'PRT-' . rand(1000, 9999)
                ]);
            }
        }

        // Agar barcha o'lchovlar kiritilgan bo'lsa
        $workplace->update(['status' => 'attested']);

        return redirect()->route('laboratory.workplaces.index')->with('success', 'O\'lchov natijalari muvaffaqiyatli saqlandi.');
    }
}
