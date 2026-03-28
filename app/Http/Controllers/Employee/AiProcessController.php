<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\AttestationApplication;
use App\Services\AiAttestationService;
use Illuminate\Http\Request;

class AiProcessController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, AttestationApplication $application, AiAttestationService $aiService)
    {
        // Check authorization and status
        if ($application->user_id !== $request->user()->id || $application->status !== 'submitted') {
            abort(403, 'Bu arizani AI orqali tasdiqlab bo\'lmaydi.');
        }

        try {
            // Process automatically via AI Simulator
            $aiService->autoProcess($application);

            return back()->with('status', '✅ Sun\'iy intellekt arizangizni avtomatik tahlil qildi va yakuniy qarorni qabul qildi.');
        } catch (\Exception $e) {
            return back()->with('error', 'AI tizimida xatolik yuz berdi: ' . $e->getMessage());
        }
    }
}
