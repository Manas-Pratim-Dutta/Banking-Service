<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\BranchUser;
use App\Models\Deed;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $bank = $request->attributes->get('bank');
        $branch = $request->attributes->get('branch');
        $officer = $request->attributes->get('officer');
        $isMaster = $request->attributes->get('is_master_admin');

        $branchesQuery = Branch::where('bank_id', $bank->id);
        $officersQuery = BranchUser::where('bank_id', $bank->id);
        $deedsQuery = Deed::where('bank_id', $bank->id);

        if (!$isMaster && $branch) {
            $deedsQuery->where('branch_id', $branch->id);
        }

        $totalBranches = $branchesQuery->count();
        $activeOfficersCount = (clone $officersQuery)->where('status', 'Active')->count();

        $currentMonthStart = now()->startOfMonth();
        $deedsThisMonth = (clone $deedsQuery)->where('created_at', '>=', $currentMonthStart)->count();
        $deedsTotalCount = (clone $deedsQuery)->count();

        // Calculate stamp duty
        $totalStampDuty = (clone $deedsQuery)->sum('stamp_duty');
        $stampDutyFormatted = $this->formatIndianCurrency($totalStampDuty);

        // Deeds by status
        $statusCounts = [
            'New' => (clone $deedsQuery)->where('status', 'New')->count(),
            'In progress' => (clone $deedsQuery)->where('status', 'In progress')->count(),
            'SRO review' => (clone $deedsQuery)->where('status', 'SRO review')->count(),
            'Completed' => (clone $deedsQuery)->where('status', 'Completed')->count(),
        ];

        // Top branches by volume
        $topBranches = Branch::where('bank_id', $bank->id)
            ->withCount('deeds')
            ->orderBy('deeds_count', 'desc')
            ->take(5)
            ->get();

        $maxBranchDeeds = $topBranches->max('deeds_count') ?: 1;

        // Recent branch users / authorized officers
        $recentOfficers = BranchUser::where('bank_id', $bank->id)
            ->with('branch')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Recent deeds for branch dashboard
        $recentDeeds = (clone $deedsQuery)
            ->with(['branch', 'creator'])
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        return view('dashboard.index', compact(
            'bank',
            'branch',
            'officer',
            'isMaster',
            'totalBranches',
            'activeOfficersCount',
            'deedsThisMonth',
            'deedsTotalCount',
            'stampDutyFormatted',
            'statusCounts',
            'topBranches',
            'maxBranchDeeds',
            'recentOfficers',
            'recentDeeds'
        ));
    }

    private function formatIndianCurrency($number): string
    {
        if ($number >= 10000000) {
            return '₹' . number_format($number / 10000000, 2) . 'Cr';
        } elseif ($number >= 100000) {
            return '₹' . number_format($number / 100000, 1) . 'L';
        } elseif ($number >= 1000) {
            return '₹' . number_format($number / 1000, 1) . 'K';
        }
        return '₹' . number_format($number, 2);
    }
}
