<?php

namespace App\Http\Controllers;

use App\Models\BankMasterUser;
use App\Models\Branch;
use App\Models\BranchUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        // If already logged in, redirect to dashboard
        if (Auth::guard('bank_master')->check() || Auth::guard('bank_branch')->check()) {
            return redirect()->route('dashboard');
        }

        $branches = Branch::where('status', 'Active')->with('bank')->get();
        $banks = BankMasterUser::where('status', 'Active')->get();

        return view('auth.login', compact('branches', 'banks'));
    }

    public function loginMaster(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('bank_master')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'))
                ->with('success', 'Logged in successfully as Bank Master Administrator.');
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'Invalid email or password for Master Admin.']);
    }

    public function loginBranch(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('bank_branch')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'))
                ->with('success', 'Logged in successfully as Authorized Officer.');
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'Invalid email or password for Authorized Officer.']);
    }

    public function logout(Request $request)
    {
        if (Auth::guard('bank_master')->check()) {
            Auth::guard('bank_master')->logout();
        }

        if (Auth::guard('bank_branch')->check()) {
            Auth::guard('bank_branch')->logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('info', 'You have been logged out.');
    }
}
