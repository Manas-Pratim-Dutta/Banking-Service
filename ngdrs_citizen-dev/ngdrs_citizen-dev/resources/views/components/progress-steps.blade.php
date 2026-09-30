<div id="ngdrs-stepper" data-active="0"
    class="w-full bg-white border border-orange-100 rounded-xl shadow-sm px-5 py-3.5 select-none mb-4">
    <!-- Card Header Indicator -->
    <div class="flex items-center justify-between mb-3">
        <div class="flex items-center gap-1.5">
        </div>
        <span id="ngdrs-step-count"
            class="text-[10px] font-semibold text-orange-600 bg-orange-50 border border-orange-200/80 px-2 py-0.5 rounded-full">
            Step 1 of 9
        </span>
    </div>

    <!-- Step Track -->
    <div class="relative"> <!-- Connecting Track -->
        <div class="absolute top-[18px] left-[6%] right-[6%] h-[2px] bg-orange-100"> <!-- Filled Progress -->
            <div id="ngdrs-progress-line" class="h-full bg-orange-500 transition-all duration-500 ease-out rounded-full"
                style="width: 0%"> </div>
        </div> <!-- Steps -->
        <div class="relative z-10 flex items-start justify-between"> <!-- 1. General Info --> <a href="{{ route('general-info') }}"
                class="ngdrs-step group flex flex-col items-center flex-1 min-w-0" data-index="0">
                <div
                    class="step-circle w-9 h-9 rounded-full flex items-center justify-center border-2 border-orange-200 bg-white text-orange-300 transition-all duration-300 group-hover:border-orange-400 group-hover:text-orange-500 group-hover:shadow-sm">
                    <svg class="w-[15px] h-[15px]" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div> <span
                    class="step-label mt-2 text-[10px] font-semibold text-gray-400 text-center leading-tight whitespace-nowrap transition-colors duration-200 group-hover:text-orange-500">
                    General Info </span>
            </a> <!-- 2. Property --> <a href="{{ route('property') }}"
                class="ngdrs-step group flex flex-col items-center flex-1 min-w-0" data-index="1">
                <div
                    class="step-circle w-9 h-9 rounded-full flex items-center justify-center border-2 border-orange-200 bg-white text-orange-300 transition-all duration-300 group-hover:border-orange-400 group-hover:text-orange-500 group-hover:shadow-sm">
                    <svg class="w-[15px] h-[15px]" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                </div> <span
                    class="step-label mt-2 text-[10px] font-semibold text-gray-400 text-center leading-tight whitespace-nowrap transition-colors duration-200 group-hover:text-orange-500">
                    Property </span>
            </a> <!-- 3. Party --> <a href="{{ route('party') }}" class="ngdrs-step group flex flex-col items-center flex-1 min-w-0"
                data-index="2">
                <div
                    class="step-circle w-9 h-9 rounded-full flex items-center justify-center border-2 border-orange-200 bg-white text-orange-300 transition-all duration-300 group-hover:border-orange-400 group-hover:text-orange-500 group-hover:shadow-sm">
                    <svg class="w-[15px] h-[15px]" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div> <span
                    class="step-label mt-2 text-[10px] font-semibold text-gray-400 text-center leading-tight whitespace-nowrap transition-colors duration-200 group-hover:text-orange-500">
                    Party </span>
            </a> <!-- 4. Witness --> <a href="{{ route('witness') }}"
                class="ngdrs-step group flex flex-col items-center flex-1 min-w-0" data-index="3">
                <div
                    class="step-circle w-9 h-9 rounded-full flex items-center justify-center border-2 border-orange-200 bg-white text-orange-300 transition-all duration-300 group-hover:border-orange-400 group-hover:text-orange-500 group-hover:shadow-sm">
                    <svg class="w-[15px] h-[15px]" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </div> <span
                    class="step-label mt-2 text-[10px] font-semibold text-gray-400 text-center leading-tight whitespace-nowrap transition-colors duration-200 group-hover:text-orange-500">
                    Witness </span>
            </a> <!-- 5. Identifier --> <a href="{{ route('identifier') }}"
                class="ngdrs-step group flex flex-col items-center flex-1 min-w-0" data-index="4">
                <div
                    class="step-circle w-9 h-9 rounded-full flex items-center justify-center border-2 border-orange-200 bg-white text-orange-300 transition-all duration-300 group-hover:border-orange-400 group-hover:text-orange-500 group-hover:shadow-sm">
                    <svg class="w-[15px] h-[15px]" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a4 4 0 118 0v1m-4 0a4 4 0 104 4M9 12h6m-3-3v6" />
                    </svg>
                </div> <span
                    class="step-label mt-2 text-[10px] font-semibold text-gray-400 text-center leading-tight whitespace-nowrap transition-colors duration-200">
                    Identifier </span>
            </a> <!-- 6. Fee Calculation --> <a href="{{ route('fee-calculation') }}"
                class="ngdrs-step group flex flex-col items-center flex-1 min-w-0" data-index="5">
                <div
                    class="step-circle w-9 h-9 rounded-full flex items-center justify-center border-2 border-orange-200 bg-white text-orange-300 transition-all duration-300 group-hover:border-orange-400 group-hover:text-orange-500 group-hover:shadow-sm">
                    <svg class="w-[15px] h-[15px]" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                </div> <span
                    class="step-label mt-2 text-[10px] font-semibold text-gray-400 text-center leading-tight whitespace-nowrap transition-colors duration-200">
                    Fee Calc. </span>
            </a> <!-- 7. eDeed --> <a href="{{ route('edeed') }}" class="ngdrs-step group flex flex-col items-center flex-1 min-w-0"
                data-index="6">
                <div
                    class="step-circle w-9 h-9 rounded-full flex items-center justify-center border-2 border-orange-200 bg-white text-orange-300 transition-all duration-300 group-hover:border-orange-400 group-hover:text-orange-500 group-hover:shadow-sm">
                    <svg class="w-[15px] h-[15px]" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div> <span
                    class="step-label mt-2 text-[10px] font-semibold text-gray-400 text-center leading-tight whitespace-nowrap transition-colors duration-200">
                    eDeed </span>
            </a> <!-- 8. Upload Document --> <a href="{{ route('upload-document') }}"
                class="ngdrs-step group flex flex-col items-center flex-1 min-w-0" data-index="7">
                <div
                    class="step-circle w-9 h-9 rounded-full flex items-center justify-center border-2 border-orange-200 bg-white text-orange-300 transition-all duration-300 group-hover:border-orange-400 group-hover:text-orange-500 group-hover:shadow-sm">
                    <svg class="w-[15px] h-[15px]" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 16V4m0 0l-4 4m4-4l4 4M4 16v3a2 2 0 002 2h12a2 2 0 002-2v-3" />
                    </svg>
                </div> <span
                    class="step-label mt-2 text-[10px] font-semibold text-gray-400 text-center leading-tight whitespace-nowrap transition-colors duration-200">
                    Upload </span>
            </a> <!-- 9. Appointment --> <a href="{{ route('appointment') }}"
                class="ngdrs-step group flex flex-col items-center flex-1 min-w-0" data-index="8">
                <div
                    class="step-circle w-9 h-9 rounded-full flex items-center justify-center border-2 border-orange-200 bg-white text-orange-300 transition-all duration-300 group-hover:border-orange-400 group-hover:text-orange-500 group-hover:shadow-sm">
                    <svg class="w-[15px] h-[15px]" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5v12a2 2 0 002 2z" />
                    </svg>
                </div> <span
                    class="step-label mt-2 text-[10px] font-semibold text-gray-400 text-center leading-tight whitespace-nowrap transition-colors duration-200">
                    Appointment </span>
            </a> </div>
    </div>
</div>
