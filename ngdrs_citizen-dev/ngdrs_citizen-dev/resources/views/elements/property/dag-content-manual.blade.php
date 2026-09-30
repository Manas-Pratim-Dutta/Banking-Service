@php
    use App\Utils\Helpers;
@endphp

<div id="dag-{{ $dag['dag_no'] }}" class="dag-content p-6">

    {{-- Valuation Banner --}}
    <div
        class="bg-gradient-to-r from-orange-50 to-white rounded-xl border border-orange-100 p-6 flex flex-col md:flex-row items-center justify-between gap-4 mb-8">

        <div class="flex items-center gap-4">
            <div class="bg-white p-3 rounded-lg shadow-sm border border-orange-100">
                <i class="fa-solid fa-calculator text-2xl text-ngdrs-orange"></i>
            </div>

            <div>
                <h4 class="text-lg font-bold text-gray-800">
                    Dag {{ $dag['dag_no'] }} • Patta {{ $dag['patta_no'] }}
                </h4>

                <input type="hidden" name="dag_no[]" value="{{ $dag['dag_no'] }}">
                <input type="hidden" name="patta_no[]" value="{{ $dag['patta_no'] }}">

                <p class="text-sm text-gray-500">
                    Enter the property details to continue.
                </p>
            </div>
        </div>

        @if($showRemoveButton)
            <button
                type="button"
                data-dag-id="{{ $dag['dag_no'] }}"
                class="dag-remove-btn bg-primary hover:bg-orange-600 text-white px-12 py-3 rounded-lg font-semibold text-sm shadow-lg shadow-orange-200 transition transform hover:-translate-y-0.5 active:scale-95"
            >
                Remove
            </button>
        @endif
    </div>


    {{-- Land Schedule --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">

        <div class="lg:col-span-2">

            <div class="bg-slate-50 rounded-xl p-6 border border-slate-100">

                <div class="flex items-center gap-3 mb-6">
                    <i class="fa-solid fa-clipboard-list text-primary"></i>

                    <div>
                        <h4 class="font-semibold text-lg">
                            Land Schedule
                        </h4>

                        <p class="text-xs text-gray-400">
                            Provide the property details
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">

                    {{-- USER ENTERS --}}
                    <x-number-input
                        label="Land area in Bigha"
                        :name="'area-bigha-' . $dag['dag_no']"
                        :id="'area-bigha-' . $dag['dag_no']"
                        required="true"
                        class="bg-white"
                        placeholder="Area in Bigha"
                    />

                    {{-- USER ENTERS --}}
                    <x-number-input
                        label="Land area in Katha"
                        :name="'area-kattha-' . $dag['dag_no']"
                        :id="'area-kattha-' . $dag['dag_no']"
                        required="true"
                        class="bg-white"
                        placeholder="Area in Katha"
                    />

                    {{-- USER ENTERS --}}
                    <x-number-input
                        label="Land area in Lessa"
                        :name="'area-lessa-' . $dag['dag_no']"
                        :id="'area-lessa-' . $dag['dag_no']"
                        required="true"
                        class="bg-white"
                        placeholder="Area in Lessa"
                    />

                    {{-- USER ENTERS --}}
                    <x-select-input
                        label="Patta Type"
                        required="true"
                        :name="'patta-type-' . $dag['dag_no']"
                        :id="'patta-type-' . $dag['dag_no']"
                        :options="$pattaTypes"
                    />

                </div>
            </div>
        </div>


        {{-- Valuation Summary --}}
        <div class="lg:col-span-1">

            <div class="bg-white rounded-xl border-2 border-orange-100 overflow-hidden h-full">

                <div class="bg-orange-50 px-5 py-3 border-b border-orange-100">
                    <span class="text-xs font-semibold text-ngdrs-orange uppercase">
                        Your Land Valuation
                    </span>
                </div>

                <div class="p-5 space-y-4">

                    {{-- BACKEND --}}
                    <div class="flex justify-between items-center">

                        <span class="text-xs text-gray-500 font-medium">
                            Zonal Value Rate
                        </span>

                        <div class="text-right">

                            <span class="font-semibold text-sm">
                                ₹{{ Helpers::formatIndianNumber($dag['zonal_rate']) }}
                            </span>

                            <p class="text-[10px] text-gray-400 uppercase">
                                per bigha
                            </p>

                            <input type="hidden" name="zonal_rate" value="{{ $dag['zonal_rate'] }}">

                        </div>
                    </div>


                    @if(isset($dag['property_valuation']))

                        <div class="flex justify-between items-center py-3 border-y border-gray-50">

                            <span class="text-xs text-gray-500 font-medium">
                                Property Valuation
                            </span>

                            <span class="text-sm">
                                ₹{{ Helpers::formatIndianNumber($dag['property_valuation']) }}
                            </span>

                            <input type="hidden" name="land_valuation" value="{{ $dag['land_valuation'] }}">

                        </div>

                    @endif


                    {{-- BACKEND --}}
                    <div class="flex justify-between items-center py-1">

                        <span class="text-xs text-gray-500 font-medium">
                            Zone Name
                        </span>

                        <span class="text-sm">
                            {{ $dag['zone_name'] }}
                        </span>

                         <input type="hidden" name="zone_name" value="{{ $dag['zone_name'] }}">

                    </div>


                    {{-- BACKEND --}}
                    <div class="flex justify-between items-center py-1">

                        <span class="text-xs text-gray-500 font-medium">
                            Zonal Class
                        </span>

                        <span class="text-sm">
                            {{ $dag['zone_class'] }}
                        </span>

                        <input type="hidden" name="zone_class" value="{{ $dag['zone_class'] }}">

                    </div>


                    {{-- BACKEND --}}
                    <div class="flex justify-between items-center py-3 border-t border-gray-50">

                        <span class="text-xs text-gray-500 font-medium">
                            Dharitree Class
                        </span>

                        <span class="text-sm">
                            {{ $dag['dharitree_class'] }}
                        </span>

                                                <input type="hidden" name="dharitree_class" value="{{ $dag['dharitree_class'] }}">


                    </div>

                </div>
            </div>
        </div>

    </div>


    {{-- Boundary Details --}}
    <div class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm">

        <div class="flex items-center gap-3 mb-6">

            <div class="bg-zinc-100 p-2 rounded-lg">
                <i class="fa-solid fa-expand text-zinc-600"></i>
            </div>

            <h4 class="font-semibold text-lg">
                Boundary Details
            </h4>

        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6 mb-6">

            {{-- USER ENTERS --}}
            <x-text-input
                label="Boundary East"
                :name="'boundary-east-' . $dag['dag_no']"
                :id="'boundary-east-' . $dag['dag_no']"
                placeholder="Boundary East"
                class="bg-white"
                required="true"
            />

            {{-- USER ENTERS --}}
            <x-text-input
                label="Boundary West"
                :name="'boundary-west-' . $dag['dag_no']"
                :id="'boundary-west-' . $dag['dag_no']"
                placeholder="Boundary West"
                class="bg-white"
                required="true"
            />

            {{-- USER ENTERS --}}
            <x-text-input
                label="Boundary South"
                :name="'boundary-south-' . $dag['dag_no']"
                :id="'boundary-south-' . $dag['dag_no']"
                placeholder="Boundary South"
                class="bg-white"
                required="true"
            />

            {{-- USER ENTERS --}}
            <x-text-input
                label="Boundary North"
                :name="'boundary-north-' . $dag['dag_no']"
                :id="'boundary-north-' . $dag['dag_no']"
                placeholder="Boundary North"
                class="bg-white"
                required="true"
            />

        </div>

    </div>

</div>