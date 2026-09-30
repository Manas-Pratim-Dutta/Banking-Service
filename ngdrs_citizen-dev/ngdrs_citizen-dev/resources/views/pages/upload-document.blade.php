<x-layout-app>

    {{-- Page Banner --}}
    <!-- <x-banner title="Common Upload Document"
        description="Upload the required documents for all parties, witnesses, and identifier to proceed with registration."
        image="images/online-doc.png" /> -->

    {{-- Progress Steps --}}
    <x-progress-steps />

    {{-- Form Container --}}
    <div class="max-w-7xl mx-auto bg-white rounded-xl shadow-sm border border-gray-100 p-8">
        <div class="max-w-5xl mx-auto">

            {{-- Pre-Registration Number Header --}}
            <x-prereg :preRegNo="$preRegNo" />

            <x-section-divider />

            <x-section-title title="Upload Required Documents" icon="images/icons/generalinfo/filldetails.svg" />

            {{-- Upload All button --}}
            <div class="flex items-center justify-end mt-2 mb-6">
                <button type="button" id="uploadAllBtn"
                    class="flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white
                           bg-orange-500 hover:bg-orange-600 active:bg-orange-700 rounded-lg
                           transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                    </svg>
                    Upload All
                </button>
            </div>

            {{-- <form action="{{ url('api/documents/upload') }}" method="POST" enctype="multipart/form-data"
                id="uploadForm" class="space-y-5"> --}}
            <form id="uploadForm" enctype="multipart/form-data" data-token="{{ $token }}" class="space-y-5">
                @csrf

                {{-- ── NOC from DC ───────────────────────────────────────────── --}}
                <div class="bg-white border border-gray-200 rounded-2xl p-6 hover:border-orange-200 transition-colors">

                    <div class="flex items-center gap-2 mb-5">

                        <span class="w-1.5 h-5 rounded-full bg-orange-400"></span>

                        <h2 class="text-sm font-bold text-gray-800 uppercase tracking-wide">
                            Required Documents
                        </h2>

                    </div>


                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-6">


                        @foreach ($documents as $document)
                            <x-upload-field :documentId="$document->document_id" name="documents[{{ $document->document_id }}]"
                                label="{{ $document->document->document_name_en }}" :uploadedFile="$uploadedFiles[$document->document_id] ?? null"
                                tip="Allowed file format PDF/JPG/PNG. Maximum size {{ $document->document->file_size }} KB." />
                        @endforeach


                    </div>


                </div>

                {{-- ── Navigation ────────────────────────────────────────────── --}}
                <div class="pt-4 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <button type="button" onclick="window.history.back()"
                        class="flex items-center justify-center px-8 py-3 text-sm font-semibold text-orange-500 border border-orange-500 rounded-lg hover:bg-orange-50 transition-colors duration-200 cursor-pointer">
                        Previous Page
                    </button>
                    <button type="submit"
                        class="flex items-center justify-center gap-2 bg-orange-500 hover:bg-orange-600 active:bg-orange-700 text-white font-semibold text-sm px-8 py-3 rounded-lg shadow-md transition-colors duration-200 cursor-pointer">
                        Save & Continue
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                        </svg>
                    </button>
                </div>

            </form>

        </div>
    </div>

    {{-- ── Preview Modal ─────────────────────────────────────────────── --}}
    <div id="previewModal"
        class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 backdrop-blur-sm p-4" role="dialog"
        aria-modal="true" aria-labelledby="previewModalTitle">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 flex-shrink-0">
                <h3 id="previewModalTitle" class="text-sm font-semibold text-gray-700 truncate pr-4">File Preview</h3>
                <button onclick="closePreview()"
                    class="p-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition-colors"
                    aria-label="Close preview">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div id="previewContent" class="flex-1 overflow-auto p-4 bg-gray-50 min-h-0"></div>
        </div>
    </div>

    @push('page-scripts')
        @vite('resources/js/pages/upload/main.ts')
    @endpush

</x-layout-app>
