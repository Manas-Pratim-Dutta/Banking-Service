@extends('layouts.app')

@section('title', 'Track registration - NGDRS Bank Portal')
@section('page_title', 'Track registration')

@section('content')
<div class="space-y-6 max-w-6xl">

    <!-- Search Section matching screenshot -->
    <div>
        <h2 class="text-lg font-bold text-slate-900 tracking-tight mb-3">Track registration</h2>
        
        <form method="GET" action="{{ route('tracking.index') }}" class="flex items-center space-x-3 max-w-md">
            <input type="text" 
                   name="deed_no" 
                   value="{{ $searchQuery ?? ($deed ? $deed->deed_no : 'MD-2026-0450') }}" 
                   placeholder="e.g. MD-2026-0450 or AGB/2026/BR001/00001" 
                   class="flex-1 text-xs rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-slate-800 font-mono focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
            
            <button type="submit" 
                    class="py-2.5 px-6 rounded-xl text-xs font-semibold text-white bg-[#f95721] hover:bg-[#ea580c] transition duration-150 shadow-md shadow-orange-500/20">
                Search
            </button>
        </form>

        <!-- Quick Switcher -->
        @if($recentDeeds->count() > 1)
            <div class="flex items-center space-x-2 mt-2 text-[11px] text-slate-400">
                <span>Quick switch:</span>
                @foreach($recentDeeds as $rd)
                    <a href="{{ route('tracking.index', ['deed_no' => $rd->deed_no]) }}" 
                       class="font-mono text-slate-600 hover:text-orange-600 underline">
                        {{ $rd->deed_no }}
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    @if($deed)
        <!-- Two Column Layout matching screenshot -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
            
            <!-- Left Card: Status Timeline matching screenshot -->
            <div class="lg:col-span-2 bg-white rounded-2xl p-8 border border-slate-100 shadow-sm shadow-slate-100/60">
                <div class="flex items-center justify-between mb-8 pb-4 border-b border-slate-100">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Status timeline</h3>
                    <div class="flex items-center space-x-2">
                        <span class="text-xs font-mono font-bold text-slate-900">{{ $deed->deed_no }}</span>
                        <span class="text-xs text-slate-400">• {{ $deed->borrower_name }}</span>
                    </div>
                </div>

                @php
                    // Determine which step is reached based on deed status
                    // Steps:
                    // 1: Submitted by bank
                    // 2: System validated
                    // 3: e-Stamp generated
                    // 4: Bank authorised & signed
                    // 5: Payment confirmed
                    // 6: SRO review
                    // 7: Digital registration & charge created
                    // 8: Synced to bank system
                    $currentStep = 5; // Default for 'In progress'
                    if ($deed->status === 'New') {
                        $currentStep = 3;
                    } elseif ($deed->status === 'In progress') {
                        $currentStep = 5;
                    } elseif ($deed->status === 'SRO review') {
                        $currentStep = 6;
                    } elseif ($deed->status === 'Completed' || $deed->status === 'Registered') {
                        $currentStep = 8;
                    }

                    $steps = [
                        1 => 'Submitted by bank',
                        2 => 'System validated',
                        3 => 'e-Stamp generated',
                        4 => 'Bank authorised & signed',
                        5 => 'Payment confirmed',
                        6 => 'SRO review',
                        7 => 'Digital registration & charge created',
                        8 => 'Synced to bank system',
                    ];
                @endphp

                <!-- Vertical Stepper matching screenshot -->
                <div class="relative pl-3 space-y-7">
                    <!-- Vertical connecting line -->
                    <div class="absolute left-6 top-3 bottom-3 w-0.5 bg-slate-200"></div>

                    @foreach($steps as $stepNum => $stepLabel)
                        @php
                            $isPassed = $stepNum <= $currentStep;
                            $isCurrent = $stepNum === $currentStep;
                        @endphp
                        <div class="relative flex items-center space-x-4">
                            <!-- Step Circle Indicator -->
                            @if($isPassed)
                                <div class="w-6 h-6 rounded-full bg-emerald-500 text-white flex items-center justify-center text-xs font-bold shadow-xs z-10">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                            @else
                                <div class="w-6 h-6 rounded-full bg-slate-100 border-2 border-slate-300 text-transparent flex items-center justify-center text-xs z-10">
                                    •
                                </div>
                            @endif

                            <!-- Step Label -->
                            <div class="flex-1">
                                <span class="text-xs font-medium {{ $isPassed ? 'text-slate-800' : 'text-slate-400' }}">
                                    {{ $stepLabel }}
                                </span>
                                @if($isCurrent && $stepNum < 8)
                                    <span class="ml-2 text-[10px] font-mono px-2 py-0.5 rounded-full bg-orange-50 text-orange-600 font-semibold border border-orange-200">
                                        Current Stage
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Action to perform SSO Handshake if SRO review pending -->
                <div class="mt-8 pt-6 border-t border-slate-100 flex items-center justify-between">
                    <div class="text-xs text-slate-500">
                        Sanctioned Loan: <strong class="text-slate-800 font-mono">₹{{ number_format($deed->loan_amount, 2) }}</strong>
                    </div>

                    <form method="POST" action="{{ route('deeds.sso', $deed->id) }}">
                        @csrf
                        <button type="submit" 
                                class="px-5 py-2 rounded-xl text-xs font-semibold text-white bg-[#f95721] hover:bg-[#ea580c] transition shadow-md shadow-orange-500/20">
                            Perform NGDRS SSO Handshake →
                        </button>
                    </form>
                </div>
            </div>

            <!-- Right Card: Registered Deed matching screenshot -->
            <div class="bg-white rounded-2xl p-8 border border-slate-100 shadow-sm shadow-slate-100/60 text-center flex flex-col justify-center items-center min-h-[220px]">
                <h4 class="text-xs font-bold text-slate-900 mb-3">Registered deed</h4>
                
                @if($deed->status === 'Completed' || $deed->status === 'Registered')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 mb-4">
                        Registered & Charge Created
                    </span>
                    <a href="javascript:void(0)" onclick="alert('Downloading Registered Deed Certificate for {{ $deed->deed_no }}')"
                       class="text-xs font-semibold text-orange-600 hover:text-orange-700 underline">
                        Download e-Deed Certificate
                    </a>
                @else
                    <span class="inline-flex items-center px-3.5 py-1 rounded-full text-xs font-semibold bg-orange-50 text-orange-600 border border-orange-200/80 mb-4">
                        Pending
                    </span>
                    <span class="text-xs text-slate-400 cursor-not-allowed">
                        Download
                    </span>
                    <p class="text-[10px] text-slate-400 mt-2 max-w-[180px]">
                        Available once SRO digital signature and charge creation are finalized.
                    </p>
                @endif
            </div>

        </div>
    @else
        <div class="bg-white rounded-2xl p-12 text-center border border-slate-100 shadow-sm text-slate-400 text-xs">
            No deed records found for tracking. Use the "+ Initiate registration" button on your dashboard to initiate an application.
        </div>
    @endif

</div>
@endsection
