<x-layout-app>

    <x-banner title="Property Details" description="This page displays all property details linked to your application, including ownership,
    location, and relevant records for verification and reference." image="images/icons/property/prop-banner.png" />
<x-progress-steps />
    <div class="max-w-7xl mx-auto bg-white rounded-md shadow-sm border border-gray-100 p-8">
        <div class="max-w-5xl mx-auto">
            <x-prereg preRegNo="20268928487" />

            <x-section-divider />

            <x-section-title title="NOC Details" icon="images/icons/property/Vector.svg" />

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

            <div id="noc-container" class="collapsible "></div>

            <x-section-divider />

            <x-section-title title="Property Details" icon="images/icons/property/Vector2.svg" />

            <div class="grid grid-cols-3 gap-x-12 gap-y-4 mb-12">
                <x-select-input label="District" name="district" :required="true" id="district-input"
                    :options="$district" />
                <x-select-input label="Subdivision" name="subdivision" :required="true" id="subdivision-input" />
                <x-select-input label="Circle" name="circle" :required="true" id="circle-input" />
                <x-select-input label="Mouja" name="mouja" :required="true" id="mouja-input" />
                <x-select-input label="Village" name="village" required="true" id="village-input" />
                <x-select-input label="Area Type" name="area-type" :required="true" id="area-type-input" />
                <div id="dag-patta-other-container" class="collapsible col-span-3"></div>
                <!-- <x-number-input label="Dag Number" name="dag-number" required="true" placeholder="Dag Number"
                id="dag-no" wrapperClass="col-span-2 mb-3" />
                <x-ui.button type="button" label="Verify Dag" id="verify-dag" wrapperClass="self-center mt-3" />
                <x-number-input label="Patta Number" name="patta-number" required="true" placeholder="Patta Number"
                wrapperClass="col-span-2" /> -->
            </div>

            <div class="flex items-center justify-between bg-[#F5F7FA] p-6 rounded-md mb-8">
                <div class="flex items-center gap-6">
                    <img src="{{ asset('images/icons/property/Vector3.svg') }}" alt="">
                    <h2 class="font-semibold">Calculate Land Valuation</h2>
                </div>
                <div>
                    <x-ui.button id="fetch-property-valuation" label="Click here" type="button"
                        class="w-sm flex justify-center items-center" />
                </div>
            </div>

            <div class="bg-[#F5F7FA] p-6 rounded-md mb-8">

                <div class="flex gap-6">
                    <div class="flex-3 p-3">
                        <div class="mb-6">
                            <div class="flex items-center gap-4 mb-2">
                                <img src="{{ asset('images/icons/property/Vector3.svg') }}" alt="">
                                <h2 class="font-semibold text-lg">Land Schedule</h2>
                            </div>
                            <p class="text-sm text-grey">Provide or verify the details to continue</p>
                        </div>
                        <div class="grid grid-cols-2 gap-x-8 gap-y-4">
                            <x-number-input label="Land area in Bigha" name="area-bigha" required="true" id="area-bigha"
                                class="bg-white" placeholder="Area in Bigha" />
                            <x-number-input label="Land area in Katha" name="area-kattha" required="true" id="area-kattha"
                                class="bg-white" placeholder="Area in Katha" />
                            <x-number-input label="Land area in Lessa" name="area-lessa" required="true" id="area-lessa"
                                class="bg-white" placeholder="Area in Lessa" />
                            <x-select-input label="Patta Type" required="true" name="patta-type" id="patta-type"/>
                        </div>
                    </div>
                    <div class="flex-2">
                        <div class="bg-white rounded-md">
                            <h3 class="p-3 text-md text-primary">Your Land Valuation</h3>
                            <hr class="border border-[#F5F7FA]">
                            <div class="p-4">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="text-sm font-semibold">Zonal Value Rate</p>
                                    </div>
                                    <div class="text-sm">
                                        <p class="font-semibold text-sm">₹200000</p>
                                        <p class="text-xs">per bigha</p>
                                    </div>
                                </div>
                                <hr class="border border-[#F5F7FA] my-4">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="text-sm">Zone Name</p>
                                    </div>
                                    <div class="text-sm">
                                        <p class=" text-sm">Zone II</p>
                                    </div>
                                </div>
                                <hr class="border border-[#F5F7FA] my-4">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="text-sm">Zone Class</p>
                                    </div>
                                    <div class="text-sm">
                                        <p class=" text-sm">Agri I</p>
                                    </div>
                                </div>
                                <hr class="border border-[#F5F7FA] my-4">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="text-sm">Dharitree Class</p>
                                    </div>
                                    <div class="text-sm">
                                        <p class=" text-sm">Kheraj Miyadi</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <x-section-divider :dashed="false" />

                <div class="px-4">
                    <x-section-title title="Boundary and Other Details" icon="images/icons/property/boundary.svg" />

                    <div class="grid grid-cols-2 gap-x-12 gap-y-6 mb-4">
                        <x-text-input label="Boundary East" name="boundary-east" placeholder="Boundary East"
                            class="bg-white" required="true" id="boundary-east" />
                        <x-text-input label="Boundary West" name="boundary-west" placeholder="Boundary West"
                            class="bg-white" required="true" id="boundary-west" />
                        <x-text-input label="Boundary South" name="boundary-south" placeholder="Boundary South"
                            class="bg-white" required="true" id="boundary-south" />
                        <x-text-input label="Boundary North" name="boundary-north" placeholder="Boundary North"
                            class="bg-white" required="true" id="boundary-north" />
                    </div>

                    <x-text-input label="Additional Information" name="additional-info"
                        placeholder="Any Additional Information" class="bg-white" />
                </div>
            </div>

            <x-ui.button label="Save Details" wrapperClass="flex justify-end"
                class="w-sm flex justify-center items-center" />
        </div>
    </div>

    @push('page-scripts')
        @vite('resources/js/pages/property/main.ts')
    @endpush

</x-layout-app>