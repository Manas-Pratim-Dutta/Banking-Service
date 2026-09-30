<x-layout-app>

    <x-progress-steps />
    <div class="card">
        <div class="max-w-5xl mx-auto">
            <x-prereg :preRegNo="$tokenNo" />

            <x-section-divider />

            <x-section-title title="Property Classification" icon="images/icons/property/Vector.svg" />

            <div class="flex justify-between gap-12 mt-4">
                <div class="w-full">
                    <x-select-input label="Select Area" name="area-type" :required="true" :options="$areaType"
                        id="area-type-id" />
                </div>
                <div class="w-full">
                    <x-select-input label="Property Type" name="property-type" :required="true" :options="$propertyType"
                        id="property-type-id" />
                </div>
            </div>

        </div>
    </div>
    <div id="noc-card" class="card hidden">
        <x-section-title title="Additional Information" icon="images/icons/property/Vector.svg" />
        <div id="noc-container"></div>
    </div>
    <div class="card hidden" id="property-details">
        <x-section-title title="Property Details" icon="images/icons/property/Vector2.svg" />

        <div class="grid grid-cols-3 gap-x-12 gap-y-4 mb-4">
            <x-select-input label="District" name="district" :required="true" id="district-input"
                :options="$district" />
            <x-select-input label="Subdivision" name="subdivision" :required="true" id="subdivision-input" />
            <x-select-input label="Circle" name="circle" :required="true" id="circle-input" />
            <x-select-input label="Mouja" name="mouja" :required="true" id="mouja-input" />
            <x-select-input label="Village" name="village" required="true" id="village-input" />
            <x-select-input label="Area Type" name="area-type" :required="true" id="area-type-input" />
            <x-text-input id="police_station" label="Police Station" name="police_station"
                placeholder="Enter Police Station" value="" :required="true" />

            <x-text-input id="post_office" label="Post Office" name="post_office" placeholder="Enter Post Office"
                value="" :required="true" />

            <x-text-input id="pin_code" label="PIN Code" name="pin_code" placeholder="Enter PIN Code" value=""
                :required="true" />

            <x-text-input id="description_of_property" label="Description of the Property"
                name="description_of_property" placeholder="Enter Description of the Property" value=""
                :required="true" />
        </div>

        <div class="hidden flex gap-4" id="dag-patta-container">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Dag
                    <span class="text-red-500">*</span>
                </label>
                <div class="relative w-full max-w-sm">
                    <input type="number" placeholder="Dag No"
                        class="w-full text-sm px-4 py-2.5 text-gray-800 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent placeholder-gray-500"
                        id="dag-no-input" />
                    <button type="submit"
                        class="hidden absolute top-1/2 right-1.5 -translate-y-1/2 px-4 py-1.5 bg-neutral-900 hover:bg-neutral-800 text-white text-sm rounded-md transition-colors duration-200 cursor-pointer"
                        id="verifyDagBtn">
                        Verify
                    </button>
                </div>
            </div>
            <div class="flex-1">
                <x-number-input label="Patta No" name="patta-no" placeholder="Patta No" :required="true"
                    id="patta-input" />
            </div>
        </div>
        <div class="mt-6">
            <x-ui.button label="Add Property" wrapperClass="flex justify-end "
                class="w-xs flex justify-center items-center" id="add-property-btn" />
        </div>

    </div>
    <div id="dag-patta-other-card" class="card hidden">
        <x-section-title title="Property Details" icon="images/icons/property/Vector.svg" />
        <div id="dag-patta-other-container"></div>
    </div>

    <!-- PROPERTY DETAILS CONTAINER -->
    <div class="bg-white rounded-md hidden mb-4" id="dagSection">
        <!-- Floating Tabs -->
        <div class="inline-flex gap-3 p-6 pb-0 rounded-2xl w-full">
            <div>
                <div class="flex flex-wrap gap-2" id="dagTabs">
                    {{-- Tab buttons injected here by TS --}}
                </div>
                <div class="relative mt-3 h-0.5 bg-gray-200 rounded-full">
                    <div id="dagUnderlineBar" class="absolute top-0 h-full rounded-full transition-all duration-200"
                        style="background: #ff803b;"></div>
                </div>
            </div>
        </div>

        {{-- DAG content panels injected here by TS --}}
        <div id="dagContents">
        </div>
    </div>
    <div class="hidden" id="property-detail-container">
        <div class="flex flex-col gap-5   mb-4">

            <!-- Card 1 -->
            <div
                class="relative w-full bg-white rounded-2xl border border-orange-100 shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 overflow-hidden">
                <div class="absolute top-0 left-0 h-full w-1.5 bg-gradient-to-b from-[#ff6900] to-orange-400"></div>

                <div class="flex flex-col md:flex-row">

                    <!-- Locality -->
                    <div
                        class="md:w-1/4 p-5 pl-6 flex flex-col justify-center gap-2.5 border-b md:border-b-0 md:border-r border-dashed border-orange-100">
                        <div class="flex items-center gap-2 mb-1">
                            <svg class="w-4 h-4 text-[#ff6900]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="text-xs font-semibold text-[#ff6900] uppercase tracking-wider">Location</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">District</span>
                            <span class="font-semibold text-gray-800">Kamrup Metro</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">Circle</span>
                            <span class="font-semibold text-gray-800">Dispur</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">Mouja</span>
                            <span class="font-semibold text-gray-800">Beltala</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">Village</span>
                            <span class="font-semibold text-gray-800">Jyotikuchi</span>
                        </div>
                    </div>

                    <!-- Dag / Patta strip -->
                    <div
                        class="md:w-[15%] flex md:flex-col items-center justify-center gap-4 md:gap-4 p-5 border-b md:border-b-0 md:border-r border-dashed border-orange-100 bg-gradient-to-br from-orange-50/60 to-transparent">
                        <div
                            class="text-center px-3 py-2 rounded-xl bg-white shadow-sm border border-orange-100 w-full">
                            <div class="text-[10px] uppercase tracking-wide text-gray-400 font-medium">Dag No.</div>
                            <div class="text-xl font-extrabold text-[#ff6900]">151</div>
                        </div>
                        <div
                            class="text-center px-3 py-2 rounded-xl bg-white shadow-sm border border-orange-100 w-full">
                            <div class="text-[10px] uppercase tracking-wide text-gray-400 font-medium">Patta No.</div>
                            <div class="text-xl font-extrabold text-[#ff6900]">967</div>
                        </div>
                    </div>

                    <!-- Body: area + patta type -->
                    <div
                        class="md:w-1/4 p-5 flex flex-col justify-center gap-3 border-b md:border-b-0 md:border-r border-dashed border-orange-100">
                        <div>
                            <div class="text-xs text-gray-400 mb-0.5">Total area</div>
                            <div class="font-semibold text-gray-800">0 Bigha 0 Katha 11.81 Lessa</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-400 mb-0.5">Patta type</div>
                            <span
                                class="inline-block px-2.5 py-1 rounded-full bg-orange-50 text-[#ff6900] text-xs font-semibold border border-orange-100">
                                Kheraj Patta (KP)
                            </span>
                        </div>
                    </div>

                    <!-- Valuation -->
                    <div
                        class="md:w-[30%] p-5 flex flex-col justify-center gap-2 bg-gradient-to-br from-[#fff4ec] to-white">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-500">Zonal value rate</span>
                            <span class="font-medium text-gray-700">₹ 23,90,010</span>
                        </div>
                        <div class="h-px bg-orange-100 my-1"></div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 font-medium text-sm">Property valuation</span>
                            <span class="font-extrabold text-[#ff6900] text-lg">₹ 2,82,259</span>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Card 2 -->
            <div
                class="relative w-full bg-white rounded-2xl border border-orange-100 shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 overflow-hidden">
                <div class="absolute top-0 left-0 h-full w-1.5 bg-gradient-to-b from-[#ff6900] to-orange-400"></div>

                <div class="flex flex-col md:flex-row">

                    <!-- Locality -->
                    <div
                        class="md:w-1/4 p-5 pl-6 flex flex-col justify-center gap-2.5 border-b md:border-b-0 md:border-r border-dashed border-orange-100">
                        <div class="flex items-center gap-2 mb-1">
                            <svg class="w-4 h-4 text-[#ff6900]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="text-xs font-semibold text-[#ff6900] uppercase tracking-wider">Location</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">District</span>
                            <span class="font-semibold text-gray-800">Kamrup Metro</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">Circle</span>
                            <span class="font-semibold text-gray-800">Dispur</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">Mouja</span>
                            <span class="font-semibold text-gray-800">Beltala</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">Village</span>
                            <span class="font-semibold text-gray-800">Jyotikuchi</span>
                        </div>
                    </div>

                    <!-- Dag / Patta strip -->
                    <div
                        class="md:w-[15%] flex md:flex-col items-center justify-center gap-4 md:gap-4 p-5 border-b md:border-b-0 md:border-r border-dashed border-orange-100 bg-gradient-to-br from-orange-50/60 to-transparent">
                        <div
                            class="text-center px-3 py-2 rounded-xl bg-white shadow-sm border border-orange-100 w-full">
                            <div class="text-[10px] uppercase tracking-wide text-gray-400 font-medium">Dag No.</div>
                            <div class="text-xl font-extrabold text-[#ff6900]">152</div>
                        </div>
                        <div
                            class="text-center px-3 py-2 rounded-xl bg-white shadow-sm border border-orange-100 w-full">
                            <div class="text-[10px] uppercase tracking-wide text-gray-400 font-medium">Patta No.</div>
                            <div class="text-xl font-extrabold text-[#ff6900]">967</div>
                        </div>
                    </div>

                    <!-- Body: area + patta type -->
                    <div
                        class="md:w-1/4 p-5 flex flex-col justify-center gap-3 border-b md:border-b-0 md:border-r border-dashed border-orange-100">
                        <div>
                            <div class="text-xs text-gray-400 mb-0.5">Total area</div>
                            <div class="font-semibold text-gray-800">0 Bigha 0 Katha 8.50 Lessa</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-400 mb-0.5">Patta type</div>
                            <span
                                class="inline-block px-2.5 py-1 rounded-full bg-orange-50 text-[#ff6900] text-xs font-semibold border border-orange-100">
                                Kheraj Patta (KP)
                            </span>
                        </div>
                    </div>

                    <!-- Valuation -->
                    <div
                        class="md:w-[30%] p-5 flex flex-col justify-center gap-2 bg-gradient-to-br from-[#fff4ec] to-white">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-500">Zonal value rate</span>
                            <span class="font-medium text-gray-700">₹ 23,90,010</span>
                        </div>
                        <div class="h-px bg-orange-100 my-1"></div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 font-medium text-sm">Property valuation</span>
                            <span class="font-extrabold text-[#ff6900] text-lg">₹ 2,03,150</span>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
    <x-ui.button label="Save Details" wrapperClass="flex justify-end" class="w-sm flex justify-center items-center"
        id="save-property-btn" />

    @push('page-scripts')
        @vite('resources/js/pages/property/main.ts')
        <script type="application/json" id="property-page-data">
                                                                                            @json([
                                                                                                'nocFlag' => $nocFlag,
                                                                                                'nocNo' => $nocNo,
                                                                                            ])
                                                                                        </script>
    @endpush

</x-layout-app>