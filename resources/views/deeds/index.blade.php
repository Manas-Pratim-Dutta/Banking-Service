@extends('layouts.app')

@section('title', 'Mortgage Deeds & Applications - NGDRS Bank Portal')
@section('page_title', 'Deed Applications')

@section('content')
<div class="space-y-6">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Mortgage Deed Applications</h2>
            <p class="text-xs text-slate-500 mt-0.5">
                Sequential deed generation and live tracking for NGDRS document registration.
            </p>
        </div>

        @if(session('auth_type') === 'branch')
            <form method="POST" action="{{ route('deeds.quick-initiate') }}">
                @csrf
                <button type="submit" 
                        class="inline-flex items-center px-5 py-2.5 rounded-xl text-xs font-semibold text-white bg-[#f95721] hover:bg-[#ea580c] transition shadow-md shadow-orange-500/20 active:scale-98">
                    + Initiate registration
                </button>
            </form>
        @endif
    </div>

    <!-- Filter & Table Card -->
    <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm shadow-slate-100/60">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-5">
            <div class="flex items-center space-x-2">
                <h3 class="text-sm font-bold text-slate-900">Application Tracking Pipeline</h3>
            </div>

            <!-- Filters -->
            <form method="GET" action="{{ route('deeds.index') }}" class="flex flex-wrap items-center gap-2">
                <select name="status" onchange="this.form.submit()" class="text-xs rounded-xl border border-slate-200 px-3 py-1.5 focus:outline-none focus:border-orange-500">
                    <option value="">All Statuses</option>
                    <option value="New" {{ request('status') === 'New' ? 'selected' : '' }}>New</option>
                    <option value="In progress" {{ request('status') === 'In progress' ? 'selected' : '' }}>In progress</option>
                    <option value="SRO review" {{ request('status') === 'SRO review' ? 'selected' : '' }}>SRO review</option>
                    <option value="Completed" {{ request('status') === 'Completed' ? 'selected' : '' }}>Completed</option>
                </select>

                <select name="branch_id" onchange="this.form.submit()" class="text-xs rounded-xl border border-slate-200 px-3 py-1.5 focus:outline-none focus:border-orange-500">
                    <option value="">All Branches</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ request('branch_id') == $b->id ? 'selected' : '' }}>
                            {{ $b->branch_name }}
                        </option>
                    @endforeach
                </select>

                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="Search deed no, borrower, PAN..." 
                       class="text-xs rounded-xl border border-slate-200 px-3 py-1.5 w-56 focus:outline-none focus:border-orange-500">
                
                <button type="submit" class="px-3 py-1.5 bg-slate-100 text-slate-700 hover:bg-slate-200 text-xs font-medium rounded-xl transition">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'status', 'branch_id']))
                    <a href="{{ route('deeds.index') }}" class="text-xs text-slate-400 hover:text-slate-600">Reset</a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-100 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">
                        <th class="py-3 px-4">DEED NO.</th>
                        <th class="py-3 px-4">BORROWER</th>
                        <th class="py-3 px-4">BRANCH</th>
                        <th class="py-3 px-4">LOAN / STAMP DUTY</th>
                        <th class="py-3 px-4">CREATED BY (OFFICER)</th>
                        <th class="py-3 px-4 text-center">STATUS</th>
                        <th class="py-3 px-4 text-right">ACTION</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 text-slate-700">
                    @forelse($deeds as $deed)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3.5 px-4 font-mono font-bold text-slate-900">
                                <a href="{{ route('deeds.show', $deed->id) }}" class="text-orange-600 hover:underline">
                                    {{ $deed->deed_no }}
                                </a>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900">{{ $deed->borrower_name }}</div>
                                <div class="font-mono text-[11px] text-slate-400">{{ $deed->borrower_pan ?? 'PAN: N/A' }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-medium text-slate-800">{{ $deed->branch->branch_name ?? '—' }}</div>
                                <div class="text-[11px] text-slate-400">{{ $deed->branch->district ?? '—' }}</div>
                            </td>
                            <td class="py-3.5 px-4 font-mono">
                                <div class="font-semibold text-slate-900">₹{{ number_format($deed->loan_amount, 2) }}</div>
                                <div class="text-[11px] text-slate-400">Duty: ₹{{ number_format($deed->stamp_duty, 2) }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-medium text-slate-800">{{ $deed->creator->name ?? 'Authorized Officer' }}</div>
                                <div class="text-[11px] text-slate-400">{{ $deed->creator->officer_id ?? '' }}</div>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @php
                                    $badgeStyles = [
                                        'New' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'In progress' => 'bg-orange-50 text-orange-700 border-orange-200',
                                        'SRO review' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                        'Completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'Rejected' => 'bg-rose-50 text-rose-700 border-rose-200',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium border {{ $badgeStyles[$deed->status] ?? 'bg-slate-100 text-slate-700 border-slate-200' }}">
                                    {{ $deed->status }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <a href="{{ route('deeds.show', $deed->id) }}" 
                                   class="inline-flex items-center px-3 py-1 rounded-lg text-[11px] font-semibold text-slate-700 bg-slate-100 hover:bg-orange-50 hover:text-orange-600 transition">
                                    Details & SSO →
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">
                                No deed applications found. Click "+ Initiate New Deed" to create one.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $deeds->links() }}
        </div>
    </div>

</div>
@endsection
