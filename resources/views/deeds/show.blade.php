@extends('layouts.app')

@section('title', "Deed {$deed->deed_no} - NGDRS Bank Portal")
@section('page_title', 'Deed Details & NGDRS Handshake')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">

    <!-- Top Action Breadcrumb -->
    <div class="flex items-center justify-between">
        <a href="{{ route('deeds.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-900 flex items-center">
            ← Back to Deeds Directory
        </a>

        <div class="flex items-center space-x-2">
            <span class="text-xs font-medium text-slate-400">Application Status:</span>
            @php
                $badgeStyles = [
                    'New' => 'bg-amber-50 text-amber-700 border-amber-200',
                    'In progress' => 'bg-orange-50 text-orange-700 border-orange-200',
                    'SRO review' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                    'Completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                ];
            @endphp
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border {{ $badgeStyles[$deed->status] ?? 'bg-slate-100 text-slate-700' }}">
                {{ $deed->status }}
            </span>
        </div>
    </div>

    <!-- Deed Master Header Card -->
    <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm shadow-slate-100/60">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 pb-6 border-b border-slate-100">
            <div>
                <span class="text-xs font-mono font-bold tracking-wider text-orange-600 uppercase">Mortgage Deed Application</span>
                <h2 class="text-2xl font-black text-slate-900 tracking-tight mt-1 font-mono">
                    {{ $deed->deed_no }}
                </h2>
                <p class="text-xs text-slate-500 mt-1">
                    Initiated on {{ $deed->created_at->format('d M Y, h:i A') }} • Bank Trans ID: <span class="font-mono text-slate-700">{{ $deed->bank_trans_id }}</span>
                </p>
            </div>

            <!-- Major Action: Proceed to NGDRS SSO Registration -->
            <form method="POST" action="{{ route('deeds.sso', $deed->id) }}">
                @csrf
                <button type="submit" 
                        class="px-6 py-3.5 rounded-xl text-xs font-bold text-white bg-[#f95721] hover:bg-[#ea580c] transition duration-150 shadow-lg shadow-orange-500/25 flex items-center space-x-2 active:scale-98">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                    <span>Proceed to NGDRS SSO Registration</span>
                </button>
                <div class="text-[10px] text-slate-400 text-center mt-1.5 flex items-center justify-center space-x-1">
                    <svg class="w-3 h-3 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                    </svg>
                    <span>AES-256-CBC Encrypted Handshake</span>
                </div>
            </form>
        </div>

        <!-- 3-Column Dossier Breakdown -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-6 text-xs">
            <!-- Col 1: Borrower Information -->
            <div class="space-y-3 bg-slate-50/70 p-4 rounded-xl border border-slate-100">
                <h4 class="font-bold text-slate-900 uppercase text-[11px] tracking-wider text-slate-400">Borrower Information</h4>
                <div>
                    <span class="text-slate-500 block">Borrower / Mortgagor:</span>
                    <span class="font-bold text-slate-900 text-sm">{{ $deed->borrower_name }}</span>
                </div>
                <div>
                    <span class="text-slate-500 block">PAN Number:</span>
                    <span class="font-mono font-bold text-slate-800">{{ $deed->borrower_pan ?? 'N/A' }}</span>
                </div>
                <div>
                    <span class="text-slate-500 block">Contact Mobile:</span>
                    <span class="text-slate-800">{{ $deed->borrower_mobile ?? 'N/A' }}</span>
                </div>
            </div>

            <!-- Col 2: Bank & Authorized Officer Details -->
            <div class="space-y-3 bg-slate-50/70 p-4 rounded-xl border border-slate-100">
                <h4 class="font-bold text-slate-900 uppercase text-[11px] tracking-wider text-slate-400">Bank & Branch Officer</h4>
                <div>
                    <span class="text-slate-500 block">Bank & Branch:</span>
                    <span class="font-bold text-slate-900">{{ $deed->branch->branch_name }}</span>
                    <span class="text-slate-500 block">{{ $deed->branch->district }} (IFSC: {{ $deed->branch->ifsc_code }})</span>
                </div>
                <div>
                    <span class="text-slate-500 block">Authorized Officer:</span>
                    <span class="font-bold text-slate-900">{{ $deed->creator->name }}</span>
                    <span class="text-[11px] font-mono text-slate-500 block">PAN: {{ $deed->creator->pan_number }} | ID: {{ $deed->creator->officer_id }}</span>
                </div>
                <div>
                    <span class="text-slate-500 block">Officer Email & Mobile:</span>
                    <span class="text-slate-700">{{ $deed->creator->email }} • {{ $deed->creator->mobile_number }}</span>
                </div>
            </div>

            <!-- Col 3: Financial & Duty Particulars -->
            <div class="space-y-3 bg-slate-50/70 p-4 rounded-xl border border-slate-100">
                <h4 class="font-bold text-slate-900 uppercase text-[11px] tracking-wider text-slate-400">Financial Particulars</h4>
                <div>
                    <span class="text-slate-500 block">Sanctioned Loan Amount:</span>
                    <span class="font-mono font-black text-slate-900 text-base">₹{{ number_format($deed->loan_amount, 2) }}</span>
                </div>
                <div>
                    <span class="text-slate-500 block">State Stamp Duty (2%):</span>
                    <span class="font-mono font-bold text-emerald-700 text-sm">₹{{ number_format($deed->stamp_duty, 2) }}</span>
                </div>
                <div>
                    <span class="text-slate-500 block">Last SSO Timestamp:</span>
                    <span class="text-slate-700">{{ $deed->sso_redirected_at ? $deed->sso_redirected_at->format('d M Y, h:i A') : 'Pending Initial SSO' }}</span>
                </div>
            </div>
        </div>

        <!-- Property Description Full Width -->
        <div class="mt-6 pt-5 border-t border-slate-100 text-xs">
            <span class="text-slate-500 font-medium block mb-1">Mortgaged Property & Land Parcel Specification:</span>
            <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 text-slate-800 font-mono text-[11px] leading-relaxed">
                {{ $deed->property_details }}
            </div>
        </div>
    </div>

    <!-- SSO Audit Log Trail Card -->
    <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm shadow-slate-100/60">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold text-slate-900">SSO Handshake Audit Trail</h3>
            <span class="text-xs font-mono text-slate-400">Total Attempts: {{ $deed->auditLogs->count() }}</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-100 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">
                        <th class="py-2.5 px-3">TIMESTAMP</th>
                        <th class="py-2.5 px-3">TRANSACTION ID</th>
                        <th class="py-2.5 px-3">ACTION</th>
                        <th class="py-2.5 px-3">IP ADDRESS</th>
                        <th class="py-2.5 px-3">STATUS</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 text-slate-700">
                    @forelse($deed->auditLogs as $log)
                        <tr>
                            <td class="py-2.5 px-3 text-slate-500 font-mono">{{ $log->created_at->format('d M Y, H:i:s') }}</td>
                            <td class="py-2.5 px-3 font-mono font-medium text-slate-900">{{ $log->bank_trans_id }}</td>
                            <td class="py-2.5 px-3">{{ $log->action }}</td>
                            <td class="py-2.5 px-3 font-mono text-slate-500">{{ $log->ip_address }}</td>
                            <td class="py-2.5 px-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    {{ $log->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-slate-400">
                                No SSO attempts logged yet for this deed. Click the orange "Proceed to NGDRS SSO Registration" button above to initiate.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
