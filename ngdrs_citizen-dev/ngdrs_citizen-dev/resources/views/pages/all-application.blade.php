<x-layout-app>

    {{-- Page content --}}
    <x-banner title="Submitted Applications" description="This page lists all document applications associated with your account.
    Select an application to view its details, status, and progress." image="images/online-doc.png" />
    <div class="flex flex-row gap-4 mb-6">

        <!-- All Applications -->
        <div
            class="flex items-center gap-4 bg-white rounded-2xl shadow-md px-6 py-5 flex-1 border-l-4 border-orange-500">
            <div class="bg-orange-100 p-3 rounded-xl">
                <svg class="w-7 h-7 text-orange-500" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12h6m-6 4h6m2 4H7a2 2 0 01-2-2V6a2 2 0 012-2h7l5 5v11a2 2 0 01-2 2z" />
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest">All Applications</p>
                <p class="text-3xl font-bold text-gray-800">07</p>
            </div>
        </div>

        <!-- Draft -->
        <div
            class="flex items-center gap-4 bg-white rounded-2xl shadow-md px-6 py-5 flex-1 border-l-4 border-orange-300">
            <div class="bg-orange-50 p-3 rounded-xl">
                <svg class="w-7 h-7 text-orange-400" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest">Draft</p>
                <p class="text-3xl font-bold text-gray-800">05</p>
            </div>
        </div>

        <!-- Submitted -->
        <div
            class="flex items-center gap-4 bg-white rounded-2xl shadow-md px-6 py-5 flex-1 border-l-4 border-green-400">
            <div class="bg-green-50 p-3 rounded-xl">
                <svg class="w-7 h-7 text-green-500" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest">Submitted</p>
                <p class="text-3xl font-bold text-gray-800">02</p>
            </div>
        </div>

    </div>
    <x-datatable :headers="[
        'Application No',
        'Applicant',
        'Service',
        'Status',
        'Submitted On'
    ]" :rows="[
        [
            'id' => 1001,
            'application_no' => 1001,
            'applicant' => 'Nitin Singh',
            'service' => 'Sale',
            'status' => 'Completed',
            'submitted_on' => '23/02/26',
        ],
        [
            'id' => 1002,
            'application_no' => 1002,
            'applicant' => 'Amit Das',
            'service' => 'Lease',
            'status' => 'Pending',
            'submitted_on' => '24/02/26',
        ],
        [
            'id' => 1003,
            'application_no' => 1003,
            'applicant' => 'Riya Sharma',
            'service' => 'Mortgage',
            'status' => 'In Progress',
            'submitted_on' => '25/02/26',
        ],
        [
            'id' => 1004,
            'application_no' => 1004,
            'applicant' => 'Sourav Paul',
            'service' => 'Gift Deed',
            'status' => 'Rejected',
            'submitted_on' => '26/02/26',
        ],
        [
            'id' => 1005,
            'application_no' => 1005,
            'applicant' => 'Ananya Roy',
            'service' => 'Sale',
            'status' => 'Completed',
            'submitted_on' => '27/02/26',
        ],
        [
            'id' => 1006,
            'application_no' => 1006,
            'applicant' => 'Debo Mahanta',
            'service' => 'Sale',
            'status' => 'Completed',
            'submitted_on' => '21/04/26',
        ],
        [
            'id' => 1007,
            'application_no' => 1007,
            'applicant' => 'Brijen Medhi',
            'service' => 'Sale',
            'status' => 'Completed',
            'submitted_on' => '17/01/26',
        ],
    ]" show-action action-label="View"
        action-route="all-applications" action-key="id" />



    <!-- when applications will be dynamic -->
    {{-- <x-datatable :headers="[
                'Application No',
                'Applicant',
                'Service',
                'Status',
                'Submitted On'
                ]" :rows="$applications" show-action action-label="View" action-route="all-applications"
        action-key="id" />
    --}}

    {{-- Page-specific JS (optional) --}}
    @push('page-scripts')

    @endpush

</x-layout-app>