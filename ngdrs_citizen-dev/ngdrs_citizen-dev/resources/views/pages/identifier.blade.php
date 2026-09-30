<x-layout-app>
    <!-- <x-banner title="Identifier Details"
        description="Provide accurate details about the witnesses, including their names, identification, occupation, and address. This information is vital for the legal validation of the document."
        image="images/Identifier-banner.png" /> -->
    <x-progress-steps />
    <form id="identifierForm">
        <div class="max-w-7xl mx-auto bg-white rounded-xl shadow-sm border border-gray-100 p-8">
            <div class="max-w-5xl mx-auto">

                <x-section-title title="Pre Registration No. -" :subtitle="$preRegistrationNo" />

                <x-section-divider />
                <div class="flex justify-end mb-5">
                    @if(!$ekycDone)
                                        <!-- <button
                            type="button"
                            id="addIdentifierBtn"
                            class="flex items-center gap-2 bg-orange-500 hover:bg-orange-600 active:bg-orange-700
                                   text-white font-semibold text-sm px-8 py-3 rounded-lg shadow-md
                                   transition-colors duration-200 cursor-pointer"
                        > -->
                                        <button type="button" id="addIdentifierBtn" class="flex items-center gap-2 bg-orange-500 hover:bg-orange-600 active:bg-orange-700 
                                   text-white font-semibold text-sm px-8 py-3 rounded-lg shadow-md 
                                   transition-colors duration-200 cursor-pointer
                                   {{ $existingIdentifier->count() >= 1 ? 'hidden' : '' }}">

                                            ADD

                                            <span class="flex items-center justify-center
                                       w-6 h-6 rounded-full bg-white/20
                                       text-base font-bold">
                                                +
                                            </span>

                                        </button>
                    @endif

                </div>
                <div id="identifierFormWrapper" class="{{ session()->has('auth_data') ? '' : 'hidden' }}">

                    <x-section-title title="Please Fill All the details"
                        icon="images/icons/generalinfo/filldetails.svg" />
                    @include(
                        'elements.identifier.dynamic-fields'
                    )

                    <div class="flex justify-end gap-4 mt-8">

                        <!-- Previous Page -->
                        <button type="button" onclick="window.history.back()" class="flex items-center justify-center px-8 py-3 text-sm font-semibold text-orange-500
                                border border-orange-500 rounded-lg
                                hover:bg-orange-50 transition-colors duration-200 hover:cursor-pointer">
                            Previous Page
                        </button>

                        <!-- Save and Continue -->
                        <button type="submit" class="flex items-center gap-2 bg-orange-500 hover:bg-orange-600 active:bg-orange-700
                                text-white font-semibold text-sm px-8 py-3 rounded-lg shadow-md
                                transition-colors duration-200 cursor-pointer">
                            Save and Continue
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                            </svg>
                        </button>

                    </div>
                </div>
    </form>
    @if($existingIdentifier->count())

        <div class="max-w-7xl mx-auto bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-4 mt-4">

            <h3 class="text-lg font-semibold mb-4">
                Identifier Details
            </h3>

            @foreach($existingIdentifier as $identifier)

                <details id="identifier-{{ $identifier->id }}"
                    class="group mb-4 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm transition-all duration-200 hover:shadow-md">

                    <summary
                        class="flex items-center justify-between cursor-pointer px-5 py-4 bg-gradient-to-r from-orange-50 to-white hover:from-orange-100 hover:to-orange-50 list-none">

                        <div class="flex flex-col">

                            <span class="font-semibold text-gray-800">
                                {{ $identifier->identification_full_name_en }}
                            </span>

                            <span class="text-sm text-gray-500">

                                @php

                                    $salutation =
                                        $masters['salutation'][$identifier->salutation]
                                        ?? '';

                                    $occupation =
                                        $masters['occupation_id'][$identifier->occupation_id]
                                        ?? '';

                                @endphp



                                @if($occupation)
                                    {{ $occupation }}
                                @endif

                            </span>

                        </div>

                        <svg class="w-5 h-5 text-orange-500 transition-transform duration-300 group-open:rotate-180" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>

                    </summary>
                    <div class="flex justify-end px-5 py-3 bg-white border-t border-gray-100">
                        <button type="button" class="delete-identifier-btn inline-flex items-center gap-2
                       border border-red-200 bg-red-50
                       text-red-600 hover:bg-red-100 hover:border-red-300
                       active:bg-red-200
                       font-semibold text-sm px-4 py-2 rounded-lg
                       transition-colors duration-200 cursor-pointer" data-id="{{ $identifier->id }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3m-7 0h10" />
                            </svg>
                            Delete
                        </button>
                    </div>

                    <div class="border-t border-gray-100 bg-gray-50 px-5 py-5">

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                            @foreach($fieldList as $field)

                                @php
                                    $name =
                                        $field->field_id_name_en;

                                    $value =
                                        $identifier->$name ?? '';
                                @endphp

                                @include(
                                    'elements.identifier.view-field',
                                    [
                                        'field' => $field,
                                        'value' => $value,
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

    @push('page-scripts')
        @vite('resources/js/pages/identifier/main.ts')
    @endpush
</x-layout-app>