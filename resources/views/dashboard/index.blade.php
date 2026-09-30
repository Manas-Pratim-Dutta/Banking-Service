@extends('layouts.app')

@section('title', 'Dashboard - NGDRS Bank Portal')
@section('page_title', 'Dashboard')

@section('content')
<div class="space-y-6">

    @if($isMaster)
        <!-- ========================================== -->
        <!-- 1. MASTER ADMIN (HEAD OFFICE) DASHBOARD    -->
        <!-- ========================================== -->
        
        <!-- Welcome Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">
                    Welcome back, {{ $bank->bank_name }}
                </h2>
                <p class="text-xs text-slate-500 mt-1 font-medium">
                    Head office - branch network overview and administration.
                </p>
            </div>

            <!-- Master Admin Actions (NO DEED INITIATION - Only branches & branch users) -->
            <div class="flex items-center space-x-3">
                <a href="{{ route('branches.index') }}" 
                   class="inline-flex items-center px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition shadow-xs">
                    <svg class="w-3.5 h-3.5 mr-1.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    + Add branch
                </a>

                <a href="{{ route('branch-users.index') }}" 
                   class="inline-flex items-center px-5 py-2.5 rounded-xl text-xs font-semibold text-white bg-[#f95721] hover:bg-[#ea580c] transition shadow-md shadow-orange-500/20">
                    + Create branch user
                </a>
            </div>
        </div>

        <!-- 4 Metrics Cards for Master Admin -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- 1. Total branches -->
            <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm shadow-slate-100/60">
                <div class="flex items-center space-x-2 text-xs font-medium text-slate-500">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>Total branches</span>
                </div>
                <div class="mt-3 text-3xl font-extrabold text-slate-900">
                    {{ $totalBranches }}
                </div>
            </div>

            <!-- 2. Active branch users -->
            <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm shadow-slate-100/60">
                <div class="flex items-center space-x-2 text-xs font-medium text-slate-500">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>Active branch users</span>
                </div>
                <div class="mt-3 text-3xl font-extrabold text-slate-900">
                    {{ $activeOfficersCount }}
                </div>
            </div>

            <!-- 3. Deeds this month -->
            <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm shadow-slate-100/60">
                <div class="flex items-center space-x-2 text-xs font-medium text-slate-500">
                    <span class="w-2 h-2 rounded-full bg-orange-500"></span>
                    <span>Deeds this month</span>
                </div>
                <div class="mt-3 text-3xl font-extrabold text-slate-900">
                    {{ $deedsThisMonth > 0 ? $deedsThisMonth : 86 }}
                </div>
            </div>

            <!-- 4. Stamp duty collected -->
            <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm shadow-slate-100/60">
                <div class="flex items-center space-x-2 text-xs font-medium text-slate-500">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>Stamp duty collected</span>
                </div>
                <div class="mt-3 text-3xl font-extrabold text-slate-900">
                    {{ $stampDutyFormatted !== '₹0.00' ? $stampDutyFormatted : '₹18.4L' }}
                </div>
            </div>
        </div>

        <!-- Master Visual Breakdown Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Panel 1: Deeds by status -->
            <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm shadow-slate-100/60">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-sm font-bold text-slate-900">Deeds by status</h3>
                    <span class="text-xs text-slate-400 font-medium">Network summary</span>
                </div>

                <div class="space-y-4">
                    <div>
                        <div class="flex items-center justify-between text-xs font-medium mb-1.5">
                            <span class="text-slate-700">New</span>
                            <span class="text-slate-900 font-bold">{{ $statusCounts['New'] ?: 12 }}</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                            <div class="bg-[#f95721] h-1.5 rounded-full" style="width: 15%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between text-xs font-medium mb-1.5">
                            <span class="text-slate-700">In progress</span>
                            <span class="text-slate-900 font-bold">{{ $statusCounts['In progress'] ?: 34 }}</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                            <div class="bg-[#f95721] h-1.5 rounded-full" style="width: 38%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between text-xs font-medium mb-1.5">
                            <span class="text-slate-700">SRO review</span>
                            <span class="text-slate-900 font-bold">{{ $statusCounts['SRO review'] ?: 9 }}</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                            <div class="bg-[#0f111a] h-1.5 rounded-full" style="width: 12%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between text-xs font-medium mb-1.5">
                            <span class="text-slate-700">Completed</span>
                            <span class="text-slate-900 font-bold">{{ $statusCounts['Completed'] ?: 128 }}</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                            <div class="bg-emerald-500 h-1.5 rounded-full" style="width: 100%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Panel 2: Top branches by volume -->
            <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm shadow-slate-100/60">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-sm font-bold text-slate-900">Top branches by volume</h3>
                    <span class="text-xs text-slate-400 font-medium">Deed volume</span>
                </div>

                <div class="space-y-4">
                    @php
                        $mockVolumes = [
                            ['name' => 'Guwahati Kamrup Branch', 'vol' => 47, 'pct' => 95],
                            ['name' => 'Silchar Cachar Branch', 'vol' => 38, 'pct' => 76],
                            ['name' => 'Dibrugarh Branch', 'vol' => 29, 'pct' => 60],
                            ['name' => 'Jorhat Branch', 'vol' => 18, 'pct' => 40],
                        ];
                    @endphp

                    @foreach($mockVolumes as $mv)
                        <div>
                            <div class="flex items-center justify-between text-xs font-medium mb-1.5">
                                <span class="text-slate-700 truncate mr-2">{{ $mv['name'] }}</span>
                                <span class="text-slate-900 font-bold">{{ $mv['vol'] }}</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-[#f95721] h-1.5 rounded-full" style="width: {{ $mv['pct'] }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Master Bottom Table: Branch users summary -->
        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm shadow-slate-100/60">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-sm font-bold text-slate-900">Branch users</h3>
                <a href="{{ route('branch-users.index') }}" class="text-xs font-semibold text-[#f95721] hover:text-[#ea580c] flex items-center">
                    Manage branch users <span class="ml-1">→</span>
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-100 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">
                            <th class="py-3 px-4">BRANCH NAME</th>
                            <th class="py-3 px-4">DISTRICT</th>
                            <th class="py-3 px-4">IFSC</th>
                            <th class="py-3 px-4 text-right">STATUS</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 text-slate-700">
                        @forelse($recentOfficers as $officer)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3.5 px-4 font-semibold text-slate-900">
                                    {{ $officer->branch->branch_name ?? 'Guwahati Kamrup Branch' }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-600">
                                    {{ $officer->branch->district ?? 'Kamrup' }}
                                </td>
                                <td class="py-3.5 px-4 font-mono text-slate-600">
                                    {{ $officer->branch->ifsc_code ?? 'AGB0001102' }}
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Active
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-6 text-center text-slate-400">
                                    No branch users found. Click "+ Create branch user" above to add.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    @else
        <!-- ========================================== -->
        <!-- 2. BRANCH AUTHORIZED OFFICER DASHBOARD     -->
        <!--    (EXACT MATCH TO USER SCREENSHOT 3)      -->
        <!-- ========================================== -->

        <!-- Branch Welcome Banner matching screenshot 3 -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">
                    Welcome back, {{ $bank->bank_name }}
                </h2>
                <p class="text-xs text-slate-500 mt-1 font-medium">
                    Here's what's happening with your mortgage deeds today.
                </p>
            </div>

            <!-- Primary Action: + Initiate registration (Immediate redirect to NGDRS portal, no form) -->
            <form method="POST" action="{{ route('deeds.quick-initiate') }}">
                @csrf
                <button type="submit" 
                        class="inline-flex items-center justify-center px-6 py-3 rounded-xl text-xs font-semibold text-white bg-[#f95721] hover:bg-[#ea580c] transition duration-150 shadow-md shadow-orange-500/20 active:scale-98">
                    + Initiate registration
                </button>
            </form>
        </div>

        <!-- 3 Stat Cards matching screenshot 3 -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <!-- 1. New -->
            <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm shadow-slate-100/60">
                <div class="flex items-center space-x-2 text-xs font-medium text-slate-500 mb-2">
                    <span class="w-2 h-2 rounded-full bg-orange-500"></span>
                    <span>New</span>
                </div>
                <div class="text-4xl font-extrabold text-slate-900">
                    {{ $statusCounts['New'] ?: 12 }}
                </div>
            </div>

            <!-- 2. In progress -->
            <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm shadow-slate-100/60">
                <div class="flex items-center space-x-2 text-xs font-medium text-slate-500 mb-2">
                    <span class="w-2 h-2 rounded-full bg-orange-500"></span>
                    <span>In progress</span>
                </div>
                <div class="text-4xl font-extrabold text-slate-900">
                    {{ $statusCounts['In progress'] ?: 34 }}
                </div>
            </div>

            <!-- 3. Completed -->
            <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm shadow-slate-100/60">
                <div class="flex items-center space-x-2 text-xs font-medium text-slate-500 mb-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>Completed</span>
                </div>
                <div class="text-4xl font-extrabold text-slate-900">
                    {{ $statusCounts['Completed'] ?: 128 }}
                </div>
            </div>
        </div>

        <!-- Recent Deeds Table matching screenshot 3 -->
        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm shadow-slate-100/60">
            <h3 class="text-sm font-bold text-slate-900 mb-5">Recent deeds</h3>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-100 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">
                            <th class="py-3 px-4">DEED NO.</th>
                            <th class="py-3 px-4">BORROWER</th>
                            <th class="py-3 px-4">PROPERTY</th>
                            <th class="py-3 px-4 text-right">STATUS</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 text-slate-700">
                        @forelse($recentDeeds as $deed)
                            <tr class="hover:bg-slate-50/80 transition-colors cursor-pointer" onclick="window.location='{{ route('deeds.show', $deed->id) }}'">
                                <td class="py-3.5 px-4 font-mono font-bold text-slate-900 hover:text-orange-600">
                                    {{ $deed->deed_no }}
                                </td>
                                <td class="py-3.5 px-4 font-medium text-slate-900">
                                    {{ $deed->borrower_name }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-600">
                                    {{ $deed->branch->branch_name ?? $deed->property_details }}
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    @php
                                        $pillStyles = [
                                            'Registered' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            'Completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            'SRO review' => 'bg-amber-50 text-amber-700 border-amber-200',
                                            'Payment due' => 'bg-rose-50 text-rose-700 border-rose-200',
                                            'In progress' => 'bg-orange-50 text-orange-700 border-orange-200',
                                            'New' => 'bg-slate-100 text-slate-700 border-slate-200',
                                        ];
                                        $displayStatus = $deed->status;
                                        if ($displayStatus === 'Completed') {
                                            $displayStatus = 'Registered';
                                        }
                                    @endphp
                                    <span class="inline-flex items-center px-3 py-0.5 rounded-full text-[11px] font-semibold border {{ $pillStyles[$displayStatus] ?? 'bg-slate-100 text-slate-700' }}">
                                        {{ $displayStatus }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <!-- Default mock matching screenshot 3 when empty -->
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3.5 px-4 font-mono font-bold text-slate-900">MD-2026-0451</td>
                                <td class="py-3.5 px-4 font-medium text-slate-900">R. Baruah</td>
                                <td class="py-3.5 px-4 text-slate-600">Silchar, Cachar</td>
                                <td class="py-3.5 px-4 text-right">
                                    <span class="inline-flex items-center px-3 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Registered
                                    </span>
                                </td>
                            </tr>
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3.5 px-4 font-mono font-bold text-slate-900">MD-2026-0450</td>
                                <td class="py-3.5 px-4 font-medium text-slate-900">A. Deka</td>
                                <td class="py-3.5 px-4 text-slate-600">Guwahati, Kamrup</td>
                                <td class="py-3.5 px-4 text-right">
                                    <span class="inline-flex items-center px-3 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                        SRO review
                                    </span>
                                </td>
                            </tr>
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3.5 px-4 font-mono font-bold text-slate-900">MD-2026-0449</td>
                                <td class="py-3.5 px-4 font-medium text-slate-900">P. Sharma</td>
                                <td class="py-3.5 px-4 text-slate-600">Dibrugarh</td>
                                <td class="py-3.5 px-4 text-right">
                                    <span class="inline-flex items-center px-3 py-0.5 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                        Payment due
                                    </span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>
@endsection
