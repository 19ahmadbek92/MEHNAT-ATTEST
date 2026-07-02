<?php

namespace App\Http\Controllers\Laboratory;

use App\Http\Controllers\Controller;
use App\Models\AttestationTender;
use App\Models\Employee;
use App\Models\ErgonomicAssessment;
use App\Models\Workplace;
use App\Services\ErgonomicAssessmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ErgonomicAssessmentController extends Controller
{
    public function __construct(private readonly ErgonomicAssessmentService $service) {}

    public function index(Workplace $workplace)
    {
        $this->authorizeWorkplace($workplace);

        $assessments = $workplace->ergonomicAssessments()->with('employee', 'assessor')->latest('assessed_at')->get();

        return view('laboratory.ergonomic.index', compact('workplace', 'assessments'));
    }

    public function create(Workplace $workplace)
    {
        $this->authorizeWorkplace($workplace);

        $employees = $workplace->employees()->where('is_active', true)->get();

        return view('laboratory.ergonomic.create', compact('workplace', 'employees'));
    }

    public function store(Request $request, Workplace $workplace)
    {
        $laboratory = $this->authorizeWorkplace($workplace);

        $data = $request->validate([
            'employee_id' => ['nullable', 'exists:employees,id'],
            'operation_type' => ['required', 'in:qolda,yarim_mexanizatsiya,mexanizatsiya'],
            'load_description' => ['nullable', 'string', 'max:255'],
            'load_mass_kg' => ['required', 'numeric', 'min:0.1', 'max:200'],
            'lifts_per_shift' => ['required', 'integer', 'min:0'],
            'lifts_per_minute' => ['required', 'numeric', 'min:0', 'max:30'],
            'shift_duration_hours' => ['required', 'numeric', 'min:1', 'max:24'],
            'worker_gender' => ['required', 'in:erkak,ayol'],

            'horizontal_distance_cm' => ['required', 'numeric', 'min:0', 'max:100'],
            'vertical_location_cm' => ['required', 'numeric', 'min:0', 'max:200'],
            'vertical_travel_cm' => ['required', 'numeric', 'min:0', 'max:250'],
            'asymmetry_angle_deg' => ['nullable', 'numeric', 'min:0', 'max:180'],
            'coupling_quality' => ['required', 'in:good,fair,poor'],
            'duration_category' => ['required', 'in:short,moderate,long'],

            'static_load_kgs' => ['nullable', 'integer', 'min:0'],
            'posture_type' => ['required', 'in:erkin,epizodik_noqulay,davriy_noqulay,majburiy,qattiq_majburiy'],
            'body_inclinations_per_shift' => ['required', 'integer', 'min:0'],
            'walking_distance_km' => ['required', 'numeric', 'min:0', 'max:30'],

            'attention_concentration_percent' => ['required', 'integer', 'min:0', 'max:100'],
            'signals_per_hour' => ['required', 'integer', 'min:0'],
            'responsibility_level' => ['required', 'in:ozi_uchun,jamoa_uchun,xavfsizlik_uchun'],
            'monotony_operations_count' => ['required', 'integer', 'min:0'],
            'night_shift' => ['nullable', 'boolean'],

            'temperature_c' => ['nullable', 'numeric', 'min:-40', 'max:80'],
            'metal_dust_mg_m3' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'noise_level_db' => ['nullable', 'numeric', 'min:0', 'max:150'],
            'uses_ppe' => ['nullable', 'boolean'],

            'employee_age' => ['nullable', 'integer', 'min:14', 'max:80'],
            'experience_years' => ['nullable', 'numeric', 'min:0', 'max:60'],
            'training_completed' => ['nullable', 'boolean'],
            'last_training_at' => ['nullable', 'date'],
            'fatigue_self_score' => ['required', 'integer', 'min:1', 'max:5'],
            'health_group' => ['required', 'in:soglom,cheklangan,nogironligi_bor'],
            'prior_incidents_count' => ['required', 'integer', 'min:0'],

            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        if (! empty($data['employee_id'])) {
            $employee = Employee::findOrFail($data['employee_id']);
            if ($employee->workplace_id !== $workplace->id) {
                abort(403, 'Bu xodim ushbu ish o\'rniga tegishli emas.');
            }
        }

        $data['night_shift'] = $request->boolean('night_shift');
        $data['uses_ppe'] = $request->boolean('uses_ppe');
        $data['training_completed'] = $request->boolean('training_completed');

        $result = $this->service->evaluate($data);

        $assessment = $workplace->ergonomicAssessments()->create(array_merge($data, $result, [
            'laboratory_id' => $laboratory->id,
            'assessed_by' => Auth::id(),
            'assessed_at' => now(),
        ]));

        return redirect()->route('laboratory.ergonomic.show', [$workplace, $assessment])
            ->with('success', 'Ergonomik baholash muvaffaqiyatli yakunlandi.');
    }

    public function show(Workplace $workplace, ErgonomicAssessment $ergonomic)
    {
        $this->authorizeWorkplace($workplace);

        abort_if($ergonomic->workplace_id !== $workplace->id, 404);

        return view('laboratory.ergonomic.show', ['workplace' => $workplace, 'assessment' => $ergonomic]);
    }

    private function authorizeWorkplace(Workplace $workplace): \App\Models\Laboratory
    {
        $laboratory = Auth::user()->laboratory;

        abort_if(! $laboratory, 403, 'Sizda laboratoriya profili mavjud emas.');

        $hasTender = AttestationTender::where('laboratory_id', $laboratory->id)
            ->where('organization_id', $workplace->organization_id)
            ->whereIn('status', ['awarded', 'completed'])
            ->exists();

        abort_if(! $hasTender, 403, 'Bu ish o\'rni sizning laboratoriyangizga tegishli emas.');

        return $laboratory;
    }
}
