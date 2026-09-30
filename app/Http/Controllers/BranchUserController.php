<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\BranchUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class BranchUserController extends Controller
{
    public function index(Request $request)
    {
        $bank = $request->attributes->get('bank');
        $currentBranch = $request->attributes->get('branch');
        $isMaster = $request->attributes->get('is_master_admin');

        $query = BranchUser::where('bank_id', $bank->id)
            ->with(['branch', 'deeds']);

        if (!$isMaster && $currentBranch) {
            $query->where('branch_id', $currentBranch->id);
        } elseif ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'ilike', "%{$s}%")
                  ->orWhere('officer_id', 'ilike', "%{$s}%")
                  ->orWhere('email', 'ilike', "%{$s}%")
                  ->orWhere('mobile_number', 'ilike', "%{$s}%")
                  ->orWhere('pan_number', 'ilike', "%{$s}%");
            });
        }

        $officers = $query->orderBy('name')->paginate(10);
        $branches = Branch::where('bank_id', $bank->id)->where('status', 'Active')->orderBy('branch_name')->get();
        $districts = $branches->pluck('district')->unique();

        return view('branch_users.index', compact('bank', 'officers', 'branches', 'districts', 'isMaster'));
    }

    public function store(Request $request)
    {
        $bank = $request->attributes->get('bank');

        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'name' => 'required|string|max:255',
            'mobile_number' => 'required|string|max:20',
            'email' => 'required|email|max:255|unique:branch_users,email',
            'password' => 'required|string|min:6',
            'pan_number' => 'required|string|max:20',
            'tan_number' => 'nullable|string|max:20',
            'age' => 'required|integer|min:18|max:80',
            'address' => 'required|string',
        ]);

        $branch = Branch::where('bank_id', $bank->id)->findOrFail($validated['branch_id']);

        // Generate a clean officer_id (e.g., AGB-GHY-04)
        $districtCode = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $branch->district), 0, 3));
        $count = BranchUser::where('branch_id', $branch->id)->count() + 1;
        $officerId = sprintf("%s-%s-%02d", $bank->bank_code, $districtCode, $count);

        BranchUser::create([
            'bank_id' => $bank->id,
            'branch_id' => $branch->id,
            'officer_id' => $officerId,
            'name' => $validated['name'],
            'email' => strtolower($validated['email']),
            'mobile_number' => $validated['mobile_number'],
            'password' => Hash::make($validated['password']),
            'designation' => 'Authorized Officer',
            'pan_number' => strtoupper(trim($validated['pan_number'])),
            'tan_number' => $validated['tan_number'] ? strtoupper(trim($validated['tan_number'])) : null,
            'age' => (int) $validated['age'],
            'address' => $validated['address'],
            'status' => 'Active',
        ]);

        return redirect()->route('branch-users.index')
            ->with('success', "Authorized Officer '{$validated['name']}' registered successfully with ID: {$officerId}.");
    }

    public function toggleStatus(BranchUser $branchUser, Request $request)
    {
        $bank = $request->attributes->get('bank');
        if ($branchUser->bank_id !== $bank->id) {
            abort(403);
        }

        $branchUser->status = $branchUser->status === 'Active' ? 'Inactive' : 'Active';
        $branchUser->save();

        return back()->with('success', "Authorized Officer status updated to {$branchUser->status}.");
    }
}
