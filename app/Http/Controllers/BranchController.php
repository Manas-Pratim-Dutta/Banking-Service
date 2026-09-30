<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index(Request $request)
    {
        $bank = $request->attributes->get('bank');

        $query = Branch::where('bank_id', $bank->id)
            ->withCount(['branchUsers', 'deeds']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('branch_name', 'ilike', "%{$s}%")
                  ->orWhere('branch_code', 'ilike', "%{$s}%")
                  ->orWhere('ifsc_code', 'ilike', "%{$s}%")
                  ->orWhere('district', 'ilike', "%{$s}%");
            });
        }

        if ($request->filled('district')) {
            $query->where('district', $request->district);
        }

        $branches = $query->orderBy('branch_name')->paginate(10);
        $districts = Branch::where('bank_id', $bank->id)->distinct()->pluck('district');

        return view('branches.index', compact('bank', 'branches', 'districts'));
    }

    public function store(Request $request)
    {
        $bank = $request->attributes->get('bank');

        $validated = $request->validate([
            'branch_name' => 'required|string|max:255',
            'branch_code' => 'required|string|max:50',
            'ifsc_code' => 'required|string|max:50',
            'district' => 'required|string|max:100',
            'address' => 'nullable|string',
            'contact_phone' => 'nullable|string|max:50',
            'contact_email' => 'nullable|email|max:255',
        ]);

        // Check uniqueness within the bank
        $exists = Branch::where('bank_id', $bank->id)
            ->where('branch_code', strtoupper($validated['branch_code']))
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors(['branch_code' => 'Branch code already exists for this bank.']);
        }

        Branch::create([
            'bank_id' => $bank->id,
            'branch_code' => strtoupper($validated['branch_code']),
            'branch_name' => $validated['branch_name'],
            'ifsc_code' => strtoupper($validated['ifsc_code']),
            'district' => $validated['district'],
            'address' => $validated['address'] ?? null,
            'contact_phone' => $validated['contact_phone'] ?? null,
            'contact_email' => $validated['contact_email'] ?? null,
            'status' => 'Active',
        ]);

        return redirect()->route('branches.index')->with('success', 'Branch created successfully.');
    }

    public function toggleStatus(Branch $branch, Request $request)
    {
        $bank = $request->attributes->get('bank');
        if ($branch->bank_id !== $bank->id) {
            abort(403);
        }

        $branch->status = $branch->status === 'Active' ? 'Inactive' : 'Active';
        $branch->save();

        return back()->with('success', "Branch status updated to {$branch->status}.");
    }
}
