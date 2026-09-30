<div id="dag-{{ $dag['id'] }}" class="dag-content {{ $isFirst ? '' : 'hidden' }} p-6">

    {{-- Valuation Banner --}}
    <div class="bg-gradient-to-r from-orange-50 to-white rounded-xl border border-orange-100 p-6 flex flex-col md:flex-row items-center justify-between gap-4 mb-8"
        data-purpose="valuation-banner">
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
                    Area, zonal rate, valuation, and boundary information for the selected property.
                </p>
            </div>
        </div>
        @if($showRemoveButton)
            <button data-dag-id="{{ $dag['id'] }}"
                class="dag-remove-btn bg-primary hover:bg-orange-600 text-white px-12 py-3 rounded-lg font-semibold text-sm shadow-lg shadow-orange-200 transition transform hover:-translate-y-0.5 active:scale-95">
                Remove
            </button>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        {{-- Land Schedule --}}
        <div class="lg:col-span-2" data-purpose="land-schedule-container">
            <div class="bg-slate-50 rounded-xl p-6 border border-slate-100">
                <div class="flex items-center gap-3 mb-6">
                    <i class="fa-solid fa-clipboard-list text-primary"></i>
                    <div>
                        <h4 class="font-semibold text-lg">Land Schedule</h4>
                        <p class="text-xs text-gray-400">Provide or verify the details to continue</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                    <x-number-input label="Land area in Bigha" :name="'area-bigha-' . $dag['id']" required="true"
                        :id="'area-bigha-' . $dag['id']" class="bg-white" placeholder="Area in Bigha"
                        :value="$dag['area_bigha'] ?? ''" :readonly="true" />
                    <x-number-input label="Land area in Katha" :name="'area-kattha-' . $dag['id']" required="true"
                        :id="'area-kattha-' . $dag['id']" class="bg-white" placeholder="Area in Katha"
                        :value="$dag['area_kattha'] ?? ''" :readonly="true" />
                    <x-number-input label="Land area in Lessa" :name="'area-lessa-' . $dag['id']" required="true"
                        :id="'area-lessa-' . $dag['id']" class="bg-white" placeholder="Area in Lessa"
                        :value="$dag['area_lessa'] ?? ''" :readonly="true" />
                    <x-select-input label="Patta Type" required="true" :name="'patta-type-' . $dag['id']"
                        :id="'patta-type-' . $dag['id']" :options="[
        $dag['patta_type_id'] => $dag['patta_type_desc']
    ]"
          :selected="$dag['patta_type_id']" />
                    <!-- <x-text-input label="Patta Type" required="true" name="patta_type_test" id="patta_type_test"
                        value="Kheraj Miyadi" class="bg-white" :readonly="true" /> -->

                </div>
            </div>
        </div>

        {{-- Valuation Summary --}}
        <div class="lg:col-span-1" data-purpose="valuation-summary">
            <div class="bg-white rounded-xl border-2 border-orange-100 overflow-hidden h-full">
                <div class="bg-orange-50 px-5 py-3 border-b border-orange-100">
                    <span class="text-xs font-semibold text-ngdrs-orange uppercase">Your Land Valuation</span>
                </div>

                <div class="p-5 space-y-4">

                    <div class="flex justify-between items-center">
                        <span class="text-xs text-gray-500 font-medium">Zonal Value Rate</span>
                        <div class="text-right">
                            <span class="font-semibold text-sm">
                                ₹{{ number_format($dag['zonal_rate']) }}
                            </span>
                            <p class="text-[10px] text-gray-400 uppercase">per bigha</p>

                            <input type="hidden" name="zonal_rate" value="{{ $dag['zonal_rate'] }}">
                        </div>
                    </div>

                    <div class="flex justify-between items-center py-3 border-y border-gray-50">
                        <span class="text-xs text-gray-500 font-medium">Property Valuation</span>
                        <span class="text-sm">
                            ₹{{ number_format($dag['land_valuation']) }}
                        </span>

                        <input type="hidden" name="land_valuation" value="{{ $dag['land_valuation'] }}">
                    </div>

                    <div class="flex justify-between items-center py-1">
                        <span class="text-xs text-gray-500 font-medium">Zone Name</span>
                        <span class="text-sm">{{ $dag['zone_name'] }}</span>

                        <input type="hidden" name="zone_name" value="{{ $dag['zone_name'] }}">
                    </div>

                    <div class="flex justify-between items-center py-1">
                        <span class="text-xs text-gray-500 font-medium">Zonal Class</span>
                        <span class="text-sm">{{ $dag['zone_class'] }}</span>

                        <input type="hidden" name="zone_class" value="{{ $dag['zone_class'] }}">
                    </div>

                    <div class="flex justify-between items-center py-3 border-t border-gray-50">
                        <span class="text-xs text-gray-500 font-medium">Dharitree Class</span>
                        <span class="text-sm">{{ $dag['dharitree_class'] }}</span>

                        <input type="hidden" name="dharitree_class" value="{{ $dag['dharitree_class'] }}">
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- Boundary Details --}}
    <div class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm" data-purpose="boundary-details">
        <div class="flex items-center gap-3 mb-6">
            <div class="bg-zinc-100 p-2 rounded-lg">
                <i class="fa-solid fa-expand text-zinc-600"></i>
            </div>
            <h4 class="font-semibold text-lg">Boundary Details</h4>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6 mb-6">
            <x-text-input label="Boundary East" :name="'boundary-east-' . $dag['id']" placeholder="Boundary East"
                class="bg-white" required="true" :id="'boundary-east-' . $dag['id']" :value="$dag['boundary_east'] ?? ''" />
            <x-text-input label="Boundary West" :name="'boundary-west-' . $dag['id']" placeholder="Boundary West"
                class="bg-white" required="true" :id="'boundary-west-' . $dag['id']" :value="$dag['boundary_west'] ?? ''" />
            <x-text-input label="Boundary South" :name="'boundary-south-' . $dag['id']" placeholder="Boundary South"
                class="bg-white" required="true" :id="'boundary-south-' . $dag['id']" :value="$dag['boundary_south'] ?? ''" />
            <x-text-input label="Boundary North" :name="'boundary-north-' . $dag['id']" placeholder="Boundary North"
                class="bg-white" required="true" :id="'boundary-north-' . $dag['id']" :value="$dag['boundary_north'] ?? ''" />
        </div>
        <!-- <x-text-input label="Additional Information" :name="'additional-info-' . $dag['id']"
            placeholder="Any Additional Information" class="bg-white" :value="$dag['additional_info'] ?? ''" /> -->
    </div>
</div>