<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NGDRS - National Generic Document Registration System (Assam Portal)</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; }
    </style>
</head>
<body class="h-full flex flex-col text-slate-800 antialiased overflow-x-hidden">

    <!-- Top Indian Flag Tri-color Accent Bar -->
    <div class="h-1.5 w-full bg-gradient-to-r from-orange-500 via-white to-emerald-600"></div>

    <!-- Official Government Header -->
    <header class="bg-white border-b border-slate-200 px-8 py-4 shadow-xs">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 rounded-xl bg-slate-900 text-white flex items-center justify-center font-black text-xl tracking-tighter shadow-md">
                    NG
                </div>
                <div>
                    <div class="flex items-center space-x-2">
                        <span class="text-lg font-black tracking-tight text-slate-900">NGDRS ASSAM</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                            OFFICIAL PORTAL
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 font-medium">
                        National Generic Document Registration System • Revenue & Disaster Management Department
                    </p>
                </div>
            </div>

            <!-- Authenticated Authorized Officer Badge -->
            @if($decryptionSuccess && $data)
                <div class="flex items-center space-x-3 bg-slate-50 border border-slate-200 px-4 py-2 rounded-2xl">
                    <div class="w-9 h-9 rounded-full bg-emerald-600 text-white font-bold flex items-center justify-center text-xs shadow-xs">
                        ✓
                    </div>
                    <div class="text-right">
                        <div class="text-xs font-bold text-slate-900 flex items-center justify-end space-x-1.5">
                            <span>{{ ($data['contact_fname'] ?? '') . ' ' . ($data['contact_lname'] ?? ($data['officer_name'] ?? 'Authorized Officer')) }}</span>
                            <span class="text-[10px] font-mono bg-slate-200 px-1.5 py-0.5 rounded text-slate-700">Bank User: {{ $data['bank_user_id'] ?? 'B' }}</span>
                        </div>
                        <div class="text-[11px] text-slate-500 font-medium">
                            {{ $data['bank_name'] ?? 'Bank' }} (ID: {{ $data['bank_id'] ?? 1 }}) • {{ $data['branch_name'] ?? '' }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </header>

    <!-- Main Content Dossier -->
    <main class="flex-1 max-w-7xl w-full mx-auto p-8 space-y-6">

        @if($decryptionSuccess && $data)
            <!-- SSO Success Banner -->
            <div class="bg-gradient-to-r from-emerald-900 to-slate-900 text-white rounded-3xl p-6 shadow-xl border border-emerald-800/60 relative overflow-hidden">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 relative z-10">
                    <div>
                        <div class="flex items-center space-x-2 text-emerald-400 text-xs font-mono font-bold tracking-wider uppercase mb-1">
                            <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>Single Sign-On Authentication Verified (ArrayBuilder Validated)</span>
                        </div>
                        <h2 class="text-2xl font-black tracking-tight text-white">
                            Mortgage Deed Ingestion: {{ $data['deed_no'] ?? ($data['bank_trans_id'] ?? 'Deed') }}
                        </h2>
                        <p class="text-xs text-slate-300 mt-1">
                            Officer session verified with User Type <strong>'B'</strong>. Mortgage articles [51, 40, 92] enabled in General Info module.
                        </p>
                    </div>

                    <div class="text-right">
                        <span class="text-[11px] font-mono text-slate-400 block">Bank / RTPS Trans ID</span>
                        <span class="text-xs font-mono font-bold text-emerald-300 bg-emerald-950/60 px-3 py-1.5 rounded-lg border border-emerald-800/80 inline-block mt-1">
                            {{ $data['bank_trans_id'] ?? $data['rtps_trans_id'] }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Ingested Deed Particulars Card -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Col 1: Statutory Officer Credentials -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm space-y-4">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Authorized Officer Details</h3>
                    
                    <div class="space-y-3 text-xs">
                        <div>
                            <span class="text-slate-500 block">Officer Name & User ID:</span>
                            <span class="font-bold text-slate-900 text-sm">{{ ($data['contact_fname'] ?? '') . ' ' . ($data['contact_lname'] ?? '') }}</span>
                            <span class="text-slate-500 font-mono text-[11px] block">Bank User ID: {{ $data['bank_user_id'] ?? 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-500 block">Designation:</span>
                            <span class="font-semibold text-slate-800">Authorized Officer</span>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <span class="text-slate-500 block">PAN Number:</span>
                                <span class="font-mono font-bold text-slate-900">{{ $data['pan_no'] ?? ($data['pan_number'] ?? 'N/A') }}</span>
                            </div>
                            <div>
                                <span class="text-slate-500 block">TAN Number:</span>
                                <span class="font-mono font-bold text-slate-900">{{ $data['tan_no'] ?? ($data['tan_number'] ?? 'N/A') }}</span>
                            </div>
                        </div>
                        <div>
                            <span class="text-slate-500 block">Contact & Email:</span>
                            <span class="text-slate-700">{{ $data['mobile_no'] }} • {{ $data['email_id'] ?? ($data['email'] ?? '') }}</span>
                        </div>
                        <div>
                            <span class="text-slate-500 block">Branch & IFSC:</span>
                            <span class="text-slate-900 font-semibold">{{ $data['branch_name'] }}</span>
                            <span class="font-mono text-slate-500 block">IFSC: {{ $data['ifsc_code'] }} ({{ $data['city'] ?? $data['district'] }})</span>
                        </div>
                    </div>
                </div>

                <!-- Col 2 & 3: Application & Land Record Details -->
                <div class="lg:col-span-2 bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Mortgage & Property Particulars</h3>
                            <span class="text-xs font-mono font-bold text-orange-600 bg-orange-50 px-2.5 py-1 rounded-md border border-orange-200">
                                {{ $data['deed_no'] }}
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 my-5 text-xs">
                            <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-100">
                                <span class="text-slate-500 block">Borrower / Mortgagor:</span>
                                <span class="font-bold text-slate-900 text-sm mt-0.5 block">{{ $data['borrower_name'] }}</span>
                                <span class="font-mono text-slate-500 text-[11px] mt-0.5 block">PAN: {{ $data['borrower_pan'] ?? 'N/A' }}</span>
                            </div>

                            <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-100">
                                <span class="text-slate-500 block">Sanctioned Loan:</span>
                                <span class="font-mono font-black text-slate-900 text-base mt-0.5 block">
                                    ₹{{ number_format($data['loan_amount'], 2) }}
                                </span>
                            </div>

                            <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-100">
                                <span class="text-slate-500 block">Stamp Duty Collected:</span>
                                <span class="font-mono font-black text-emerald-700 text-base mt-0.5 block">
                                    ₹{{ number_format($data['stamp_duty'], 2) }}
                                </span>
                            </div>
                        </div>

                        <div>
                            <span class="text-xs font-medium text-slate-500 block mb-1">Assam Land Records Parcel Specification:</span>
                            <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-xl font-mono text-xs text-slate-800 leading-relaxed">
                                {{ $data['property_details'] }}
                            </div>
                        </div>
                    </div>

                    <!-- SRO Simulation Actions -->
                    <div class="mt-6 pt-5 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center space-x-2">
                            <!-- Action 1: SRO Review -->
                            <form method="POST" action="{{ route('mock-ngdrs.update-status') }}">
                                @csrf
                                <input type="hidden" name="deed_no" value="{{ $data['deed_no'] }}">
                                <input type="hidden" name="status" value="SRO review">
                                <button type="submit" class="px-4 py-2.5 rounded-xl text-xs font-semibold bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition border border-indigo-200">
                                    Mark as SRO Review
                                </button>
                            </form>

                            <!-- Action 2: SRO Approve & Complete -->
                            <form method="POST" action="{{ route('mock-ngdrs.update-status') }}">
                                @csrf
                                <input type="hidden" name="deed_no" value="{{ $data['deed_no'] }}">
                                <input type="hidden" name="status" value="Completed">
                                <button type="submit" class="px-4 py-2.5 rounded-xl text-xs font-semibold bg-emerald-600 text-white hover:bg-emerald-700 transition shadow-sm">
                                    ✓ Approve & Create Land Record Charge
                                </button>
                            </form>
                        </div>

                        <a href="{{ route('deeds.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 underline">
                            ← Return to Bank Portal
                        </a>
                    </div>
                </div>
            </div>

            <!-- Collapsible Raw Decrypted JSON Drawer for Developers / Auditors -->
            <details class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm text-xs">
                <summary class="font-bold text-slate-900 cursor-pointer flex items-center justify-between select-none">
                    <span>Inspect Raw AES-256-CBC Decrypted Ingestion Payload</span>
                    <span class="text-orange-600 font-mono text-[11px]">View JSON Schema</span>
                </summary>
                <div class="mt-4 pt-4 border-t border-slate-100 font-mono text-[11px] bg-slate-900 text-emerald-400 p-4 rounded-xl overflow-x-auto">
                    <pre>{{ json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                </div>
            </details>

        @else
            <!-- Error State -->
            <div class="bg-white rounded-3xl p-12 text-center border border-rose-200 shadow-xl max-w-lg mx-auto">
                <div class="w-16 h-16 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mx-auto mb-4 text-2xl font-bold">
                    ✕
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-2">SSO Authentication Failed</h3>
                <p class="text-xs text-slate-500 mb-6">
                    {{ $errorMessage ?? 'Unable to decrypt payload or key handshake invalid.' }}
                </p>
                <a href="{{ route('login') }}" class="px-5 py-2.5 rounded-xl text-xs font-semibold text-white bg-[#f95721] hover:bg-[#ea580c] transition shadow-md">
                    Return to Bank Login
                </a>
            </div>
        @endif

    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-4 px-8 text-center text-[11px] text-slate-400">
        Government of Assam • Revenue & Disaster Management • National Generic Document Registration System (NGDRS)
    </footer>

</body>
</html>
