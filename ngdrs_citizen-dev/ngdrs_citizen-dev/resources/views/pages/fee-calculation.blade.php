<x-layout-app>
    <!--
    <x-banner title="Fee Calculation"
        description="This page facilitates fee calculation for your document registration, covering stamp duty, municipal charges, and other applicable fees. These details ensure a transparent and smooth calculation for your convenience."
        image="images/icons/fee-calculation/prop-banner.png" /> -->
    <x-progress-steps />

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-start">
        <div class="lg:col-span-7 space-y-6">
            <div class="border border-gray-100 rounded-2xl p-6 bg-white shadow-sm">
                <div class="text-lg font-semibold text-gray-800 tracking-tight">
                    Pre Registration No. - <span id="pre-registration-no" class="font-normal text-gray-800 ml-1">
                        {{ $preRegistrationNo }}
                        {{-- 202600030702 --}}
                    </span>
                </div>
                <x-section-divider />
                <x-select-input label="Please Select Fee Rule First" name="fee_rule" id="fee-rule-select"
                    :required="true">
                </x-select-input>
            </div>

            <!-- Fee Rule Parameters Card -->
            <div id="fee-rule-card" class="hidden border border-gray-100 rounded-2xl p-6 bg-white shadow-sm space-y-6">
                <div class="flex items-center pb-4 border-b border-gray-100">
                    <svg width="22" height="25" viewBox="0 0 22 25" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M21.75 23.5625C21.75 23.8029 21.6545 24.0334 21.4846 24.2033C21.3146 24.3733 21.0841 24.4688 20.8438 24.4688H0.90625C0.665898 24.4688 0.435389 24.3733 0.265435 24.2033C0.0954798 24.0334 0 23.8029 0 23.5625C0 23.3221 0.0954798 23.0916 0.265435 22.9217C0.435389 22.7517 0.665898 22.6562 0.90625 22.6562H20.8438C21.0841 22.6562 21.3146 22.7517 21.4846 22.9217C21.6545 23.0916 21.75 23.3221 21.75 23.5625ZM21.75 14.5V19.0312C21.75 19.512 21.559 19.973 21.2191 20.3129C20.8792 20.6528 20.4182 20.8438 19.9375 20.8438H1.8125C1.3318 20.8438 0.870779 20.6528 0.530869 20.3129C0.190959 19.973 0 19.512 0 19.0312V14.5C0 14.0193 0.190959 13.5583 0.530869 13.2184C0.870779 12.8785 1.3318 12.6875 1.8125 12.6875H8.20496L6.42531 4.38512C6.31198 3.85665 6.31825 3.30953 6.44368 2.7838C6.5691 2.25807 6.81049 1.76704 7.15019 1.34665C7.48989 0.926262 7.9193 0.587155 8.40698 0.354151C8.89466 0.121148 9.42827 0.000145095 9.96875 3.51669e-09H11.7812C12.3218 -2.38062e-05 12.8556 0.120855 13.3434 0.353785C13.8312 0.586716 14.2608 0.925801 14.6006 1.34621C14.9404 1.76662 15.1819 2.25771 15.3074 2.78352C15.4329 3.30934 15.4392 3.85656 15.3258 4.38512L13.545 12.6875H19.9375C20.4182 12.6875 20.8792 12.8785 21.2191 13.2184C21.559 13.5583 21.75 14.0193 21.75 14.5ZM10.0582 12.6875H11.6918L13.553 4.00449C13.6095 3.7403 13.6063 3.4668 13.5436 3.20401C13.4808 2.94121 13.3601 2.69577 13.1903 2.48565C13.0204 2.27552 12.8058 2.10602 12.562 1.98955C12.3182 1.87308 12.0514 1.81259 11.7812 1.8125H9.96875C9.69847 1.81242 9.4316 1.87279 9.18767 1.98919C8.94374 2.10558 8.72892 2.27506 8.55896 2.48521C8.389 2.69535 8.26818 2.94085 8.20536 3.20373C8.14254 3.46661 8.13931 3.7402 8.1959 4.00449L10.0582 12.6875ZM19.9375 19.0312V14.5H1.8125V19.0312H19.9375Z"
                            fill="#1B1B1E" />
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-800 ml-3">Stamp Duty Calculation</h3>
                </div>

                <!-- Token No (Hidden Field) -->
                <input type="hidden" id="token-no" name="token_no"
                    value="{{ $preRegistrationNo ?? ($tokenNo ?? '') }}" />

                <!-- Article ID (Hidden Field) -->
                <input type="hidden" id="article-id" value="{{ $articleId ?? request('article_id', '91') }}" />

                <!-- FBO (Hidden Field) -->
                <input type="hidden" name="data[frm][FBO]" id="FBO" value="0" />

                <!-- Text & Number Inputs Grid -->
                <div id="valuation-inputs-grid" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- FAA: Zonal Value -->
                    <x-text-input label="Zonal Value" name="data[frm][FAA]" id="FAA"
                        value="{{ $zonalValuation ?? 0 }}" maxlength="15" readonly />

                    <!-- Consideration Amount -->
                    <div id="consideration-amount-wrapper">
                        <x-number-input label="Consideration Amount" name="data[frm][consideration_amount]"
                            id="consideration-amount" placeholder="Enter Consideration Amount"
                            value="{{ !empty($considerationAmount) && (float) $considerationAmount > 0 ? $considerationAmount : 0 }}"
                            :required="true" />
                    </div>
                </div>

                <!-- Toggle Options Grid -->
                <div id="fee-rules-toggles-container">
                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4">Rules</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <!-- FBG: Municipality charges -->
                        <div
                            class="flex items-center justify-between p-3 bg-white rounded-lg border border-gray-100 shadow-sm hover:border-gray-200 transition-colors">
                            <span class="text-sm font-semibold text-gray-700">Municipality Charges</span>
                            <label class="relative inline-flex items-center cursor-pointer select-none">
                                <input type="checkbox" id="FBG" name="data[frm][FBG]" value="1"
                                    class="sr-only peer"
                                    {{ !empty($feeParams['FBG']) || !empty($feeParams['municipality_flag']) ? 'checked' : '' }}>
                                <div
                                    class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-500">
                                </div>
                            </label>
                        </div>

                        <!-- FBQ: metrocity -->
                        <div
                            class="flex items-center justify-between p-3 bg-white rounded-lg border border-gray-100 shadow-sm hover:border-gray-200 transition-colors">
                            <span class="text-sm font-semibold text-gray-700">Metro City</span>
                            <label class="relative inline-flex items-center cursor-pointer select-none">
                                <input type="checkbox" id="FBQ" name="data[frm][FBQ]" value="1"
                                    class="sr-only peer"
                                    {{ !empty($feeParams['FBQ']) || !empty($feeParams['metro_city_flag']) ? 'checked' : '' }}>
                                <div
                                    class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-500">
                                </div>
                            </label>
                        </div>

                        <!-- FBR: Home Visit -->
                        <div
                            class="flex items-center justify-between p-3 bg-white rounded-lg border border-gray-100 shadow-sm hover:border-gray-200 transition-colors">
                            <span class="text-sm font-semibold text-gray-700">Home Visit</span>
                            <label class="relative inline-flex items-center cursor-pointer select-none">
                                <input type="checkbox" id="FBR" name="data[frm][FBR]" value="1"
                                    class="sr-only peer"
                                    {{ !empty($feeParams['FBR']) || !empty($feeParams['home_visit_flag']) ? 'checked' : '' }}>
                                <div
                                    class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-500">
                                </div>
                            </label>
                        </div>

                        <!-- FCA: Tatkal -->
                        <div
                            class="flex items-center justify-between p-3 bg-white rounded-lg border border-gray-100 shadow-sm hover:border-gray-200 transition-colors">
                            <span class="text-sm font-semibold text-gray-700">Tatkal</span>
                            <label class="relative inline-flex items-center cursor-pointer select-none">
                                <input type="checkbox" id="FCA" name="data[frm][FCA]" value="1"
                                    class="sr-only peer"
                                    {{ !empty($feeParams['FCA']) || !empty($feeParams['tatkal_flag']) ? 'checked' : '' }}>
                                <div
                                    class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-500">
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end border-t border-gray-100">
                    <button type="button" id="calculate-btn"
                        class="flex items-center gap-2 bg-orange-500 hover:bg-orange-600 active:bg-orange-700 text-white font-semibold text-sm px-6 py-2.5 rounded-lg shadow-md transition-colors duration-200 cursor-pointer">
                        Calculate Now
                    </button>
                </div>
            </div>

            <div class="border border-gray-100 rounded-2xl p-6 bg-white shadow-sm space-y-6">
                <div class="flex items-center pb-4 border-b border-gray-100">
                    <svg width="26" height="28" viewBox="0 0 26 28" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M5.4979 0.0373653C2.76601 0.377934 0.551632 2.53802 0.0876415 5.31481C0.00964164 5.78173 0 6.73495 0 13.9987C0 21.2624 0.00964164 22.2156 0.0876415 22.6825C0.532024 25.342 2.56549 27.4237 5.19999 27.916C5.7981 28.0278 20.1995 28.0281 20.8 27.9163C22.1263 27.6695 23.256 27.0637 24.1889 26.0992C24.8709 25.3941 25.3712 24.5645 25.6736 23.6372C26.0121 22.5994 25.9999 22.9589 25.9999 13.9987C25.9999 5.03845 26.0121 5.39793 25.6736 4.36009C25.3712 3.43281 24.8709 2.60323 24.1889 1.89817C23.2993 0.978282 22.3528 0.44854 20.9895 0.107415C20.6429 0.0206735 19.9787 0.0115487 13.2437 0.00131104C9.19206 -0.00486492 5.70645 0.0113817 5.4979 0.0373653ZM5.30832 1.50886C4.22824 1.73653 3.15255 2.38929 2.50591 3.20947C2.00026 3.85072 1.74996 4.36121 1.49873 5.26329C1.38271 5.67991 1.38249 5.69371 1.36538 13.637C1.35373 19.0311 1.36678 21.7597 1.406 22.1096C1.51688 23.099 1.93223 24.0945 2.54637 24.8426C3.03479 25.4376 3.88136 26.0274 4.63563 26.2983C5.41655 26.5788 5.24424 26.5731 12.9948 26.5731C17.8181 26.5731 20.3458 26.5536 20.5598 26.5147C22.7149 26.1226 24.3432 24.3544 24.5904 22.1378C24.6643 21.4752 24.6643 6.52213 24.5904 5.85952C24.4812 4.8806 24.054 3.87781 23.4228 3.11889C22.752 2.31235 21.6459 1.68028 20.5598 1.48265C20.0421 1.38845 5.7631 1.41299 5.30832 1.50886ZM4.59836 6.90448C4.39329 7.01899 4.26112 7.25829 4.26112 7.51523C4.26112 7.7991 4.34817 7.96663 4.567 8.10367C4.73584 8.20939 4.91128 8.21173 12.675 8.21C19.0252 8.20855 20.6354 8.19398 20.7355 8.13689C20.9352 8.02311 21.07 7.76511 21.0704 7.4962C21.0707 7.28789 21.0408 7.2224 20.8605 7.03718L20.6502 6.82119L12.6949 6.82341C5.93655 6.82531 4.71834 6.83749 4.59836 6.90448ZM15.5798 11.0559C15.4644 11.0837 15.2085 11.1937 15.0111 11.3003C14.7055 11.4653 14.3554 11.799 12.6575 13.5435C10.817 15.4347 10.661 15.6098 10.637 15.8112C10.5817 16.2755 10.9555 16.6495 11.4156 16.5899L11.6458 16.5602V17.7437C11.6458 19.0552 11.6944 19.3724 11.9717 19.8703C12.1563 20.2016 12.5952 20.6462 12.9458 20.8569C13.4486 21.1592 13.6233 21.1761 16.2286 21.1761C18.4544 21.1761 18.646 21.1685 18.9661 21.0669C19.7645 20.8134 20.3927 20.1681 20.6394 19.3481C20.7315 19.0421 20.7458 18.8299 20.7458 17.7718V16.5492L20.9081 16.5858C21.243 16.6614 21.6194 16.4205 21.718 16.0673C21.803 15.7628 21.7015 15.5619 21.2074 15.057L20.7458 14.5853V13.3453V12.1053L20.5604 11.8974C20.3889 11.7052 20.3524 11.6896 20.0739 11.6896C19.8062 11.6896 19.7533 11.7098 19.5958 11.8715C19.4243 12.0474 19.4181 12.0701 19.3999 12.5809C19.3896 12.871 19.3649 13.1084 19.345 13.1084C19.3252 13.1084 18.9801 12.7685 18.5784 12.353C17.7402 11.4862 17.511 11.3064 17.0141 11.1262C16.6304 10.9871 15.9942 10.9559 15.5798 11.0559ZM15.8437 12.4672C15.7246 12.513 15.6161 12.5508 15.6026 12.5512C15.5891 12.5516 14.998 13.146 14.289 13.8719L13 15.1918V16.9075C13 18.9211 13.0163 19.0283 13.3805 19.4023C13.5891 19.6167 13.6888 19.6743 13.9456 19.729C14.117 19.7656 14.2776 19.7744 14.3024 19.7486C14.3271 19.7228 14.3549 19.1258 14.3643 18.422C14.3834 16.9745 14.4049 16.8865 14.8365 16.486C15.2135 16.1362 15.3724 16.0899 16.1958 16.0899C17.0192 16.0899 17.1781 16.1362 17.5551 16.486C17.9875 16.8873 18.0081 16.9724 18.0275 18.4391C18.0416 19.4968 18.0589 19.7415 18.1217 19.7662C18.2932 19.8338 18.7601 19.6587 18.991 19.4401C19.3742 19.0772 19.3916 18.9693 19.3916 16.9511V15.194L18.1323 13.9063C17.4396 13.1981 16.7941 12.5781 16.6978 12.5287C16.4392 12.3961 16.094 12.3713 15.8437 12.4672ZM4.567 12.5493C4.3473 12.6869 4.26112 12.8539 4.26112 13.1422C4.26112 13.4128 4.42172 13.6755 4.64554 13.7706C4.75512 13.8173 5.39558 13.8311 6.92091 13.8197L9.04381 13.8039L9.22088 13.622C9.37834 13.4602 9.3979 13.4059 9.3979 13.1309C9.3979 12.8449 9.38273 12.8074 9.19558 12.6312L8.99322 12.4408L6.86642 12.441C4.85598 12.4411 4.73015 12.4471 4.567 12.5493ZM15.7333 17.5729C15.7182 17.6134 15.7125 18.1214 15.7206 18.7019L15.7354 19.7573H16.1958H16.6562V18.6445V17.5318L16.2085 17.5156C15.8758 17.5035 15.7537 17.5183 15.7333 17.5729ZM4.77668 18.0582C4.53407 18.1193 4.34372 18.3148 4.27629 18.572C4.23046 18.7469 4.23891 18.8278 4.32374 19.0248C4.5072 19.4508 4.51077 19.4514 6.86025 19.4511C8.79806 19.4509 8.91988 19.445 9.08379 19.3423C9.40434 19.1415 9.50374 18.7762 9.33642 18.414C9.16249 18.0375 9.1386 18.0336 6.93332 18.0227C5.84592 18.0173 4.87542 18.0333 4.77668 18.0582Z"
                            fill="#212529" />
                    </svg>

                    <h3 class="text-lg font-semibold text-gray-800 ml-3">Properties Added</h3>
                </div>

                <div class="flex gap-6 overflow-x-auto pb-4 scrollbar-thin scrollbar-thumb-gray-200">
                    @forelse($properties ?? [] as $property)
                        <div
                            class="border border-gray-200 rounded-xl p-5 min-w-[280px] bg-white space-y-3 shrink-0 shadow-xs hover:border-gray-300 transition-colors">
                            <div class="flex justify-between items-center text-sm pb-2 border-b border-gray-100">
                                <span class="font-bold text-gray-800">Property Id</span>
                                <span
                                    class="font-bold text-gray-800 {{ $loop->first ? 'property-id-val' : '' }}">{{ $property['property_id'] }}</span>
                            </div>
                            @if (!empty($property['dag_no']))
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-600">Dag No.</span>
                                    <span class="text-gray-800 font-medium">{{ $property['dag_no'] }}</span>
                                </div>
                            @endif
                            @if (!empty($property['patta_no']))
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-600">Patta No.</span>
                                    <span class="text-gray-800 font-medium">{{ $property['patta_no'] }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-600">Land Area In Bigha</span>
                                <span class="text-gray-800 font-medium">{{ $property['area_bigha'] ?? 0 }}</span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-600">Land Area In Katha</span>
                                <span class="text-gray-800 font-medium">{{ $property['area_kattha'] ?? 0 }}</span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-600">Land Area In Lessa</span>
                                <span class="text-gray-800 font-medium">{{ $property['area_lessa'] ?? 0 }}</span>
                            </div>
                        </div>
                    @empty
                        <div
                            class="w-full flex flex-col items-center justify-center p-6 bg-gray-50 border border-dashed border-gray-200 rounded-xl text-center space-y-2">
                            <svg class="w-10 h-10 text-gray-400 mb-1" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <p class="text-sm font-semibold text-gray-700">No Properties Found</p>
                            <p class="text-xs text-gray-500 max-w-sm">No property details are linked to this
                                application token ({{ $tokenNo ?: 'N/A' }}). You may proceed with calculations or add
                                property details in the previous step.</p>
                            <!-- Hidden fallback property ID so fee calculation continues during testing -->
                            <span class="hidden property-id-val">1</span>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="lg:col-span-5 space-y-6">
            <div class="border border-gray-100 rounded-2xl p-6 bg-white shadow-sm space-y-4">
                <label class="block text-lg font-semibold text-gray-800">
                    Exemptions
                </label>

                <!-- Toggles / Checkboxes -->
                <div class="space-y-3 py-1">
                    <div
                        class="flex items-center justify-between p-3 bg-gray-50/50 rounded-xl border border-gray-100 shadow-sm hover:border-gray-200/80 transition-colors">
                        <span class="text-sm font-semibold text-gray-700">Ex-servicemen ?</span>
                        <label class="relative inline-flex items-center cursor-pointer select-none">
                            <input type="checkbox" id="toggle-ex-servicemen" class="sr-only peer">
                            <div
                                class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-500">
                            </div>
                        </label>
                    </div>

                    {{-- <div
                        class="flex items-center justify-between p-3 bg-gray-50/50 rounded-xl border border-gray-100 shadow-sm hover:border-gray-200/80 transition-colors">
                        <span class="text-sm font-semibold text-gray-700">Tea Garden ?</span>
                        <label class="relative inline-flex items-center cursor-pointer select-none">
                            <input type="checkbox" id="toggle-tea-garden" class="sr-only peer">
                            <div
                                class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-500">
                            </div>
                        </label>
                    </div> --}}
                </div>

                <!-- Ex-servicemen sub-section -->
                <div id="section-ex-servicemen" class="hidden mt-3 space-y-2">
                    <label class="block text-xs font-semibold text-gray-600">
                        Ex-servicemen ?
                        <svg class="w-4.5 h-4.5 text-orange-500 inline-block align-middle ml-1" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10" />
                            <line x1="12" y1="16" x2="12" y2="12" />
                            <line x1="12" y1="8" x2="12.01" y2="8" />
                        </svg>
                    </label>
                    <div class="flex gap-3">
                        <input type="text" id="service-no-input" placeholder="Enter Service Number"
                            class="flex-1 border border-gray-300 rounded-lg text-sm px-4 py-2.5 bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent" />
                        <button type="button" id="verify-ex-servicemen-btn"
                            class="bg-success hover:bg-[#157347] text-white font-semibold text-sm px-5 py-2.5 rounded-lg transition-colors cursor-pointer shrink-0">
                            Click to Apply
                        </button>
                    </div>

                    <!-- Ex-servicemen breakdown card -->
                    <div id="ex-servicemen-details-card"
                        class="hidden mt-3 p-3.5 bg-emerald-50/80 border border-emerald-200/80 rounded-xl space-y-2.5 text-xs">
                        <div class="flex items-center justify-between font-semibold">
                            <span class="flex items-center gap-1.5 text-emerald-800">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor"
                                    stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Ex-Servicemen Concession
                            </span>
                            <span id="ex-service-no-badge"
                                class="px-2 py-0.5 bg-emerald-100 text-emerald-800 rounded font-mono font-medium text-[11px]"></span>
                        </div>
                        <div id="ex-servicemen-items-table"
                            class="space-y-1.5 pt-1 border-t border-emerald-200/60 font-sans">
                            <!-- Dynamic Fee Items Concession Breakdown -->
                        </div>
                        <div
                            class="flex justify-between items-center pt-1.5 border-t border-emerald-200/60 font-medium text-emerald-900">
                            <span>Total Exemption Saved:</span>
                            <span id="ex-total-savings" class="font-bold text-emerald-700 text-sm"></span>
                        </div>
                    </div>
                </div>

                <!-- Tea garden sub-section -->
                {{-- <div id="section-tea-garden" class="hidden mt-3 space-y-2">
                    <x-select-input label="Tea Garden Area" name="tea_garden_area" id="tea-garden-area">
                        <option value="less_500">&lt;500 acre</option>
                        <option value="more_500">&gt;500 acre</option>
                    </x-select-input>
                </div> --}}
            </div>

            <div class="border border-gray-100 rounded-2xl bg-white shadow-md">
                <div
                    class="relative receipt-container bg-gray-50 p-6 rounded-2xl border border-gray-200/60 overflow-hidden">

                    {{-- Section header --}}
                    <div class="flex items-center gap-2 mb-5">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.75"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <h3 class="text-xs font-medium text-gray-400 uppercase tracking-widest">Your Total Payment</h3>
                    </div>

                    <div id="payment-breakdown" class="bg-white border border-gray-100 rounded-xl p-4">
                        <div class="flex items-center gap-2 text-sm text-gray-400">
                            <svg class="w-4 h-4 flex-shrink-0 text-gray-300" fill="none" stroke="currentColor"
                                stroke-width="1.75" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 7h6m-3-3v3m-6 4h12M9 16h6" />
                            </svg>
                            Calculate fees to view total payable amount
                        </div>
                    </div>

                    <div class="border-t border-gray-200/70 my-5"></div>

                    <div class="flex justify-between items-end">
                        <div class="space-y-1">
                            <span class="text-xs font-medium text-gray-400 uppercase tracking-widest">Payable
                                Amount</span>
                            <div id="payment-total"
                                class="text-3xl font-semibold text-gray-800 tracking-tight leading-none">₹0</div>
                        </div>

                        <div
                            class="w-10 h-10 rounded-xl bg-gray-100 border border-gray-200/60 flex items-center justify-center flex-shrink-0">
                            <svg width="16" height="20" viewBox="0 0 36 44" fill="none"
                                xmlns="http://www.w3.org/2000/svg" class="text-gray-500">
                                <path
                                    d="M24 0H3C1.34 0 0 1.34 0 3V41C0 42.66 1.34 44 3 44H33C34.66 44 36 42.66 36 41V12L24 0ZM24 12V3L33 12H24Z"
                                    fill="currentColor" />
                                <path d="M8 20H28" stroke="white" stroke-width="3" stroke-linecap="round" />
                                <path d="M8 27H28" stroke="white" stroke-width="3" stroke-linecap="round" />
                                <path d="M8 34H20" stroke="white" stroke-width="3" stroke-linecap="round" />
                            </svg>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="flex justify-end gap-4 my-16">
        <!-- Previous Page -->
        <button type="button" onclick="window.history.back()"
            class="flex items-center justify-center px-8 py-3 text-sm font-semibold text-orange-500
                                border border-orange-500 rounded-lg
                                hover:bg-orange-50 transition-colors duration-200 hover:cursor-pointer">
            Previous Page
        </button>

        <!-- Save and Continue -->
        <button type="submit"
            class="flex items-center gap-2 bg-orange-500 hover:bg-orange-600 active:bg-orange-700
                                text-white font-semibold text-sm px-8 py-3 rounded-lg shadow-md
                                transition-colors duration-200 cursor-pointer">
            Save and Continue
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
            </svg>
        </button>

    </div>

    <!-- Ex-servicemen Disclaimer Modal -->
    <x-ui.alert-modal id="ex-servicemen-disclaimer-modal" type="disclaimer" title="Disclaimer :"
        confirmText="Confirm" cancelText="Cancel">
        <div class="text-left text-sm leading-relaxed text-gray-600 mt-2 space-y-2">
            <p>Ex-servicemen exemption could be used just once. 75% fee waived in registration, however party is liable
                to pay full amount in stamp.</p>
            <p>Once this is used, it cannot be re-used for any other deed.</p>
        </div>
    </x-ui.alert-modal>

    <!-- Ex-servicemen Remove Confirmation Modal -->
    <x-ui.alert-modal id="ex-servicemen-remove-modal" type="warning" title="Remove Exemption"
        description="Are you sure you want to remove the ex-servicemen exemption? This will restore original amounts."
        confirmText="Yes, Remove" cancelText="No, Keep" />

    @push('page-scripts')
        @vite('resources/js/pages/fee-calculation/main.ts')
    @endpush

</x-layout-app>
