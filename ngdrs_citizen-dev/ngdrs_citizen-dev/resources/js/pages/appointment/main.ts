import { notyf } from "../../utils/ui/notyf";
import { swalConfirm } from "../../utils/ui/swal";
import Swal from "sweetalert2";
import api from "../../utils/http/axios";

document.addEventListener("DOMContentLoaded", () => {
    // ── State ──────────────────────────────────────────────────
    const today = new Date();
    let currentYear = today.getFullYear();
    let currentMonth = today.getMonth(); // 0-indexed
    let selectedDate: number | null = null;
    let selectedSlot: string | null = null;

    const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

    // ── DOM refs ──────────────────────────────────────────────
    const title = document.getElementById('calendarTitle') as HTMLHeadingElement | null;
    const grid = document.getElementById('calendarGrid') as HTMLDivElement | null;
    const slotPanel = document.getElementById('timeSlotPanel') as HTMLDivElement | null;
    const slotList = document.getElementById('timeSlotList') as HTMLDivElement | null;
    const slotTitle = document.getElementById('timeSlotTitle') as HTMLHeadingElement | null;
    const scheduleBtn = document.getElementById('scheduleBtn') as HTMLButtonElement | null;
    const summaryBar = document.getElementById('summaryBar') as HTMLDivElement | null;
    const summaryText = document.getElementById('summaryText') as HTMLSpanElement | null;

    const inputDate = document.getElementById('input_appointment_date') as HTMLInputElement | null;
    const inputTime = document.getElementById('input_appointment_time') as HTMLInputElement | null;

    const prevMonthBtn = document.getElementById('prevMonth');
    const nextMonthBtn = document.getElementById('nextMonth');

    // ── Availability Helper ───────────────────────────────────
    function isDateAvailable(year: number, month: number, day: number): boolean {
        const todayNoTime = new Date();
        todayNoTime.setHours(0, 0, 0, 0);

        const date = new Date(year, month, day);
        const dayOfWeek = date.getDay();

        // Sunday (0) is always closed
        if (dayOfWeek === 0) return false;

        // Saturday (6) is open on 1st, 3rd, and 5th Saturdays, closed on 2nd and 4th
        if (dayOfWeek === 6) {
            const saturdayRank = Math.ceil(day / 7);
            if (saturdayRank === 2 || saturdayRank === 4) {
                return false;
            }
        }

        // Past dates are not available
        return date >= todayNoTime;
    }

    // ── Calendar Render ───────────────────────────────────────
    function renderCalendar() {
        if (!title || !grid) return;

        title.textContent = `${monthNames[currentMonth]} ${currentYear}`;
        grid.innerHTML = '';

        const firstDay = new Date(currentYear, currentMonth, 1).getDay(); // 0=Sun
        // Convert to Mon-start: Mon=0 ... Sun=6
        const offset = (firstDay === 0) ? 6 : firstDay - 1;
        const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
        const prevDays = new Date(currentYear, currentMonth, 0).getDate();

        // Previous month filler
        for (let i = offset - 1; i >= 0; i--) {
            const d = document.createElement('div');
            d.className = 'text-center text-xs py-2.5 mx-0.5 rounded-lg text-gray-300 opacity-40 cursor-default';
            d.textContent = String(prevDays - i);
            grid.appendChild(d);
        }

        // Current month days
        for (let d = 1; d <= daysInMonth; d++) {
            const cell = document.createElement('div');
            cell.className = 'text-center text-xs py-2.5 mx-0.5 rounded-lg font-medium transition-all duration-150';
            cell.textContent = String(d);

            const isAvail = isDateAvailable(currentYear, currentMonth, d);
            if (isAvail) {
                if (selectedDate === d) {
                    cell.className += ' bg-orange-500 text-white font-bold shadow-md shadow-orange-200 border border-orange-600 cursor-pointer';
                } else {
                    cell.className += ' border border-orange-100 text-orange-600 hover:bg-orange-50 hover:border-orange-300 cursor-pointer';
                }
                cell.addEventListener('click', () => selectDate(d));
            } else {
                cell.className += ' text-gray-300 bg-gray-50/50 cursor-not-allowed';
            }
            grid.appendChild(cell);
        }

        // Next month filler
        const totalCells = offset + daysInMonth;
        const remaining = totalCells % 7 === 0 ? 0 : 7 - (totalCells % 7);
        for (let i = 1; i <= remaining; i++) {
            const d = document.createElement('div');
            d.className = 'text-center text-xs py-2.5 mx-0.5 rounded-lg text-gray-300 opacity-40 cursor-default';
            d.textContent = String(i);
            grid.appendChild(d);
        }
    }


    function extractCleanErrorMessage(rawMessage: string): string {
        if (!rawMessage) return "Unable to connect to appointment services. Please try again later.";

        // Check for connection refusal, timeout, network or cURL errors
        if (
            /failed to connect/i.test(rawMessage) ||
            /connection refused/i.test(rawMessage) ||
            /curl error/i.test(rawMessage) ||
            /network error/i.test(rawMessage) ||
            /network is unreachable/i.test(rawMessage) ||
            /failed to fetch/i.test(rawMessage) ||
            /econnrefused/i.test(rawMessage) ||
            /timed?\s*out/i.test(rawMessage)
        ) {
            return "Unable to connect to appointment services. Please verify the service is running or try again later.";
        }

        try {
            const jsonMatch = rawMessage.match(/\{[\s\S]*\}/);
            if (jsonMatch) {
                const parsed = JSON.parse(jsonMatch[0]);
                if (parsed.message) {
                    return parsed.message;
                }
                if (parsed.errors) {
                    const firstErr = Object.values(parsed.errors)[0];
                    if (Array.isArray(firstErr)) return String(firstErr[0]);
                    if (typeof firstErr === 'string') return firstErr;
                }
            }
        } catch {
            // Not a JSON match
        }

        const cleaned = rawMessage
            .replace(/^Failed to book appointment:\s*/i, '')
            .replace(/^Failed to fetch appointment slots:\s*/i, '')
            .replace(/^Unable to verify appointment date:\s*/i, '')
            .replace(/^HTTP request returned status code \d+:\s*/i, '')
            .replace(/^[{"\\]+/, '')
            .trim();

        if (/failed to connect|connection refused|curl error|econnrefused/i.test(cleaned)) {
            return "Unable to connect to appointment services. Please try again later.";
        }

        return cleaned;
    }

    function renderHolidayNotice(appDate: string, message: string) {
        if (!slotPanel || !slotList) return;
        slotPanel.classList.remove('hidden');
        resetScheduleBtn();
        if (summaryBar) summaryBar.classList.add('hidden');

        slotList.innerHTML = `
            <div class="flex flex-col items-center justify-center p-6 text-center bg-amber-50/80 border border-amber-200 rounded-2xl animate-fade-in my-2">
                <div class="w-12 h-12 rounded-full bg-amber-100 border border-amber-200/60 flex items-center justify-center text-amber-600 mb-3 shadow-sm shadow-amber-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <h4 class="text-sm font-bold text-amber-900">Office Closed / Holiday</h4>
                <p class="text-xs text-amber-700 mt-1.5 leading-relaxed">${message || 'The selected date is a holiday or non-working day.'}</p>
                <div class="mt-3.5 px-3 py-1 bg-white border border-amber-200 text-amber-800 text-[11px] font-medium rounded-full shadow-2xs">
                    Please select another working date
                </div>
            </div>
        `;
    }

    function renderServiceUnreachableNotice(message: string, onRetry?: () => void) {
        if (!slotPanel || !slotList) return;
        slotPanel.classList.remove('hidden');
        resetScheduleBtn();
        if (summaryBar) summaryBar.classList.add('hidden');

        slotList.innerHTML = `
            <div class="flex flex-col items-center justify-center p-6 text-center bg-red-50/80 border border-red-200 rounded-2xl animate-fade-in my-2">
                <div class="w-12 h-12 rounded-full bg-red-100 border border-red-200/60 flex items-center justify-center text-red-600 mb-3 shadow-sm shadow-red-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h4 class="text-sm font-bold text-red-900">Service Unreachable</h4>
                <p class="text-xs text-red-700 mt-1.5 leading-relaxed">${message || 'Unable to connect to appointment services. Please try again later.'}</p>
                <button type="button" id="retryConnectionBtn" class="mt-3.5 px-4 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors cursor-pointer">
                    Retry Connection
                </button>
            </div>
        `;

        if (onRetry) {
            document.getElementById('retryConnectionBtn')?.addEventListener('click', onRetry);
        }
    }

    function showSlotSpinner(appDate: string) {
        if (!slotPanel || !slotList) return;
        slotPanel.classList.remove('hidden');
        slotList.innerHTML = `
            <div class="flex flex-col items-center justify-center py-14 text-center">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-3 border-orange-500 border-t-transparent mb-3"></div>
                <p class="text-xs font-semibold text-gray-700">Checking Availability...</p>
                <p class="text-[11px] text-gray-400 mt-1">Loading time slots for ${appDate}</p>
            </div>
        `;
    }

    async function selectDate(day: number) {
        const appDate = formatDate(day);
        selectedDate = day;
        selectedSlot = null;
        renderCalendar();
        resetScheduleBtn();

        if (summaryBar) summaryBar.classList.add('hidden');

        // Show spinner immediately on date click
        showSlotSpinner(appDate);

        try {
            // 1️⃣ Check date availability
            const checkResponse = await api.post('/v1/slots/check-date', {
                
                    date: appDate,
                    state_id: 18,
                    district_id: 1
                }, {
                headers: {
                    'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement).content
                }
            });

            const checkData = checkResponse.data;
            if (!checkData.success) {
                const cleanMsg = extractCleanErrorMessage(checkData.message || 'Date is unavailable.');
                const isHoliday = Boolean(checkData.is_holiday) ||
                    cleanMsg.toLowerCase().includes('holiday') ||
                    cleanMsg.toLowerCase().includes('non-working');

                if (isHoliday) {
                    renderHolidayNotice(appDate, cleanMsg);
                } else if (/unable to connect/i.test(cleanMsg) || /unreachable/i.test(cleanMsg)) {
                    renderServiceUnreachableNotice(cleanMsg, () => selectDate(day));
                } else {
                    if (slotPanel) slotPanel.classList.add('hidden');
                }
                notyf.error(cleanMsg);
                return;
            }

            // 2️⃣ Load slots for that date
            await loadSlots(appDate);
        } catch (err: any) {
            const cleanMsg = extractCleanErrorMessage(err.message || 'Failed to check appointment date');
            renderServiceUnreachableNotice(cleanMsg, () => selectDate(day));
            notyf.error(cleanMsg);
        }
    }

    function formatDate(day: number): string {
        const dd = String(day).padStart(2, '0');
        const mm = String(currentMonth + 1).padStart(2, '0');
        return `${currentYear}-${mm}-${dd}`;
    }

    async function loadSlots(appDate: string) {
        const shiftId = (document.getElementById('shift_id') as HTMLInputElement).value;
        const officeId = (document.getElementById('office_id') as HTMLInputElement)?.value || '15';

        try {
            const response = await api.post('/v1/slots/available', {
                
                    office_id: parseInt(officeId, 10),
                    shift_id: parseInt(shiftId, 10),
                    appointment_date: appDate,
                    appointment_type: 'N'
                }, {
                headers: {
                    'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement).content
                }
            });

            const data = response.data;

            if (!data.success) {
                const cleanMsg = extractCleanErrorMessage(data.message || 'Failed to load available slots.');
                const isHoliday = Boolean(data.is_holiday) ||
                    cleanMsg.toLowerCase().includes('holiday') ||
                    cleanMsg.toLowerCase().includes('non-working');

                if (isHoliday) {
                    renderHolidayNotice(appDate, cleanMsg);
                } else if (/unable to connect/i.test(cleanMsg) || /unreachable/i.test(cleanMsg)) {
                    renderServiceUnreachableNotice(cleanMsg, () => loadSlots(appDate));
                } else if (slotList) {
                    slotList.innerHTML = `<div class="text-xs text-red-500 text-center py-6">${cleanMsg}</div>`;
                }

                notyf.error(cleanMsg);
                return;
            }

            const slotsList = data.intervals || data.slots || data.data || [];
            renderSlots(appDate, slotsList);
        } catch (err: any) {
            const cleanMsg = extractCleanErrorMessage(err.message || 'Failed to fetch appointment slots.');
            renderServiceUnreachableNotice(cleanMsg, () => loadSlots(appDate));
            notyf.error(cleanMsg);
        }
    }

    interface GroupedInterval {
        intervalId: number;
        timeRange: string;
        totalCapacity: number;
        availableCount: number;
        isAvailable: boolean;
        isExpired: boolean;
        firstAvailableSlotNo: number;
        subSlots: {
            slotNo: number;
            slotKey?: string;
            isAvailable: boolean;
        }[];
    }

    function groupSlotsByInterval(slotsData: any[]): GroupedInterval[] {
        const map = new Map<string, GroupedInterval>();

        slotsData.forEach((slot, index) => {
            const timeRange = slot.time_range || slot.time || `Interval ${index + 1}`;
            const intervalId = slot.interval_id ?? (index + 1);
            const key = `${intervalId}_${timeRange}`;

            if (!map.has(key)) {
                map.set(key, {
                    intervalId,
                    timeRange,
                    totalCapacity: 0,
                    availableCount: 0,
                    isAvailable: false,
                    isExpired: slot.is_expired ?? false,
                    firstAvailableSlotNo: 0,
                    subSlots: []
                });
            }

            const group = map.get(key)!;

            // Direct support for new intervals format with sub_slots array
            if (slot.sub_slots && Array.isArray(slot.sub_slots)) {
                group.totalCapacity = slot.total_capacity ?? slot.sub_slots.length;
                group.availableCount = slot.available_count ?? slot.sub_slots.filter((s: any) => s.is_available).length;
                group.isAvailable = (slot.is_available !== undefined) ? Boolean(slot.is_available) : (group.availableCount > 0);
                group.firstAvailableSlotNo = slot.next_available_slot_no ?? (slot.sub_slots.find((s: any) => s.is_available)?.slot_no ?? 1);
                group.subSlots = slot.sub_slots.map((s: any) => ({
                    slotNo: s.slot_no,
                    slotKey: s.slot_key ?? `${intervalId}_${s.slot_no}`,
                    isAvailable: Boolean(s.is_available) && !slot.is_expired
                }));
            } else if (slot.total_capacity && slot.total_capacity > 1 && (!slot.slot_no || slot.total_capacity !== 1)) {
                // Aggregated interval without sub_slots array
                group.totalCapacity = slot.total_capacity;
                group.availableCount = slot.available_count ?? slot.available ?? 0;
                group.isAvailable = (slot.is_available !== undefined) ? slot.is_available : (group.availableCount > 0);
                group.firstAvailableSlotNo = slot.next_available_slot_no ?? slot.slot_no ?? 1;

                for (let i = 1; i <= group.totalCapacity; i++) {
                    group.subSlots.push({
                        slotNo: i,
                        slotKey: `${intervalId}_${i}`,
                        isAvailable: i <= group.availableCount
                    });
                }
            } else {
                // Flat sub-slot item (e.g. slot_key: "3_1", slot_no: 1)
                const isSlotAvail = (slot.is_available !== undefined)
                    ? Boolean(slot.is_available)
                    : ((slot.available_count ?? slot.available ?? 1) > 0);
                const slotNo = slot.slot_no ?? (group.subSlots.length + 1);

                group.totalCapacity += 1;
                if (isSlotAvail && !slot.is_expired) {
                    group.availableCount += 1;
                    if (group.firstAvailableSlotNo === 0) {
                        group.firstAvailableSlotNo = slotNo;
                    }
                }
                group.subSlots.push({
                    slotNo,
                    slotKey: slot.slot_key ?? `${intervalId}_${slotNo}`,
                    isAvailable: isSlotAvail && !slot.is_expired
                });
                group.isAvailable = group.availableCount > 0 && !group.isExpired;
            }

            if (slot.is_expired) {
                group.isExpired = true;
                group.isAvailable = false;
            }
        });

        return Array.from(map.values());
    }

    function renderSlots(appDate: string, slotsData: any[]) {
        if (!slotList || !slotPanel) return;

        slotList.innerHTML = '';
        slotPanel.classList.remove('hidden');

        if (!slotsData || slotsData.length === 0) {
            slotList.innerHTML = '<div class="text-sm text-gray-500 text-center py-4">No available slots found for the selected date.</div>';
            return;
        }

        const intervals = groupSlotsByInterval(slotsData);

        intervals.forEach((interval) => {
            const full = !interval.isAvailable || interval.availableCount === 0;
            const div = document.createElement('div');
            div.className = full
                ? 'border border-red-200 bg-red-50/30 rounded-xl p-3 cursor-not-allowed opacity-60'
                : 'border border-orange-100 hover:border-orange-300 hover:bg-orange-50/30 rounded-xl p-3 cursor-pointer transition-all duration-150';

            const allocatedSlotNo = interval.firstAvailableSlotNo > 0 ? interval.firstAvailableSlotNo : 1;

            div.innerHTML = `
            <div class="flex justify-between items-center">
                <p class="text-sm font-semibold ${full ? 'text-red-500' : 'text-gray-800'}">${interval.timeRange}</p>
                <div class="flex items-center" title="${interval.availableCount} of ${interval.totalCapacity} sub-slots available">
                    ${interval.subSlots.map(s => `<span class="inline-block w-2 h-2 rounded-full mx-0.5" style="background:${s.isAvailable && !interval.isExpired ? '#f97316' : '#e5e7eb'};"></span>`).join('')}
                </div>
            </div>
            <div class="mt-1 flex justify-between items-center text-xs">
                <span class="${full ? 'text-red-400 font-medium' : 'text-orange-600 font-medium'}">
                    ${interval.isExpired ? 'Time Expired' : (interval.availableCount === 0 ? 'Fully Booked' : `${interval.availableCount} of ${interval.totalCapacity} Available`)}
                </span>
            </div>
        `;

            if (!full) {
                div.addEventListener('click', () => {
                    selectedSlot = interval.timeRange;

                    slotList.querySelectorAll('.border-orange-500').forEach(el => {
                        el.classList.remove('border-orange-500', 'bg-orange-50/50');
                        el.classList.add('border-orange-100');
                    });

                    div.classList.remove('border-orange-100');
                    div.classList.add('border-orange-500', 'bg-orange-50/50');

                    const inputSheduledTime = document.getElementById('input_sheduled_time') as HTMLInputElement | null;
                    if (inputSheduledTime) inputSheduledTime.value = interval.timeRange;

                    const inputAppDate = document.getElementById('input_appointment_date') as HTMLInputElement | null;
                    if (inputAppDate) inputAppDate.value = appDate;

                    const inputIntervalId = document.getElementById('input_interval_id') as HTMLInputElement | null;
                    if (inputIntervalId) inputIntervalId.value = String(interval.intervalId);

                    const inputSlotNo = document.getElementById('input_slot_no') as HTMLInputElement | null;
                    if (inputSlotNo) inputSlotNo.value = String(allocatedSlotNo);

                    activateScheduleBtn(appDate, `${interval.timeRange} (Slot #${allocatedSlotNo})`);
                });
            }

            slotList.appendChild(div);
        });
    }

    // ── Schedule Button ───────────────────────────────────────
    function resetScheduleBtn() {
        if (!scheduleBtn) return;
        scheduleBtn.disabled = true;
        scheduleBtn.className = 'w-full py-3 rounded-xl text-sm font-semibold transition-all duration-200 bg-gray-200 text-gray-400 cursor-not-allowed';
    }

    function activateScheduleBtn(dateLabel: string, time: string) {
        if (!scheduleBtn) return;
        scheduleBtn.disabled = false;
        scheduleBtn.className = 'w-full py-3 rounded-xl text-sm font-semibold transition-all duration-200 bg-orange-500 hover:bg-orange-600 text-white cursor-pointer shadow-md shadow-orange-200';

        scheduleBtn.onclick = () => {
            swalConfirm(
                "Confirm Appointment Selection",
                `Would you like to select ${dateLabel} at ${time} for your appointment?`
            ).then((result) => {
                if (result.isConfirmed) {
                    confirmAppointment(dateLabel, time);
                }
            });
        };
    }

    // ── Confirm Selection ─────────────────────────────────────
    function confirmAppointment(dateLabel: string, time: string) {
        if (inputDate) inputDate.value = dateLabel;
        if (inputTime) inputTime.value = time;

        if (summaryText && summaryBar) {
            summaryText.textContent = `${dateLabel} at ${time}`;
            summaryBar.classList.remove('hidden');

            // Scroll to summary bar smoothly
            summaryBar.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        notyf.success("Appointment slot selected! Click 'Confirm & Submit' to submit.");
    }

    // ── Form Submission & Swal Response ────────────────────────
    const appointmentForm = document.getElementById('appointmentForm') as HTMLFormElement | null;
    const submitBtn = document.getElementById('submitAppointmentBtn') as HTMLButtonElement | null;

    appointmentForm?.addEventListener('submit', async (e) => {
        e.preventDefault();

        const btn = submitBtn || appointmentForm.querySelector('button[type="submit"]') as HTMLButtonElement | null;
        const originalBtnHtml = btn ? btn.innerHTML : '';

        try {
            if (btn) {
                btn.disabled = true;
                btn.classList.add('opacity-75', 'cursor-not-allowed');
                btn.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    Booking Appointment...
                `;
            }

            const formData = new FormData(appointmentForm);
            const applicantMobileNumbers = formData.getAll('applicant_mobile_numbers[]');

            const payload = {
                token_no: String(formData.get('token_no') || ''),
                user_id: Number(formData.get('user_id') || 1),
                office_id: Number(formData.get('office_id') || 15),
                shift_id: Number(formData.get('shift_id') || 1),
                appointment_date: String(formData.get('appointment_date') || ''),
                interval_id: Number(formData.get('interval_id') || 1),
                slot_no: Number(formData.get('slot_no') || 1),
                sheduled_time: String(formData.get('sheduled_time') || ''),
                applicant_mobile_numbers: applicantMobileNumbers.length
                    ? applicantMobileNumbers.map(n => String(n))
                    : ['9876543210']
            };

            const response = await fetch(appointmentForm.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content || ''
                },
                body: JSON.stringify(payload)
            });

            const data = await response.json();

            if (data.success) {
                const appt = data.appointment || {};
                await Swal.fire({
                    icon: 'success',
                    title: 'Appointment Booked!',
                    html: `
                        <div class="text-left text-sm text-gray-700 bg-orange-50/70 p-4 rounded-xl border border-orange-200 mt-3 space-y-2">
                            <div class="flex justify-between items-center pb-2 border-b border-orange-200">
                                <span class="text-gray-500 font-medium">Status:</span>
                                <span class="font-bold text-green-700 bg-green-100 px-2.5 py-0.5 rounded-full text-xs">Confirmed</span>
                            </div>
                            ${appt.token_no ? `
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500">Token Number:</span>
                                <span class="font-bold text-gray-900">${appt.token_no}</span>
                            </div>` : ''}
                            ${appt.appointment_id ? `
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500">Appointment ID:</span>
                                <span class="font-semibold text-gray-800">#${appt.appointment_id}</span>
                            </div>` : ''}
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500">Date:</span>
                                <span class="font-semibold text-gray-800">${appt.appointment_date || payload.appointment_date}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500">Time Slot:</span>
                                <span class="font-semibold text-gray-800">${appt.sheduled_time || payload.sheduled_time}</span>
                            </div>
                            ${appt.type_label ? `
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500">Type:</span>
                                <span class="font-semibold text-gray-800">${appt.type_label}</span>
                            </div>` : ''}
                        </div>
                        <p class="mt-3 text-xs text-gray-500">${data.message || 'Your appointment has been registered successfully.'}</p>
                    `,
                    confirmButtonText: 'Great, Continue',
                    confirmButtonColor: '#f97316',
                    allowOutsideClick: false,
                });
            } else {
                const cleanError = extractCleanErrorMessage(data.message || 'Unable to book appointment.');
                await Swal.fire({
                    icon: 'error',
                    title: 'Booking Failed',
                    text: cleanError,
                    confirmButtonText: 'Try Again',
                    confirmButtonColor: '#ef4444'
                });
            }
        } catch (err: any) {
            const cleanError = extractCleanErrorMessage(err.message || 'Unable to connect to appointment services. Please try again later.');
            await Swal.fire({
                icon: 'error',
                title: 'Service Unavailable',
                text: cleanError,
                confirmButtonText: 'Close',
                confirmButtonColor: '#ef4444'
            });
        } finally {
            if (btn) {
                btn.disabled = false;
                btn.classList.remove('opacity-75', 'cursor-not-allowed');
                btn.innerHTML = originalBtnHtml;
            }
        }
    });

    // ── Navigation ────────────────────────────────────────────
    prevMonthBtn?.addEventListener('click', () => {
        if (currentMonth === 0) {
            currentMonth = 11;
            currentYear--;
        } else {
            currentMonth--;
        }
        selectedDate = null;
        selectedSlot = null;
        if (slotPanel) slotPanel.classList.add('hidden');
        if (summaryBar) summaryBar.classList.add('hidden');
        renderCalendar();
    });

    nextMonthBtn?.addEventListener('click', () => {
        if (currentMonth === 11) {
            currentMonth = 0;
            currentYear++;
        } else {
            currentMonth++;
        }
        selectedDate = null;
        selectedSlot = null;
        if (slotPanel) slotPanel.classList.add('hidden');
        if (summaryBar) summaryBar.classList.add('hidden');
        renderCalendar();
    });

    // ── Init ──────────────────────────────────────────────────
    renderCalendar();

});


