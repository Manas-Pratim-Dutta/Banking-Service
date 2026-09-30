@extends('layouts.app')

@section('title', 'Branch Users (Authorized Officers) - NGDRS Bank Portal')
@section('page_title', 'Branch users')

@section('content')
<div class="space-y-6">

    <!-- Header Section matching screenshot -->
    <div>
        <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Branch users</h2>
        <p class="text-xs text-slate-500 mt-0.5">
            Create and manage branch level logins for your bank network. All branch users operate as designated <strong>Authorized Officers</strong> with statutory PAN/TAN credentials.
        </p>
    </div>

    <!-- Create Branch User Form Card matching screenshot -->
    <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm shadow-slate-100/60">
        <div class="mb-5">
            <h3 class="text-sm font-bold text-slate-900">Create branch user</h3>
            <p class="text-xs text-slate-400 mt-0.5">Sets up a Sub login for an Authorized Officer. Credentials and compliance tokens are issued automatically.</p>
        </div>

        <form method="POST" action="{{ route('branch-users.store') }}">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
                <!-- Select Branch -->
                <div>
                    <label class="block font-medium text-slate-600 mb-1.5">Select Branch</label>
                    <select name="branch_id" id="branch_dropdown" onchange="autoFillBranchDetails(this)" required class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                        <option value="">Select branch</option>
                        @foreach($branches as $br)
                            <option value="{{ $br->id }}" 
                                    data-name="{{ $br->branch_name }}"
                                    data-code="{{ $br->branch_code }}"
                                    data-ifsc="{{ $br->ifsc_code }}"
                                    data-district="{{ $br->district }}"
                                    data-address="{{ $br->address }}">
                                {{ $br->branch_name }} ({{ $br->branch_code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- District (Auto-filled / selected) -->
                <div>
                    <label class="block font-medium text-slate-600 mb-1.5">District</label>
                    <input type="text" id="disp_district" readonly placeholder="Select branch first" 
                           class="w-full rounded-xl border border-slate-200 bg-slate-100/70 px-3.5 py-2 text-slate-700 cursor-not-allowed">
                </div>

                <!-- IFSC code (Auto-filled) -->
                <div>
                    <label class="block font-medium text-slate-600 mb-1.5">IFSC code</label>
                    <input type="text" id="disp_ifsc" readonly placeholder="Auto-populated" 
                           class="w-full font-mono rounded-xl border border-slate-200 bg-slate-100/70 px-3.5 py-2 text-slate-700 cursor-not-allowed">
                </div>

                <!-- Branch code (Auto-filled) -->
                <div>
                    <label class="block font-medium text-slate-600 mb-1.5">Branch code</label>
                    <input type="text" id="disp_code" readonly placeholder="Auto-populated" 
                           class="w-full font-mono rounded-xl border border-slate-200 bg-slate-100/70 px-3.5 py-2 text-slate-700 cursor-not-allowed">
                </div>

                <!-- Authorized Officer Name -->
                <div>
                    <label class="block font-medium text-slate-600 mb-1.5">Authorized Officer Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Bikash Sarma" 
                           class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                </div>

                <!-- Contact mobile number -->
                <div>
                    <label class="block font-medium text-slate-600 mb-1.5">Contact mobile number</label>
                    <input type="text" name="mobile_number" value="{{ old('mobile_number') }}" required placeholder="98xxxxxx21" 
                           class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                </div>

                <!-- Email ID -->
                <div>
                    <label class="block font-medium text-slate-600 mb-1.5">Email ID (Login Username)</label>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="branch.ghy@agb.co.in" 
                           class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                </div>

                <!-- Password -->
                <div>
                    <label class="block font-medium text-slate-600 mb-1.5">Password</label>
                    <input type="password" name="password" required value="password" placeholder="••••••••" 
                           class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                </div>

                <!-- MANDATORY COMPLIANCE FIELDS REQUIRED BY USER -->
                <!-- PAN Number -->
                <div>
                    <label class="block font-medium text-slate-600 mb-1.5">
                        Officer PAN Number <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="pan_number" value="{{ old('pan_number') }}" required placeholder="e.g. BPZPS2914K" 
                           class="w-full font-mono uppercase rounded-xl border border-slate-200 px-3.5 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                </div>

                <!-- TAN Number -->
                <div>
                    <label class="block font-medium text-slate-600 mb-1.5">
                        Branch / Officer TAN Number
                    </label>
                    <input type="text" name="tan_number" value="{{ old('tan_number') }}" placeholder="e.g. SHLB02914A" 
                           class="w-full font-mono uppercase rounded-xl border border-slate-200 px-3.5 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                </div>

                <!-- Age -->
                <div>
                    <label class="block font-medium text-slate-600 mb-1.5">
                        Age (Years) <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" name="age" value="{{ old('age', 35) }}" min="18" max="80" required placeholder="e.g. 38" 
                           class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                </div>

                <!-- Residential / Official Address -->
                <div>
                    <label class="block font-medium text-slate-600 mb-1.5">
                        Officer Residential Address <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="address" value="{{ old('address') }}" required placeholder="Street, Quarter / Town, City" 
                           class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                </div>
            </div>

            <!-- Action Button matching screenshot -->
            <div class="mt-6 flex justify-start">
                <button type="submit" 
                        class="py-2.5 px-6 rounded-xl text-xs font-semibold text-white bg-[#f95721] hover:bg-[#ea580c] transition shadow-md shadow-orange-500/20">
                    Create branch user
                </button>
            </div>
        </form>
    </div>

    <!-- All Branch Users Table matching screenshot -->
    <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm shadow-slate-100/60">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
            <h3 class="text-sm font-bold text-slate-900">All branch users</h3>

            <!-- Search filter -->
            <form method="GET" action="{{ route('branch-users.index') }}" class="flex items-center space-x-2">
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="Search officer, ID, PAN, email..." 
                       class="text-xs rounded-xl border border-slate-200 px-3 py-1.5 w-60 focus:outline-none focus:border-orange-500">
                <button type="submit" class="px-3 py-1.5 bg-slate-100 text-slate-700 hover:bg-slate-200 text-xs font-medium rounded-xl transition">
                    Search
                </button>
                @if(request('search'))
                    <a href="{{ route('branch-users.index') }}" class="text-xs text-slate-400 hover:text-slate-600">Clear</a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-100 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">
                        <th class="py-3 px-4">BRANCH USER ID</th>
                        <th class="py-3 px-4">BRANCH NAME</th>
                        <th class="py-3 px-4">DISTRICT</th>
                        <th class="py-3 px-4">IFSC</th>
                        <th class="py-3 px-4">CONTACT & OFFICER</th>
                        <th class="py-3 px-4">PAN / TAN</th>
                        <th class="py-3 px-4">AGE</th>
                        <th class="py-3 px-4 text-center">STATUS</th>
                        <th class="py-3 px-4">CREATED</th>
                        <th class="py-3 px-4 text-right">ACTIONS</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 text-slate-700">
                    @forelse($officers as $officer)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3.5 px-4 font-mono font-bold text-slate-900">
                                {{ $officer->officer_id }}
                            </td>
                            <td class="py-3.5 px-4 font-medium text-slate-800">
                                {{ $officer->branch->branch_name ?? '—' }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-600">
                                {{ $officer->branch->district ?? '—' }}
                            </td>
                            <td class="py-3.5 px-4 font-mono text-slate-600">
                                {{ $officer->branch->ifsc_code ?? '—' }}
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900">{{ $officer->name }}</div>
                                <div class="text-[11px] text-slate-500">{{ $officer->mobile_number }}</div>
                                <div class="text-[11px] text-slate-400">{{ $officer->email }}</div>
                            </td>
                            <td class="py-3.5 px-4 font-mono">
                                <div class="text-slate-900 font-semibold">{{ $officer->pan_number }}</div>
                                @if($officer->tan_number)
                                    <div class="text-[10px] text-slate-400">TAN: {{ $officer->tan_number }}</div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-slate-600">
                                {{ $officer->age ?? '—' }} yrs
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium {{ $officer->status === 'Active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $officer->status }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-500 text-[11px]">
                                {{ $officer->created_at->format('d M Y') }}
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <form method="POST" action="{{ route('branch-users.toggle', $officer->id) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-[11px] font-medium text-slate-500 hover:text-slate-900 underline">
                                        {{ $officer->status === 'Active' ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="py-8 text-center text-slate-400">
                                No branch users found. Use the form above to add an Authorized Officer.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $officers->links() }}
        </div>
    </div>

</div>

@push('scripts')
<script>
    function autoFillBranchDetails(select) {
        const option = select.options[select.selectedIndex];
        if (!option.value) {
            document.getElementById('disp_district').value = '';
            document.getElementById('disp_ifsc').value = '';
            document.getElementById('disp_code').value = '';
            return;
        }
        document.getElementById('disp_district').value = option.getAttribute('data-district') || '';
        document.getElementById('disp_ifsc').value = option.getAttribute('data-ifsc') || '';
        document.getElementById('disp_code').value = option.getAttribute('data-code') || '';
    }
</script>
@endpush
@endsection
