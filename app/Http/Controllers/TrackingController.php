<?php

namespace App\Http\Controllers;

use App\Models\Deed;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function index(Request $request)
    {
        $bank = $request->attributes->get('bank');
        $branch = $request->attributes->get('branch');
        $isMaster = $request->attributes->get('is_master_admin');

        $query = Deed::where('bank_id', $bank->id);
        if (!$isMaster && $branch) {
            $query->where('branch_id', $branch->id);
        }

        $searchQuery = $request->input('deed_no');
        $deed = null;

        if ($searchQuery) {
            $deed = (clone $query)->where(function ($q) use ($searchQuery) {
                $q->where('deed_no', 'ilike', "%{$searchQuery}%")
                  ->orWhere('bank_trans_id', 'ilike', "%{$searchQuery}%")
                  ->orWhere('borrower_name', 'ilike', "%{$searchQuery}%");
            })->first();
        }

        // If no specific deed searched, load the latest deed
        if (!$deed) {
            $deed = (clone $query)->orderBy('created_at', 'desc')->first();
        }

        // Available recent deeds for easy clicking
        $recentDeeds = (clone $query)->orderBy('created_at', 'desc')->take(6)->get();

        return view('tracking.index', compact('bank', 'branch', 'deed', 'searchQuery', 'recentDeeds'));
    }
}
