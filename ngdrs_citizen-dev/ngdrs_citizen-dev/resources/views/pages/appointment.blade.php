<x-layout-app>

    {{-- Page Banner --}}
    <x-banner title="Appointment Booking"
        description="Select a suitable date and time slot to schedule your document registration appointment at the sub-registrar office."
        image="images/online-doc.png" />

    {{-- Progress Steps --}}
    <x-progress-steps />

    {{-- Form Container --}}
    <div class="max-w-7xl mx-auto bg-white rounded-xl shadow-sm border border-gray-100 p-8">
        <div class="max-w-5xl mx-auto">

            {{-- Pre-Registration Number Header --}}
            <x-prereg :preRegNo="$preRegNo" />

            <x-section-divider />

            <x-section-title title="Please Select An Available Date" icon="images/icons/generalinfo/filldetails.svg" />

            {{-- Main Content: Calendar + Time Slots --}}
            <div class="flex flex-col lg:flex-row gap-8 mt-6">

                {{-- Calendar --}}
                <div class="flex-1 border border-gray-200 rounded-2xl p-6 shadow-sm bg-white">
                    {{-- Calendar Navigation --}}
                    <div class="flex items-center justify-between mb-6">
                        <button type="button" id="prevMonth"
                            class="text-orange-500 hover:text-orange-600 transition-colors p-2 text-2xl font-bold cursor-pointer select-none">&laquo;</button>
                        <h3 id="calendarTitle" class="text-lg font-bold text-gray-800 select-none"></h3>
                        <button type="button" id="nextMonth"
                            class="text-orange-500 hover:text-orange-600 transition-colors p-2 text-2xl font-bold cursor-pointer select-none">&raquo;</button>
                    </div>

                    {{-- Day Headers --}}
                    <div class="grid grid-cols-7 mb-4 border-b border-gray-100 pb-2">
                        @foreach (['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $day)
                            <div
                                class="text-center text-xs font-bold text-gray-500 uppercase tracking-wider select-none py-1">
                                {{ $day }}</div>
                        @endforeach
                    </div>

                    {{-- Calendar Days Grid --}}
                    <div id="calendarGrid" class="grid grid-cols-7 gap-2 select-none"></div>
                </div>

                {{-- Time Slot Panel --}}
                <div id="timeSlotPanel"
                    class="w-full lg:w-80 border border-gray-200 rounded-2xl p-6 shadow-sm bg-white hidden flex flex-col">
                    <div class="flex items-center gap-2 mb-4">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" />
                            <path d="M12 6v6l4 2" stroke-linecap="round" />
                        </svg>
                        <h3 id="timeSlotTitle" class="text-sm font-bold text-gray-800">Select Available Time</h3>
                    </div>

                    {{-- Time Slot List --}}
                    <div id="timeSlotList" class="space-y-3 overflow-y-auto max-h-80 pr-1 scrollbar-thin"></div>

                    {{-- Schedule Button --}}
                    <div class="mt-6">
                        <button type="button" id="scheduleBtn"
                            class="w-full py-3 rounded-xl text-sm font-semibold transition-all duration-200 bg-gray-200 text-gray-400 cursor-not-allowed"
                            disabled>
                            Select This Slot
                        </button>
                    </div>
                </div>
            </div>

            {{-- Appointment Summary & Confirm Form --}}
            <div id="summaryBar" class="hidden mt-6">
                <div
                    class="bg-orange-50 border border-orange-200 rounded-2xl py-4 px-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <svg class="w-6 h-6 text-orange-600 flex-shrink-0" fill="none" stroke="currentColor"
                            stroke-width="2" viewBox="0 0 24 24">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                            <line x1="16" y1="2" x2="16" y2="6" stroke-linecap="round" />
                            <line x1="8" y1="2" x2="8" y2="6" stroke-linecap="round" />
                            <line x1="3" y1="10" x2="21" y2="10" />
                        </svg>
                        <div>
                            <span class="text-xs text-gray-500 font-medium block">Selected Appointment Slot</span>
                            <span id="summaryText" class="text-sm font-bold text-gray-800"></span>
                        </div>
                    </div>

                    {{-- Form Submission to confirm the appointment --}}
                    <form id="appointmentForm" method="POST" action="{{ url('api/v1/appointments/book') }}"
                        class="w-full sm:w-auto">
                        @csrf
                        <input type="hidden" name="token_no" id="input_token_no" value="{{ $preRegNo }}">
                        <input type="hidden" name="user_id" id="input_user_id" value="{{ auth()->id() ?? 1 }}">
                        <input type="hidden" name="office_id" id="office_id" value="{{ $officeId }}">
                        <input type="hidden" name="shift_id" id="shift_id" value="{{ $shiftId }}">
                        <input type="hidden" name="appointment_date" id="input_appointment_date">
                        <input type="hidden" name="interval_id" id="input_interval_id">
                        <input type="hidden" name="slot_no" id="input_slot_no" value="1">
                        <input type="hidden" name="sheduled_time" id="input_sheduled_time">
                        <input type="hidden" name="applicant_mobile_numbers[]"
                            value="{{ session('mobile_no', '9876543210') }}">

                        <button type="submit" id="submitAppointmentBtn"
                            class="w-full sm:w-auto flex items-center justify-center gap-2 bg-orange-500 hover:bg-orange-600 active:bg-orange-700 text-white font-semibold text-sm px-6 py-3 rounded-lg shadow-md transition-colors duration-200 cursor-pointer">
                            Confirm & Submit
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>

            {{-- Previous Page navigation --}}
            <div class="flex justify-start gap-4 mt-8">
                <button type="button" onclick="window.history.back()"
                    class="flex items-center justify-center px-8 py-3 text-sm font-semibold text-orange-500 border border-orange-500 rounded-lg hover:bg-orange-50 transition-colors duration-200 hover:cursor-pointer">
                    Previous Page
                </button>
            </div>

        </div>
    </div>

    @push('page-scripts')
        @vite('resources/js/pages/appointment/main.ts')
    @endpush

</x-layout-app>
