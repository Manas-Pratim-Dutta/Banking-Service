@props(['name', 'label', 'documentId', 'uploadedFile' => null, 'tip' => null, 'full' => false])

@php
    $inputId = 'file_' . $documentId;
@endphp

<div data-upload-wrapper data-document-id="{{ $documentId }}"
    class="{{ $full ? 'col-span-full' : '' }} flex flex-col gap-1.5">


    {{-- Label --}}
    <div class="flex items-center gap-1.5">

        <label for="{{ $inputId }}" class="text-sm font-medium text-gray-700 leading-none">
            {{ $label }}
        </label>


        @if ($tip)
            <div class="relative group/tip flex-shrink-0">

                <button type="button"
                    class="flex items-center justify-center w-4 h-4 rounded-full text-orange-400 border border-orange-300 hover:bg-orange-50">

                    <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z"
                            clip-rule="evenodd" />
                    </svg>

                </button>


                <div
                    class="absolute left-1/2 -translate-x-1/2 bottom-full mb-2 w-52 px-3 py-2
                    bg-gray-800 text-white text-xs rounded-lg shadow-lg z-10 opacity-0
                    pointer-events-none group-hover/tip:opacity-100">

                    {{ $tip }}

                </div>

            </div>
        @endif


        <span class="text-red-500 text-sm">*</span>


        <div data-view-file>
            {{-- Existing File View Link --}}
            @if ($uploadedFile)

                <a href="{{ route('documents.view', $uploadedFile->up_id) }}" target="_blank"
                    class="ml-2 text-orange-500 hover:text-orange-700 text-xs font-semibold">

                    View File

                </a>
                @endif

        </div>


    </div>



    {{-- Input row --}}
    <div data-upload-box class="flex items-center border border-gray-200 rounded-lg overflow-hidden bg-white">


        <label for="{{ $inputId }}"
            class="flex-shrink-0 cursor-pointer px-4 py-2 text-sm font-medium text-gray-700
            bg-gray-50 border-r border-gray-200 hover:bg-gray-100">

            Choose File

        </label>



        <input type="file" id="{{ $inputId }}" name="{{ $name }}" data-document-id="{{ $documentId }}"
            data-upload-input class="sr-only" accept=".pdf,.jpg,.jpeg,.png">



        <span data-file-name class="flex-1 px-3 text-sm text-gray-400 truncate">

            No file chosen

        </span>



        {{-- Upload --}}
        <button type="button" data-upload-btn
            class="hidden flex-shrink-0 items-center gap-1.5 px-3 py-1.5 mx-2 my-1 text-xs font-semibold
            text-white bg-orange-500 hover:bg-orange-600 rounded-md">

            Upload

        </button>



        {{-- Preview --}}
        <button type="button" data-preview-btn class="hidden px-2 py-2 text-gray-400 hover:text-orange-500">

            👁

        </button>



        {{-- Download --}}
        <button type="button" data-download-btn class="hidden px-2 py-2 text-gray-400 hover:text-orange-500">

            ↓

        </button>



        {{-- Clear --}}
        <button type="button" data-clear-btn class="hidden px-2 py-2 text-gray-400 hover:text-red-500">

            ✕

        </button>


    </div>



    {{-- Existing Uploaded filename --}}
    @if ($uploadedFile)

        <p class="text-xs text-gray-500">

            Uploaded:
            {{ $uploadedFile->input_fname }}

        </p>

    @endif



    {{-- Preview --}}
    <div data-inline-preview class="hidden mt-1 rounded-xl overflow-hidden border border-gray-200 bg-gray-50">
    </div>



    <p class="text-xs text-gray-400">
        File Size: 1MB
    </p>



    @error($name)
        <p class="text-xs text-red-500">
            {{ $message }}
        </p>
    @enderror


</div>
