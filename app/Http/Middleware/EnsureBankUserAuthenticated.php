<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class EnsureBankUserAuthenticated
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $master = Auth::guard('bank_master')->user();
        $branchUser = Auth::guard('bank_branch')->user();

        if (!$master && !$branchUser) {
            return redirect()->route('login')->with('warning', 'Please sign in to access the Bank Service portal.');
        }

        $bank = null;
        $branch = null;
        $officer = null;
        $isMaster = false;

        if ($master) {
            $bank = $master;
            $isMaster = true;
        } elseif ($branchUser) {
            $officer = $branchUser;
            $bank = $branchUser->bank;
            $branch = $branchUser->branch;
        }

        // Share globally with all views rendered within this middleware
        View::share([
            'authBank' => $bank,
            'authBranch' => $branch,
            'authOfficer' => $officer,
            'isMasterAdmin' => $isMaster,
            'currentGuard' => $isMaster ? 'bank_master' : 'bank_branch',
        ]);

        $request->attributes->set('bank', $bank);
        $request->attributes->set('branch', $branch);
        $request->attributes->set('officer', $officer);
        $request->attributes->set('is_master_admin', $isMaster);

        return $next($request);
    }
}
