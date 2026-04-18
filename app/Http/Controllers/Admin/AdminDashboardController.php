<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttestationApplication;
use App\Models\AttestationCampaign;
use App\Models\User;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $adminCount = User::where('role', 'admin')->count();
        $expertCount = User::where('role', 'expert')->count();
        $commissionCount = User::where('role', 'commission')->count();
        $employerCount = User::where('role', 'employer')->count();
        $totalApplications = AttestationApplication::count();
        $activeCampaigns = AttestationCampaign::where('status', 'open')->count();

        return view('admin.index', compact(
            'totalUsers',
            'adminCount',
            'expertCount',
            'commissionCount',
            'employerCount',
            'totalApplications',
            'activeCampaigns'
        ));
    }
}
