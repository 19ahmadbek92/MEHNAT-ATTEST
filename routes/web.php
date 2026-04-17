<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\EriController;
use App\Http\Controllers\Auth\OneIDController;
use App\Http\Controllers\Auth\SelectTypeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AttestationCampaignController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Employee\ApplicationController;
use App\Http\Controllers\Commission\EvaluationController;
use App\Http\Controllers\HR\ApplicationReviewController;
use App\Http\Controllers\Employee\AiProcessController;

Route::get('/', HomeController::class)->name('home');
Route::get('/healthz', HealthController::class)->name('healthz');

Route::get('lang/{lang}', [\App\Http\Controllers\LanguageController::class, 'switchLang'])->name('lang.switch');

/*
|--------------------------------------------------------------------------
| Faqat local muhit: migratsiya va avto-login (internetda ISHLATMASLIK kerak)
|--------------------------------------------------------------------------
*/
if (app()->environment('local')) {
    Route::get('/run-migrations-auto', function () {
        try {
            \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);

            return 'Migrated successfully! <a href="/auto-login-admin">Go to Dashboard</a>';
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    });

    Route::get('/auto-login-admin', function () {
        $user = \App\Models\User::whereIn('role', ['admin', 'employer'])->first();
        if ($user) {
            \Illuminate\Support\Facades\Auth::login($user);

            return redirect()->route('dashboard');
        }

        return 'Test User not found.';
    });
}

