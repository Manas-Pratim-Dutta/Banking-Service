@extends('layouts.app')

@section('title', 'Settings - NGDRS Bank Portal')
@section('page_title', 'Settings')

@section('content')
<div class="max-w-2xl">

    <!-- Card matching screenshot 1 -->
    <div class="bg-white rounded-2xl p-8 border border-slate-100 shadow-sm shadow-slate-100/60">
        <h3 class="text-base font-bold text-slate-900 mb-6">Settings</h3>

        <form method="POST" action="{{ route('settings.update') }}" class="space-y-5 text-xs">
            @csrf

            <!-- Bank name -->
            <div>
                <label class="block font-medium text-slate-600 mb-1.5">Bank name</label>
                <input type="text" 
                       name="bank_name" 
                       value="{{ old('bank_name', $bank->bank_name) }}" 
                       required 
                       placeholder="e.g. Assam Gramin Bank" 
                       class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 text-xs">
            </div>

            <!-- Branch -->
            <div>
                <label class="block font-medium text-slate-600 mb-1.5">Branch</label>
                <input type="text" 
                       readonly 
                       value="{{ $branch ? $branch->branch_name : 'Head Office - Guwahati' }}" 
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-slate-700 cursor-not-allowed text-xs">
            </div>

            <!-- Notification email -->
            <div>
                <label class="block font-medium text-slate-600 mb-1.5">Notification email</label>
                <input type="email" 
                       name="notification_email" 
                       value="{{ old('notification_email', $bank->notification_email ?? 'branch.guwahati@agb.co.in') }}" 
                       required 
                       placeholder="e.g. branch.guwahati@agb.co.in" 
                       class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 text-xs">
            </div>

            <!-- Head Office Address -->
            <div>
                <label class="block font-medium text-slate-600 mb-1.5">Head Office Address</label>
                <input type="text" 
                       name="head_office_address" 
                       value="{{ old('head_office_address', $bank->head_office_address) }}" 
                       placeholder="Street, town, district" 
                       class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 text-xs">
            </div>

            <!-- NGDRS SSO Config Readout -->
            <div class="pt-4 border-t border-slate-100">
                <h4 class="font-bold text-slate-900 mb-2">NGDRS SSO Configuration</h4>
                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 space-y-1 font-mono text-[11px] text-slate-600">
                    <div><span class="text-slate-400">Endpoint:</span> {{ config('services.ngdrs.sso_endpoint') }}</div>
                    <div><span class="text-slate-400">Cipher:</span> AES-256-CBC</div>
                    <div><span class="text-slate-400">SSO Key:</span> ••••••••••••••••</div>
                    <div><span class="text-slate-400">SSO IV:</span> {{ config('services.ngdrs.sso_iv') }}</div>
                </div>
            </div>

            <!-- Save Changes Orange Button matching screenshot 1 -->
            <div class="pt-3">
                <button type="submit" 
                        class="w-full sm:w-auto px-8 py-3 rounded-xl text-xs font-semibold text-white bg-[#f95721] hover:bg-[#ea580c] transition shadow-md shadow-orange-500/20">
                    Save changes
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
