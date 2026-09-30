<x-layout-app>

    {{-- Page content --}}
    <!-- <x-banner title="Party Details"
        description="This page collects detailed information about the parties involved in the land transaction, whether buying or selling. Provide accurate personal, identification, and contact details to ensure compliance with legal requirements and a smooth documentation process."
        image="images/online-doc.png" /> -->
    <x-progress-steps />

    @php if ($is_noc != 'Y') { @endphp
    <form id="partyForm">
        <div class="max-w-7xl mx-auto bg-white rounded-xl shadow-sm border border-gray-100 p-8 mb-4">
            <div class="max-w-5xl mx-auto">

                <x-section-title title="Pre Registration No. -" :subtitle="$preRegistrationNo" />

                <x-section-divider />



                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                    <x-select-input label="Select Party Type" name="select_party_type" :required="true"
                        :options="$partytype" id="partytype" tooltip="Select the document party type" />

                    <x-select-input label="Select Party Category" name="select_party_category" :required="true"
                        :options="[]" id="partycategory" tooltip="Select the document party category" />
                </div>

                <div id="exemptionFromAppearanceWrapper" class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5 hidden">
                    <x-select-input label="Exemption from Appearance under Article 88" name="exemption_from_appearance"
                        :required="true" :options="['Y' => 'Yes', 'N' => 'No']" id="exemptionFromAppearance"
                        tooltip="Select whether this party is exempt from appearance under Article 88" />
                </div>


                <div class="flex justify-end gap-4 mt-8">
                    <button type="button" id="addPartyBtn" data-action="add-party" class="flex items-center gap-2 bg-orange-500 hover:bg-orange-600 active:bg-orange-700
                                text-white font-semibold text-sm px-8 py-3 rounded-lg shadow-md
                                transition-colors duration-200 cursor-pointer">


                        ADD

                        <span class="flex items-center justify-center
                                        w-6 h-6 rounded-full bg-white/20
                                        text-base font-bold group-hover:rotate-90
                                        transition-transform duration-200">

                            +

                        </span>

                    </button>
                </div>
            </div>
        </div>



        <div id="partyFormWrapper" class="hidden">
            <div class="max-w-7xl mx-auto bg-white rounded-xl shadow-sm border border-gray-100 p-8">
                <div class="max-w-5xl mx-auto">

                    @include(
                        'elements.party.dynamic-fields'
                    )




                    <div class="flex justify-end gap-4 mt-8">


                        <button type="button" onclick="window.history.back()" class="flex items-center justify-center px-8 py-3 text-sm font-semibold text-orange-500
                                border border-orange-500 rounded-lg
                                hover:bg-orange-50 transition-colors duration-200 hover:cursor-pointer">
                            Previous Page
                        </button>


                        <button type="submit" id="savePartyBtn"
                            class="flex items-center gap-2 bg-orange-500 hover:bg-orange-600 active:bg-orange-700 text-white font-semibold text-sm px-8 py-3 rounded-lg shadow-md transition-colors duration-200 cursor-pointer">
                            Save and Continue
                        </button>

                    </div>
                </div>
            </div>


        </div>

    </form>
    @php } @endphp


    @if($existingParties->count())

        <div class="max-w-7xl mx-auto bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-4 mt-4">

            <h3 class="text-lg font-semibold mb-4">
                Party Details
            </h3>

            @foreach($existingParties as $party)

                <details id="party-{{ $party->id }}"
                    class="group mb-4 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm transition-all duration-200 hover:shadow-md">

                    <summary
                        class="flex items-center justify-between cursor-pointer px-5 py-4 bg-gradient-to-r from-orange-50 to-white hover:from-orange-100 hover:to-orange-50 list-none">

                        <!-- <div class="flex flex-col">

                                                            <span class="font-semibold text-gray-800">
                                                                {{ $party->party_full_name_en }}
                                                            </span>

                                                            <span class="text-sm text-gray-500">

                                                                {{ $party->party_type_name }}

                                                                |

                                                                {{ $party->party_category_name }}

                                                            </span>

                                                        </div> -->

                     
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 w-full">

    {{-- LEFT: PARTY DETAILS --}}
    <div class="flex items-center gap-3 min-w-0">

        {{-- Party avatar --}}
        <!-- <div class="flex-shrink-0 w-10 h-10 rounded-full
                    bg-indigo-50 text-indigo-600
                    flex items-center justify-center
                    font-semibold text-sm">
            {{ strtoupper(substr($party->party_full_name_en, 0, 1)) }}
        </div> -->

        <div class="min-w-0">

            <div class="font-semibold text-gray-800 truncate">
                {{ $party->party_full_name_en }}
            </div>

            <div class="text-sm text-gray-500 mt-0.5">
                {{ $party->party_type_name }}
                <span class="mx-1 text-gray-300">|</span>
                {{ $party->party_category_name }}
            </div>

            {{-- Presenter --}}
            <button
                type="button"
                class="presenter-btn mt-2 inline-flex items-center gap-1.5
                       px-3 py-1.5 rounded-md text-xs font-medium transition
                       {{ ($party->is_presenter ?? 'N') === 'Y'
                            ? 'bg-green-50 text-green-700 border border-green-200'
                            : 'bg-gray-50 text-gray-600 border border-gray-200 hover:bg-gray-100' }}"
                data-party-id="{{ $party->id }}"
                data-url="{{ route('party.set-presenter') }}"
            >

                @if(($party->is_presenter ?? 'N') === 'Y')

                    <svg class="w-3.5 h-3.5"
                         fill="none"
                         stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M5 13l4 4L19 7"/>
                    </svg>

                    Presenter

                @else

                    <svg class="w-3.5 h-3.5"
                         fill="none"
                         stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M12 4v16m8-8H4"/>
                    </svg>

                    Set as Presenter

                @endif

            </button>

        </div>

    </div>


    {{-- RIGHT: FORM 60/61 --}}
    @if(($party->pan_form_list ?? null) == 8)

        <div class="flex-shrink-0 md:w-72">

            <div class="flex items-center gap-3
                        px-3 py-2.5
                        rounded-lg
                        bg-gray-50
                        border border-gray-200">

                {{-- Document icon --}}
                <div class="flex-shrink-0 w-9 h-9 rounded-md
                            bg-white border border-gray-200
                            flex items-center justify-center
                            text-gray-500">

                    <svg class="w-5 h-5"
                         fill="none"
                         stroke="currentColor"
                         viewBox="0 0 24 24">

                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="1.7"
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0
                                 01-2-2V5a2 2 0 012-2h5.586
                                 a1 1 0 01.707.293l4.414
                                 4.414a1 1 0 01.293.707V19
                                 a2 2 0 01-2 2z"/>

                    </svg>

                </div>


                <div class="flex-1 min-w-0">

                    <div class="text-xs font-medium text-gray-700">
                        Form 60/61
                    </div>

                    <div class="text-[11px] text-gray-400">
                        PDF · Max 2 MB
                    </div>

                </div>


                {{-- Upload button --}}
                <label
                    class="flex-shrink-0 cursor-pointer
                           inline-flex items-center gap-1.5
                           px-3 py-1.5
                           rounded-md
                           bg-white
                           border border-gray-300
                           text-xs font-medium text-gray-700
                           hover:bg-gray-100
                           transition"
                >

                    <svg class="w-3.5 h-3.5"
                         fill="none"
                         stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M4 16v1a2 2 0
                                 002 2h12a2 2 0
                                 002-2v-1M12
                                 12V3m0 0L8
                                 7m4-4l4 4"/>
                    </svg>

                    Upload

                    <input
                        type="file"
                        class="party-file-input hidden"
                        data-party-id="{{ $party->id }}"
                        data-url="{{ route('party.upload-file') }}"
                        accept=".pdf,.jpg,.jpeg,.png"
                    >

                </label>

            </div>


            {{-- Upload status --}}
            <div
                class="file-upload-status mt-1.5 text-xs"
                data-status-party="{{ $party->id }}"
            ></div>


            {{-- Existing uploaded file --}}
            @if(!empty($party->uploaded_file))

                <div
                    class="existing-file mt-1.5 flex items-center gap-2"
                    data-existing-party="{{ $party->id }}"
                >

                    <svg class="w-3.5 h-3.5 text-green-600"
                         fill="none"
                         stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M5 13l4 4L19 7"/>
                    </svg>

                    <a
                        href="{{ asset('storage/' . $party->uploaded_file) }}"
                        target="_blank"
                        class="text-xs text-green-700 hover:text-green-800 hover:underline"
                    >
                        Form 60/61 uploaded · View
                    </a>

                </div>

            @endif

        </div>

    @endif

