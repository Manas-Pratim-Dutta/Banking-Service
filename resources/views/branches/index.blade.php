@extends('layouts.app')

@section('title', 'Bank Branches - NGDRS Bank Portal')
@section('page_title', 'Branches')

@section('content')
<div class="space-y-6">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Bank Branches</h2>
            <p class="text-xs text-slate-500 mt-0.5">
                Register and manage physical branch offices for {{ $bank->bank_name }} across state districts.
            </p>
        </div>

        <div class="flex items-center space-x-2">
            <span class="text-xs font-semibold px-3 py-1.5 bg-slate-100 rounded-xl text-slate-700 border border-slate-200">
                Total Branches: <span class="text-orange-600">{{ $branches->total() }}</span>
            </span>
        </div>
    </div>

    <!-- Create Branch Form Card -->
    <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm shadow-slate-100/60">
        <div class="mb-5">
            <h3 class="text-sm font-bold text-slate-900">Add new branch</h3>
            <p class="text-xs text-slate-400 mt-0.5">Define a physical or logical branch unit with dedicated IFSC code.</p>
        </div>

        <form method="POST" action="{{ route('branches.store') }}">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
                <!-- Branch Name -->
                <div>
                    <label class="block font-medium text-slate-600 mb-1.5">Branch name</label>
                    <input type="text" 
                           name="branch_name" 
                           value="{{ old('branch_name') }}"
                           required 
                           placeholder="e.g. Jorhat Town Branch" 
                           class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                </div>

                <!-- District -->
                <div>
                    <label class="block font-medium text-slate-600 mb-1.5">District</label>
                    <input type="text" 
                           name="district" 
                           value="{{ old('district') }}"
                           required 
                           placeholder="e.g. Jorhat, Kamrup, Cachar" 
                           class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                </div>

                <!-- IFSC code -->
                <div>
                    <label class="block font-medium text-slate-600 mb-1.5">IFSC code</label>
                    <input type="text" 
                           name="ifsc_code" 
                           value="{{ old('ifsc_code') }}"
                           required 
                           placeholder="e.g. AGB0001064" 
                           class="w-full font-mono uppercase rounded-xl border border-slate-200 px-3.5 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                </div>

                <!-- Branch code -->
                <div>
                    <label class="block font-medium text-slate-600 mb-1.5">Branch code</label>
                    <input type="text" 
                           name="branch_code" 
                           value="{{ old('branch_code') }}"
                           required 
                           placeholder="e.g. BR005" 
                           class="w-full font-mono uppercase rounded-xl border border-slate-200 px-3.5 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                </div>

                <!-- Branch Address -->
                <div class="md:col-span-2">
                    <label class="block font-medium text-slate-600 mb-1.5">Branch address</label>
                    <input type="text" 
                           name="address" 
                           value="{{ old('address') }}"
                           placeholder="Street, locality, town" 
                           class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                </div>

                <!-- Contact phone -->
                <div>
                    <label class="block font-medium text-slate-600 mb-1.5">Contact phone / landline</label>
                    <input type="text" 
                           name="contact_phone" 
                           value="{{ old('contact_phone') }}"
                           placeholder="e.g. 0361-2456789" 
                           class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                </div>

                <!-- Contact email -->
                <div>
                    <label class="block font-medium text-slate-600 mb-1.5">Branch official email</label>
                    <input type="email" 
                           name="contact_email" 
                           value="{{ old('contact_email') }}"
                           placeholder="e.g. branch.jorhat@agb.co.in" 
                           class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                </div>
            </div>

            <div class="mt-5 flex justify-end">
                <button type="submit" 
                        class="px-5 py-2.5 rounded-xl text-xs font-semibold text-white bg-[#f95721] hover:bg-[#ea580c] transition shadow-md shadow-orange-500/20">
                    + Add Branch
                </button>
            </div>
        </form>
    </div>

    <!-- All Branches Table Card -->
    <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm shadow-slate-100/60">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
            <h3 class="text-sm font-bold text-slate-900">All registered branches</h3>

            <!-- Search -->
            <form method="GET" action="{{ route('branches.index') }}" class="flex items-center space-x-2">
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="Search branch, IFSC, district..." 
                       class="text-xs rounded-xl border border-slate-200 px-3 py-1.5 w-60 focus:outline-none focus:border-orange-500">
                <button type="submit" class="px-3 py-1.5 bg-slate-100 text-slate-700 hover:bg-slate-200 text-xs font-medium rounded-xl transition">
                    Search
                </button>
                @if(request('search'))
                    <a href="{{ route('branches.index') }}" class="text-xs text-slate-400 hover:text-slate-600">Clear</a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-100 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">
                        <th class="py-3 px-4">CODE</th>
                        <th class="py-3 px-4">BRANCH NAME</th>
                        <th class="py-3 px-4">DISTRICT</th>
                        <th class="py-3 px-4">IFSC</th>
                        <th class="py-3 px-4 text-center">OFFICERS</th>
                        <th class="py-3 px-4 text-center">DEEDS</th>
                        <th class="py-3 px-4 text-center">STATUS</th>
                        <th class="py-3 px-4 text-right">ACTIONS</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 text-slate-700">
                    @forelse($branches as $b)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3.5 px-4 font-mono font-bold text-slate-900">{{ $b->branch_code }}</td>
                            <td class="py-3.5 px-4 font-semibold text-slate-900">
                                <div>{{ $b->branch_name }}</div>
                                <div class="text-[11px] font-normal text-slate-400 truncate max-w-xs">{{ $b->address }}</div>
                            </td>
                            <td class="py-3.5 px-4 text-slate-600">{{ $b->district }}</td>
                            <td class="py-3.5 px-4 font-mono text-slate-600">{{ $b->ifsc_code }}</td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-700">
                                    {{ $b->branch_users_count }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-orange-50 text-orange-700">
                                    {{ $b->deeds_count }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium {{ $b->status === 'Active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $b->status }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right space-x-2">
                                <form method="POST" action="{{ route('branches.toggle', $b->id) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-[11px] font-medium text-slate-500 hover:text-slate-900 underline">
                                        {{ $b->status === 'Active' ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-400">
                                No branches found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $branches->links() }}
        </div>
    </div>

</div>
@endsection
