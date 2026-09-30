<?php

namespace App\Http\Controllers;

use App\Models\BankMasterUser;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index(Request $request)
    {
        $bank = $request->attributes->get('bank');
        $branch = $request->attributes->get('branch');
        $isMaster = $request->attributes->get('is_master_admin');

        return view('settings.index', compact('bank', 'branch', 'isMaster'));
    }

    public function update(Request $request)
    {
        $bank = $request->attributes->get('bank');

        $validated = $request->validate([
            'bank_name' => 'required|string|max:255',
            'head_office_address' => 'nullable|string',
            'notification_email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
        ]);

        $bank->update($validated);

        return back()->with('success', 'Bank settings updated successfully.');
    }
}