</div>


                        <!-- <div class="flex flex-col">

                                            <span class="font-semibold text-gray-800">
                                                {{ $party->party_full_name_en }}
                                            </span>

                                            <span class="text-sm text-gray-500">
                                                {{ $party->party_type_name }}
                                                |
                                                {{ $party->party_category_name }}
                                            </span>

                                            <button type="button" class="presenter-btn mt-2 w-fit px-3 py-1.5 rounded-md text-sm font-medium
                                            {{ ($party->is_presenter ?? 'N') === 'Y'
                                    ? 'bg-green-100 text-green-700'
                                    : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}" data-party-id="{{ $party->id }}"
                                                data-url="{{ route('party.set-presenter') }}">
                                                {{ ($party->is_presenter ?? 'N') === 'Y'
                                    ? 'Presenter'
                                    : 'Set as Presenter' }}
                                            </button>

                                        </div> -->

                        <svg class="w-5 h-5 text-orange-500 transition-transform duration-300 group-open:rotate-180" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>

                    </summary>
                    @php if ($is_noc != 'Y') { @endphp
                    <div class="flex justify-end px-5 py-3 bg-white border-t border-gray-100">
                        <button type="button"
                            class="delete-party-btn inline-flex items-center gap-2
                                                                                       border border-red-200 bg-red-50
                                                                                       text-red-600 hover:bg-red-100 hover:border-red-300
                                                                                       active:bg-red-200
                                                                                       font-semibold text-sm px-4 py-2 rounded-lg
                                                                                       transition-colors duration-200 cursor-pointer"
                            data-id="{{ $party->id }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6V7M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3m-7 0h10" />
                            </svg>
                            Delete
                        </button>
                    </div>
                    @php } @endphp
                    <div class="border-t border-gray-100 bg-gray-50 px-5 py-5">

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                            @foreach($party->viewFields as $field)

                                @include(
                                    'elements.party.view-field',
                                    [
                                        'field' => $field,
                                        'party' => $party,
                                        'masters' => $masters
                                    ]
                                )

                            @endforeach

                        </div>

                    </div>

                </details>

            @endforeach

        </div>

    @endif
    <script>
        window.partyEntry = @json($partyEntry ?? []);
        window.poa_applicable = @json($poa_applicable);
        window.missingPartyTypeNames = @json($missingPartyTypeNames ?? []);
    </script>
    <script>
        document.addEventListener('click', function (e) {

            const button = e.target.closest('.presenter-btn');

            if (!button) {
                return;
            }

            const partyId = button.dataset.partyId;
            const url = button.dataset.url;

            if (!partyId) {
                return;
            }

            button.disabled = true;
            button.innerText = 'Updating...';

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    party_id: partyId
                })
            })
                .then(response => response.json())
                .then(data => {

                    if (!data.success) {
                        throw new Error(data.message || 'Unable to update presenter.');
                    }

                    /*
                     * Make every button "Set as Presenter"
                     */
                    document.querySelectorAll('.presenter-btn').forEach(btn => {

                        btn.disabled = false;

                        btn.innerText = 'Set as Presenter';

                        btn.classList.remove(
                            'bg-green-100',
                            'text-green-700'
                        );

                        btn.classList.add(
                            'bg-gray-100',
                            'text-gray-700',
                            'hover:bg-gray-200'
                        );
                    });

                    /*
                     * Make selected party the presenter
                     */
                    const selectedButton = document.querySelector(
                        '.presenter-btn[data-party-id="' + partyId + '"]'
                    );

                    if (selectedButton) {

                        selectedButton.innerText = 'Presenter';

                        selectedButton.classList.remove(
                            'bg-gray-100',
                            'text-gray-700',
                            'hover:bg-gray-200'
                        );

                        selectedButton.classList.add(
                            'bg-green-100',
                            'text-green-700'
                        );

                        selectedButton.disabled = false;
                    }

                })
                .catch(error => {

                    console.error(error);

                    button.disabled = false;
                    button.innerText = 'Set as Presenter';

                    alert(error.message || 'Something went wrong.');
                });

        });
    </script>
    <script>
        document.addEventListener('change', function (e) {

            const input = e.target.closest('.party-file-input');

            if (!input) {
                return;
            }

            const file = input.files[0];

            if (!file) {
                return;
            }

            const partyId = input.dataset.partyId;
            const url = input.dataset.url;

            const statusElement = document.querySelector(
                '.file-upload-status[data-status-party="' + partyId + '"]'
            );

            /*
             * 2 MB
             */
            const maxSize = 2 * 1024 * 1024;

            /*
             * Client-side size check.
             *
             * This is only for user convenience.
             * The server also checks the size.
             */
            if (file.size > maxSize) {

                statusElement.innerHTML =
                    '<span class="text-red-600">' +
                    'File size must not exceed 2 MB.' +
                    '</span>';

                input.value = '';

                return;
            }

            /*
             * Allowed extensions.
             */
            const allowedExtensions = [
                'pdf',
                'jpg',
                'jpeg',
                'png'
            ];

            const extension = file.name
                .split('.')
                .pop()
                .toLowerCase();

            if (!allowedExtensions.includes(extension)) {

                statusElement.innerHTML =
                    '<span class="text-red-600">' +
                    'Only PDF, JPG, JPEG and PNG files are allowed.' +
                    '</span>';

                input.value = '';

                return;
            }

            /*
             * Disable while uploading.
             */
            input.disabled = true;

            statusElement.innerHTML =
                '<span class="text-blue-600">' +
                'Uploading...' +
                '</span>';

            /*
             * FormData is required for file upload.
             */
            const formData = new FormData();

            formData.append('party_id', partyId);
            formData.append('file', file);

            fetch(url, {
                method: 'POST',

                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },

                body: formData
            })
                .then(async response => {

                    const data = await response.json();

                    if (!response.ok || !data.success) {
                        throw new Error(
                            data.message || 'File upload failed.'
                        );
                    }

                    return data;
                })
                .then(data => {

                    statusElement.innerHTML =
                        '<span class="text-green-600">' +
                        'File uploaded successfully.' +
                        '</span>';

                    /*
                     * Remove old "View uploaded file" link.
                     */
                    const oldFile = document.querySelector(
                        '.existing-file[data-existing-party="' + partyId + '"]'
                    );

                    if (oldFile) {
                        oldFile.remove();
                    }

                    /*
                     * Add new View File link.
                     */
                    const linkContainer = document.createElement('div');

                    linkContainer.className = 'existing-file mt-2';

                    linkContainer.setAttribute(
                        'data-existing-party',
                        partyId
                    );

                    linkContainer.innerHTML =
                        '<a href="' + data.url + '"' +
                        ' target="_blank"' +
                        ' class="text-blue-600 hover:underline text-sm">' +
                        'View uploaded file' +
                        '</a>';

                    statusElement.parentNode.appendChild(linkContainer);

                    /*
                     * Reset input so the same file can be selected again
                     * if necessary.
                     */
                    input.value = '';

                })
                .catch(error => {

                    console.error('Upload error:', error);

                    statusElement.innerHTML =
                        '<span class="text-red-600">' +
                        error.message +
                        '</span>';

                    input.value = '';

                })
                .finally(() => {

                    input.disabled = false;

                });

        });
    </script>
    @push('page-scripts')
        @vite('resources/js/pages/party/main.ts')
    @endpush


</x-layout-app>