/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| USER DASHBOARD
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // Admin routes
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/admin', [AdminDashboardController::class, 'index'])
            ->name('admin.dashboard');

        Route::resource('admin/campaigns', AttestationCampaignController::class)
            ->names('admin.campaigns');
    });
    // Davlat ekspertizasi routes (Vazirlik xulosasi)
    Route::middleware(['role:expert'])->group(function () {
        Route::get('ministry/expertise', [\App\Http\Controllers\Ministry\ExpertiseController::class, 'index'])->name('ministry.expertise.index');
        Route::get('ministry/expertise/{expertise}', [\App\Http\Controllers\Ministry\ExpertiseController::class, 'show'])->name('ministry.expertise.show');
        Route::post('ministry/expertise/{expertise}/process', [\App\Http\Controllers\Ministry\ExpertiseController::class, 'process'])->name('ministry.expertise.process');
    });

    // Reports (Admin + Expert)
    Route::middleware(['role:admin,expert'])->group(function () {
        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/campaigns/{campaign}', [ReportController::class, 'campaign'])->name('reports.campaign');
        Route::get('reports/campaigns/{campaign}/csv', [ReportController::class, 'campaignCsv'])->name('reports.campaign.csv');
    });

    // Ish beruvchi (employer) routes (oldin employee edi)
    Route::middleware(['role:employer'])->group(function () {
        // Employer's Organization
        Route::resource('employer/organization', \App\Http\Controllers\Employer\OrganizationController::class)->only(['index', 'store', 'update'])->names('employer.organization');
        
        // Employer's Workplaces
        Route::resource('employer/workplaces', \App\Http\Controllers\Employer\WorkplaceController::class)->names('employer.workplaces');
        Route::get('employer/workplaces/{workplace}/print', [\App\Http\Controllers\Employer\WorkplaceController::class, 'print'])->name('employer.workplaces.print');

        Route::resource('employer/tenders', \App\Http\Controllers\Employer\AttestationTenderController::class)->names('employer.tenders');
        // Tender lifecycle actions
        Route::post('employer/tenders/{tender}/award', [\App\Http\Controllers\Employer\AttestationTenderController::class, 'award'])->name('employer.tenders.award');
        Route::post('employer/tenders/{tender}/complete', [\App\Http\Controllers\Employer\AttestationTenderController::class, 'complete'])->name('employer.tenders.complete');

        Route::resource('employer/expertise', \App\Http\Controllers\Employer\StateExpertiseController::class)->only(['index', 'store'])->names('employer.expertise');

        // Application routes
        Route::get('applications', [ApplicationController::class, 'index'])->name('employee.applications.index');
        Route::get('applications/create', [ApplicationController::class, 'create'])->name('employee.applications.create');
        Route::post('applications', [ApplicationController::class, 'store'])->name('employee.applications.store');
        Route::get('applications/{application}', [ApplicationController::class, 'show'])->name('employee.applications.show');
        Route::post('applications/{application}/ai-process', AiProcessController::class)->name('employee.applications.ai_process');
    });

    // Laboratory (Attestatsiya o'tkazuvchi tashkilot) routes
    Route::middleware(['role:laboratory'])->group(function () {
        Route::get('laboratory/profile', [\App\Http\Controllers\Laboratory\LaboratoryProfileController::class, 'index'])->name('laboratory.profile.index');
        Route::post('laboratory/profile', [\App\Http\Controllers\Laboratory\LaboratoryProfileController::class, 'store'])->name('laboratory.profile.store');
        Route::put('laboratory/profile/{laboratory}', [\App\Http\Controllers\Laboratory\LaboratoryProfileController::class, 'update'])->name('laboratory.profile.update');

        Route::get('laboratory/protocols', [\App\Http\Controllers\Laboratory\ProtocolController::class, 'index'])->name('laboratory.protocols.index');
        Route::get('laboratory/protocols/{application}/create', [\App\Http\Controllers\Laboratory\ProtocolController::class, 'create'])->name('laboratory.protocols.create');
        Route::post('laboratory/protocols/{application}', [\App\Http\Controllers\Laboratory\ProtocolController::class, 'store'])->name('laboratory.protocols.store');

        Route::get('laboratory/workplaces', [\App\Http\Controllers\Laboratory\MeasurementController::class, 'index'])->name('laboratory.workplaces.index');
        Route::get('laboratory/workplaces/{workplace}/measure', [\App\Http\Controllers\Laboratory\MeasurementController::class, 'create'])->name('laboratory.measurements.create');
        Route::post('laboratory/workplaces/{workplace}/measure', [\App\Http\Controllers\Laboratory\MeasurementController::class, 'store'])->name('laboratory.measurements.store');
    });

    // Commission routes
    Route::middleware(['role:commission'])->group(function () {
        Route::get('commission/evaluations', [EvaluationController::class, 'index'])
            ->name('commission.evaluations.index');
        Route::get('commission/evaluations/{application}', [EvaluationController::class, 'evaluateForm'])
            ->name('commission.evaluations.form');
        Route::post('commission/evaluations/{application}', [EvaluationController::class, 'storeOrUpdate'])
            ->name('commission.evaluations.store');
    });

    // HR (arizalarni ko'rib chiqish, tasdiqlash)
    Route::middleware(['role:hr'])->group(function () {
        Route::get('hr/applications', [ApplicationReviewController::class, 'index'])
            ->name('hr.applications.index');
        Route::get('hr/applications/{application}', [ApplicationReviewController::class, 'show'])
            ->name('hr.applications.show');
        Route::post('hr/applications/{application}/approve', [ApplicationReviewController::class, 'approve'])
            ->name('hr.applications.approve');
        Route::post('hr/applications/{application}/reject', [ApplicationReviewController::class, 'reject'])
            ->name('hr.applications.reject');
        Route::post('hr/applications/{application}/finalize', [ApplicationReviewController::class, 'finalize'])
            ->name('hr.applications.finalize');
    });

    // Institute Expert routes (Dastlabki baholash)
    Route::middleware(['role:institute_expert'])->group(function () {
        Route::get('institute/expertise', [\App\Http\Controllers\Institute\ExpertiseController::class, 'index'])->name('institute.expertise.index');
        Route::get('institute/expertise/{expertise}', [\App\Http\Controllers\Institute\ExpertiseController::class, 'show'])->name('institute.expertise.show');
        Route::post('institute/expertise/{expertise}/process', [\App\Http\Controllers\Institute\ExpertiseController::class, 'process'])->name('institute.expertise.process');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

// Kirish turini tanlash sahifasi
Route::get('/login/select-type', SelectTypeController::class)->name('auth.select-type');

Route::middleware(['guest', 'throttle:demo-auth', 'demo.sso'])->group(function () {
    Route::get('/auth/oneid', [OneIDController::class, 'redirect'])
        ->name('auth.oneid.redirect');

    Route::get('/auth/oneid/callback', [OneIDController::class, 'callback'])
        ->name('auth.oneid.callback');

    Route::get('/auth/eri', [EriController::class, 'login'])
        ->name('auth.eri.login');
});
