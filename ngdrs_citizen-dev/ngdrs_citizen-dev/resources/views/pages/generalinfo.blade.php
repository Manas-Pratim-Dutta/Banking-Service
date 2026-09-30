<x-layout-app>

    {{-- Page content --}}
    <!-- <x-banner title="General Information"
        description="This page lists all document applications associated with your account.
    Select an application to view its details, status, and progress."
        image="images/online-doc.png" /> -->

    <x-progress-steps />

    @if (!empty($token))
        <x-section-title title="Pre Registration No. -" :subtitle="$token" />

        <x-section-divider />
    @endif

    <form method="POST" action="{{ route('general-info') }}">
        @csrf
        <div class="max-w-7xl mx-auto bg-white rounded-xl shadow-sm border border-gray-100 p-8">
            <div class="max-w-5xl mx-auto">

                <x-section-title title="Please Fill All the details" icon="images/icons/generalinfo/filldetails.svg" />
                <x-select-input label="Article" name="article" :required="true" :options="$article" id="article-id"
                    tooltip="Select the document article type" />

                <div id="doc-title-wrapper" class="hidden">
                    <x-text-input label="Document Title" name="document_title" id="document-title" readonly />
                </div>
                <div id="lease-type-wrapper" class="hidden">
                    <x-select-input label="Lease Type" name="lease_type" id="lease-type" :options="[
                        'private' => 'Private Lease',
                        'govt' => 'Govt Lease',
                        'sub' => 'Sub Lease',
                    ]" />
                </div>

                <!-- Hidden: Title ID -->
                <input type="hidden" name="document_title_id" id="document-title-id" />

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">

                    <x-select-input label="Document Execution Type" name="document_execution_type"
                        id="document-execution-type" :required="true" tooltip="Type of document execution">
                        <option value="1" selected>Document Executed In India</option>
                        <option value="2">Document Executed Outside India</option>
                        <option value="3">Court Decree</option>
                    </x-select-input>

                    <x-date-input label="Date Of Execution" name="date_of_execution" :required="true"
                        tooltip="Execution date of the document" :value="now()->format('Y-m-d')" />

                    {{-- Entry Date In India --}}
                    <div id="entry-date-wrapper" class="hidden">
                        <x-date-input label="Entry Date In India" name="entry_date_in_india" id="entry-date-in-india"
                            tooltip="Select entry date in India" />
                    </div>
                    {{-- Court Order Date --}}
                    <div id="court-order-date-wrapper" class="hidden">
                        <x-date-input label="Court Order Date" name="court_order_date"
                            tooltip="Select court order date" />
                    </div>

                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                    <x-select-input label="Registration By Court Order" name="court_case" id="court-case" :required="true"
                        tooltip="Select the relevant court case">
                        <option value="1" selected>No</option>
                        <option value="2">Yes</option>
                    </x-select-input>

                    {{-- court case order date --}}
                    <div id="court-date-wrapper" class="hidden">
                        <x-date-input label="Court Order Date" name="court-case_order_date" id="court-order-date"
                            tooltip="" />
                    </div>
                    {{-- Court Order Number --}}
                    <div id="court-order-number-wrapper" class="hidden">
                        <x-text-input label="Court Order Number" placeholder="Enter Court Order Number"
                            name="court_order_number" id="court-order-number-wrapper" tooltip="" />
                    </div>

                    <x-select-input label="Deed Writer Type" name="deed_writer_type" id="deed-writer-type"
                        :required="true" tooltip="Full name of the document / deed writer">
                        <option value="self" selected>Self</option>
                        <option value="Deed Writer">Deed Writer</option>
                        <option value="advocate name">Advocate</option>
                    </x-select-input>

                    <div id="number-of-pages" class="hidden">
                        <x-number-input label="No. of Pages" name="no_of_pages" placeholder="Enter the no. of pages"
                            :required="true" tooltip="Total number of pages" />
                    </div>

                    <div id="advocate-name" class="hidden">
                        <x-text-input label="Advocate Name" name="advocate_name" placeholder="Advocate Name"
                            :required="true" tooltip="Full name of the advocate in English" />
                    </div>

                    <div id="document_writer_name" class="hidden">
                        <x-text-input label="Name of The Document Writer / Deed Writer" name="document_writer_name"
                            placeholder="Full name of the document / deed writer" :required="true"
                            tooltip="Full name of the document / deed writer" />
                    </div>
                </div>

                <x-section-divider />
                <x-section-title title="Import data from previously registered Document"
                    icon="images/icons/generalinfo/import.svg" />

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">

                    <x-text-input label="Reference Doc No." name="reference_doc_no" placeholder="Enter Reference Doc No"
                        :required="true" />


                    {{-- <x-select-input label="Link Office Name" name="link_office_name" :required="true"
                        tooltip="Select the linked office" /> --}}
                </div>

                {{-- <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">

                    <x-date-input label="Link Document Date" name="link_document_date" :required="true"
                        tooltip="Date of the linked document" />

                    <x-date-input label="Reference/Registered Doc.Date" name="reference_registered_doc_date"
                        :required="true" tooltip="Date of the registered document" />
                </div>
                <div>
                    <x-text-input label="SRO Office Document Number" name="sro_office_document_number"
                        placeholder="Enter SRO Office Document Number" :required="true"
                        tooltip="SRO office document number" />
                </div> --}}
                <x-section-divider />
                <x-section-title title="Document Submission Office Details"
                    icon="images/icons/generalinfo/docsubmit.svg" />
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">

                    <x-select-input id="district_id" label="District" :options="$district" name="district"
                        :required="true" tooltip="Select district" />

                    <x-select-input id="taluka_id" label="Circle" name="circle" :required="true"
                        tooltip="Select circle" />

                    <x-select-input id="office_id" label="Office Name" name="office_name" :required="true" />
                </div>
                <x-section-divider />
                <x-section-title title="Home Visit Details" icon="images/icons/generalinfo/homevisit.svg" />
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                    <x-select-input id="is_home_visit" label="Is Home Visit" name="is_home_visit" :required="true"
                        tooltip="Is a home visit required?">
                        <!-- <option value="yes" @selected(old('is_home_visit') === 'yes')>Yes</option> -->
                        <option value="Y">Yes</option>
                        <option value="N" selected>No</option>
                    </x-select-input>

                    <div id="home-visit-address-wrapper" class="hidden">
                        <x-text-input label="Home Visit Address" name="home_visit_address"
                            placeholder="Enter the home address" :required="true"
                            tooltip="Full address for the home visit" />
                    </div>
                </div>

                <div class="flex justify-end mt-8">
                    <button type="submit"
                        class="flex items-center gap-2 bg-orange-500 hover:bg-orange-600 active:bg-orange-700 text-white font-semibold text-sm px-8 py-3 rounded-lg shadow-md transition-colors duration-200 cursor-pointer">
                        Save and Next
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </form>
    <div id="noc-config" data-enabled="{{ $isNocFlow ? '1' : '0' }}" {{--
        data-district-id="{{ $nocData['district_id'] ?? '' }}" data-circle-id="{{ $nocData['circle_id'] ?? '' }}" --}}
        data-district-id="{{ $nocData['data']['district_id'] ?? '' }}"
        data-circle-id="{{ $nocData['data']['circle_id'] ?? '' }}">
    </div>

    @push('page-scripts')
        {{-- <!-- @vite('resources/js/pages/generalinfo.ts') --> --}}
        @vite('resources/js/pages/generalinfo/main.ts')
    @endpush
</x-layout-app>
