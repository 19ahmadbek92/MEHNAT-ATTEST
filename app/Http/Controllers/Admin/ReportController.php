<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttestationApplication;
use App\Models\AttestationCampaign;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $campaigns = AttestationCampaign::latest()->get();

        return view('reports.index', compact('campaigns'));
    }

    public function campaign(AttestationCampaign $campaign)
    {
        $applications = AttestationApplication::with(['user', 'campaign'])
            ->where('campaign_id', $campaign->id)
            ->latest()
            ->paginate(25);

        return view('reports.campaign', compact('campaign', 'applications'));
    }

    public function campaignCsv(AttestationCampaign $campaign)
    {
        $filename = 'campaign_' . $campaign->id . '_applications.csv';

        $applications = AttestationApplication::with(['user'])
            ->where('campaign_id', $campaign->id)
            ->orderByDesc('id')
            ->get();

        return response()->streamDownload(function () use ($applications) {
            $out = fopen('php://output', 'w');

            fputcsv($out, [
                'ID',
                'Tashkilot',
                'Email',
                'Ish o\'rni nomi',
                'Bo\'lim',
                'Status',
                'Ekspert izohi',
                'O\'rtacha ball',
                'Ish o\'rni klassi',
                'Yaratilgan',
            ]);

            foreach ($applications as $a) {
                fputcsv($out, [
                    $a->id,
                    $a->user?->name,
                    $a->user?->email,
                    $a->workplace_name ?? $a->position,
                    $a->department,
                    $a->status,
                    $a->hr_comment,
                    $a->final_score,
                    $a->getWorkplaceClassLabel(),
                    optional($a->created_at)->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}

