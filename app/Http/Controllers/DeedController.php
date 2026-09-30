<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\BranchUser;
use App\Models\Deed;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DeedController extends Controller
{
    public function index(Request $request)
    {
        $bank = $request->attributes->get('bank');
        $branch = $request->attributes->get('branch');
        $isMaster = $request->attributes->get('is_master_admin');

        $query = Deed::where('bank_id', $bank->id)->with(['branch', 'creator']);

        if (!$isMaster && $branch) {
            $query->where('branch_id', $branch->id);
        } elseif ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('deed_no', 'ilike', "%{$s}%")
                  ->orWhere('borrower_name', 'ilike', "%{$s}%")
                  ->orWhere('borrower_pan', 'ilike', "%{$s}%")
                  ->orWhere('bank_trans_id', 'ilike', "%{$s}%");
            });
        }

        $deeds = $query->orderBy('created_at', 'desc')->paginate(10);
        $branches = Branch::where('bank_id', $bank->id)->where('status', 'Active')->get();

        return view('deeds.index', compact('bank', 'deeds', 'branches', 'isMaster', 'branch'));
    }

    public function store(Request $request)
    {
        $bank = $request->attributes->get('bank');
        $authOfficer = $request->attributes->get('officer');
        $isMaster = $request->attributes->get('is_master_admin');

        // STRICT POLICY: Master branch cannot initiate deeds! Only branch authorized officers can!
        if ($isMaster || !$authOfficer) {
            abort(403, 'Initiate New Deed can only be performed by Branch Authorized Officers. Master branch is restricted to managing branches and users.');
        }

        $branch = $authOfficer->branch;
        if (!$branch) {
            return back()->with('error', 'Authorized Officer does not have an active branch assignment.');
        }

        $validated = $request->validate([
            'borrower_name' => 'required|string|max:255',
            'borrower_mobile' => 'nullable|string|max:20',
            'borrower_pan' => 'nullable|string|max:20',
            'property_details' => 'required|string',
            'loan_amount' => 'required|numeric|min:0',
            'stamp_duty' => 'nullable|numeric|min:0',
        ]);

        // Generate sequential deed number for this branch
        $deedNo = Deed::generateDeedNo($bank, $branch);
        $stampDuty = $validated['stamp_duty'] ?? round($validated['loan_amount'] * 0.02, 2);

        $deed = Deed::create([
            'deed_no' => $deedNo,
            'bank_id' => $bank->id,
            'branch_id' => $branch->id,
            'created_by_user_id' => $authOfficer->id,
            'borrower_name' => $validated['borrower_name'],
            'borrower_mobile' => $validated['borrower_mobile'] ?? null,
            'borrower_pan' => $validated['borrower_pan'] ? strtoupper($validated['borrower_pan']) : null,
            'property_details' => $validated['property_details'],
            'loan_amount' => $validated['loan_amount'],
            'stamp_duty' => $stampDuty,
            'status' => 'New',
            'bank_trans_id' => (string) Str::uuid(),
        ]);

        return redirect()->route('deeds.show', $deed->id)
            ->with('success', "Mortgage Deed Application {$deedNo} created successfully by {$authOfficer->name}.");
    }

    public function show(Deed $deed, Request $request)
    {
        $bank = $request->attributes->get('bank');
        if ($deed->bank_id !== $bank->id) {
            abort(403);
        }

        $deed->load(['branch', 'creator', 'auditLogs' => function ($q) {
            $q->orderBy('created_at', 'desc');
        }]);

        return view('deeds.show', compact('deed', 'bank'));
    }
}
