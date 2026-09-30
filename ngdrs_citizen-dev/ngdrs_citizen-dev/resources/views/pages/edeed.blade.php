<x-layout-app>

    {{-- Page-specific CSS (optional) --}}
    @push('page-styles')

    @endpush
    <x-progress-steps />
    {{-- Page content --}}
    <div class="card">
        <x-text-input label="Pre Reg No." name="document_execution_type" id="token"
            placeholder="Enter Document Execution Type" :required="true" tooltip="Type of document execution"
            :value="$token" :readonly="true" />
        <x-text-input label="Article" name="article" placeholder="Enter Document Execution Type" :required="true"
            tooltip="Type of document execution" value="{{ $article->article_desc_en ?? '' }}" :readonly="true" />
        <x-select-input label="Select Language" name="language" id="language" :required="true" :options="[
        'english' => 'English',
        'assamese' => 'Assamese',
        'bengali' => 'Bengali'
    ]" />


        <div id="article-fields-container" class="mt-4"></div>
        <div id="article-details-section" class="mt-6 hidden">

            <div class="flex justify-end mb-3">
                <button type="button" id="addArticleDetail" class="inline-flex items-center gap-2
                   px-5 py-2.5
                   rounded-xl
                   bg-blue-600
                   text-white
                   text-sm font-semibold
                   shadow-sm
                   hover:bg-blue-700
                   hover:shadow-md
                   active:scale-[0.98]
                   transition-all">

                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>

                    Add Detail
                </button>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">

                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex items-center justify-between">

                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">
                                Article Details
                            </h3>

                            <p class="text-sm text-gray-500 mt-1">
                                Saved transaction details
                            </p>
                        </div>

                        <span id="articleDetailsCount" class="inline-flex items-center justify-center
                       min-w-[32px] h-7 px-2.5
                       rounded-full
                       bg-blue-100 text-blue-700
                       text-xs font-bold">
                            0
                        </span>

                    </div>
                </div>

                <div id="articleDetailsTableWrapper" class="overflow-x-auto">

                    <table id="articleDetailsTable" class="w-full text-sm">

                        <thead id="articleDetailsTableHead" class="bg-gray-50 border-b border-gray-200">
                        </thead>

                        <tbody id="articleDetailsTableBody" class="divide-y divide-gray-100 bg-white">
                        </tbody>

                    </table>

                    <div id="articleDetailsEmptyState" class="hidden px-6 py-12 text-center">

                        <p class="text-sm font-medium text-gray-700">
                            No article details added
                        </p>

                        <p class="text-xs text-gray-400 mt-1">
                            Click "Add Detail" to add a transaction
                        </p>

                    </div>

                </div>

            </div>
        </div>
        <div id="editor-wrapper" class="hidden mt-4 mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Document Content
            </label>

            <div id="editor" style="height: 400px;"></div>

            <input type="hidden" id="editor-content" name="editor_content">
        </div>
        <div class="flex items-center justify-end gap-4">
            <button type="button" id="ai-summary-btn" class="hidden items-center gap-2 px-8 py-3 rounded-xl
       bg-gradient-to-r from-violet-600 to-orange-500
       text-white font-semibold shadow-lg
       hover:shadow-xl hover:scale-[1.02]
       transition-all duration-200 cursor-pointer">
                AI Summary
            </button>

            <!-- <x-ui.button label="Generate E-deed" wrapperClass="flex justify-end"
                class="w-sm flex justify-center items-center hidden" id="gen-edeed-btn" /> -->

            @if($download_status === 'Y')
                <x-ui.button label="Download E-deed" wrapperClass="flex justify-end"
                    class="w-sm flex justify-center items-center" id="download-edeed-btn" />
            @else
                <x-ui.button label="Generate E-deed" wrapperClass="flex justify-end"
                    class="w-sm flex justify-center items-center hidden" id="gen-edeed-btn" />
            @endif
            @if($regenerate_flag === 'Y')
                <x-ui.button label="Regenerate" wrapperClass="flex justify-end"
                    class="text-xs px-8 py-4 flex justify-center items-center" id="regenerate-edeed-btn" />
            @endif
        </div>
        <!-- Summary Modal -->
        <div id="ai-summary-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden
           flex items-center justify-center z-50">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl mx-4">

                <div class="border-b p-5 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-full bg-gradient-to-r from-violet-600 to-orange-500 flex items-center justify-center text-white">
                            AI
                        </div>

                        <div>
                            <h3 class="font-bold text-lg">AI Generated Summary</h3>
                            <p class="text-sm text-gray-500">
                                Key highlights extracted from the Sale Deed
                            </p>
                        </div>
                    </div>

                    <button id="close-ai-summary" class="text-gray-500 hover:text-gray-700 text-xl cursor-pointer">
                        ×
                    </button>
                </div>

                <div class="p-8 max-h-[75vh] overflow-y-auto bg-[#faf9f6] relative">

                    <!-- Watermark -->
                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none opacity-5">
                        <img src="/images/banner-img.png" class="w-96 h-96 object-contain">
                    </div>

                    <div class="relative z-10">

                        <!-- Government Header -->
                        <div class="text-center border-b-4 border-gray-800 pb-4">

                            <img src="/images/banner-img.png" class="w-20 h-20 mx-auto mb-2">

                            <h2 class="text-xl font-bold tracking-wide uppercase">
                                Government of Assam
                            </h2>

                            <h3 class="text-lg font-semibold">
                                National Generic Document Registration System (NGDRS)
                            </h3>

                            <p class="text-sm text-gray-600">
                                AI Generated Deed Summary
                            </p>
                        </div>

                        <!-- Reference Section -->
                        <div class="mt-4 border rounded-lg overflow-hidden">
                            <table class="w-full text-sm">
                                <tr class="border-b">
                                    <td class="font-semibold p-2 bg-gray-100 w-1/4">
                                        Pre Registration No.
                                    </td>
                                    <td class="p-2">{{ $token }}</td>

                                    <td class="font-semibold p-2 bg-gray-100 w-1/4">
                                        Article
                                    </td>
                                    <td class="p-2">Sale</td>
                                </tr>

                                <tr>
                                    <td class="font-semibold p-2 bg-gray-100">
                                        Summary Generated
                                    </td>
                                    <td class="p-2">19 June 2026</td>


                                </tr>
                            </table>
                        </div>

                        <!-- Title -->
                        <div class="text-center mt-8 mb-6">
                            <h2 class="text-lg font-bold uppercase underline">
                                Summary of Sale Deed
                            </h2>
                        </div>

                        <!-- Overview -->
                        <div class="mb-6">
                            <h3 class="font-bold uppercase border-b pb-2 mb-3">
                                1. Transaction Overview
                            </h3>

                            <p class="text-sm leading-8 text-justify">
                                This Sale Deed records the transfer of ownership of a residential
                                land parcel from
                                <strong>Nitin Singh</strong>
                                (Seller) to
                                <strong>Jyoti Bordoloi</strong>
                                (Purchaser) for a total consideration amount of
                                <strong>₹45,00,000</strong>.
                            </p>
                        </div>

                        <!-- Parties -->
                        <div class="mb-6">
                            <h3 class="font-bold uppercase border-b pb-2 mb-3">
                                2. Parties to the Transaction
                            </h3>

                            <table class="w-full border text-sm">
                                <tr class="border-b">
                                    <td class="p-3 bg-gray-100 font-semibold w-1/4">
                                        Seller
                                    </td>
                                    <td class="p-3">
                                        Nitin Singh
                                    </td>
                                </tr>

                                <tr>
                                    <td class="p-3 bg-gray-100 font-semibold">
                                        Purchaser
                                    </td>
                                    <td class="p-3">
                                        Amlan Jyoti Bordoloi
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <!-- Property -->
                        <div class="mb-6">
                            <h3 class="font-bold uppercase border-b pb-2 mb-3">
                                3. Property Details
                            </h3>

                            <table class="w-full border text-sm">
                                <tr class="border-b">
                                    <td class="p-3 bg-gray-100 font-semibold">Property Type</td>
                                    <td class="p-3">Residential Land</td>
                                </tr>

                                <tr class="border-b">
                                    <td class="p-3 bg-gray-100 font-semibold">Location</td>
                                    <td class="p-3">
                                        Beltola, Dispur Revenue Circle,
                                        Kamrup Metropolitan
                                    </td>
                                </tr>

                                <tr class="border-b">
                                    <td class="p-3 bg-gray-100 font-semibold">Area</td>
                                    <td class="p-3">2 Katha 10 Lecha</td>
                                </tr>

                                <tr class="border-b">
                                    <td class="p-3 bg-gray-100 font-semibold">Dag No.</td>
                                    <td class="p-3">1125</td>
                                </tr>

                                <tr>
                                    <td class="p-3 bg-gray-100 font-semibold">Patta No.</td>
                                    <td class="p-3">87</td>
                                </tr>
                            </table>
                        </div>

                        <!-- Financial -->
                        <div class="mb-6">
                            <h3 class="font-bold uppercase border-b pb-2 mb-3">
                                4. Financial Particulars
                            </h3>

                            <table class="w-full border text-sm">
                                <tr class="border-b">
                                    <td class="p-3 bg-gray-100 font-semibold">
                                        Sale Consideration
                                    </td>
                                    <td class="p-3">₹45,00,000</td>
                                </tr>

                                <tr class="border-b">
                                    <td class="p-3 bg-gray-100 font-semibold">
                                        Market Value
                                    </td>
                                    <td class="p-3">₹48,50,000</td>
                                </tr>

                                <tr class="border-b">
                                    <td class="p-3 bg-gray-100 font-semibold">
                                        Stamp Duty
                                    </td>
                                    <td class="p-3">₹2,42,500</td>
                                </tr>

                                <tr>
                                    <td class="p-3 bg-gray-100 font-semibold">
                                        Registration Fee
                                    </td>
                                    <td class="p-3">₹48,500</td>
                                </tr>
                            </table>
                        </div>

                        <!-- Legal -->
                        <div class="mb-6">
                            <h3 class="font-bold uppercase border-b pb-2 mb-3">
                                5. Legal Highlights
                            </h3>

                            <ol class="list-decimal pl-8 text-sm space-y-2">
                                <li>The Seller confirms clear and marketable title.</li>
                                <li>The property is free from encumbrances and litigation.</li>
                                <li>Vacant possession has been delivered to the Purchaser.</li>
                                <li>The Seller indemnifies the Purchaser against future title disputes.</li>
                            </ol>
                        </div>

                        <!-- Regulatory -->
                        <div class="mb-6">
                            <h3 class="font-bold uppercase border-b pb-2 mb-3">
                                6. Regulatory Information
                            </h3>

                            <table class="w-full border text-sm">
                                <tr class="border-b">
                                    <td class="p-3 bg-gray-100 font-semibold">NOC Number</td>
                                    <td class="p-3">NOC/KAM/2026/45879</td>
                                </tr>

                                <tr>
                                    <td class="p-3 bg-gray-100 font-semibold">NOC Date</td>
                                    <td class="p-3">15 June 2026</td>
                                </tr>
                            </table>
                        </div>

                        <!-- Footer -->
                        <div class="mt-10 border-t pt-4">
                            <p class="text-xs text-gray-500 text-center">
                                This summary has been generated automatically by the NGDRS AI
                                Assistant and is intended for informational purposes only.
                                The registered deed document shall prevail in case of any discrepancy.
                            </p>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <!-- E-Sign Modal -->
        <!-- <div id="esign-modal"
            class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden flex items-center justify-center z-50">

            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl mx-4">

                <div class="border-b p-5 flex justify-between items-center">
                    <div>
                        <h3 class="text-xl font-bold">Party e-Sign Verification</h3>
                        <p class="text-sm text-gray-500">
                            All parties must complete e-Sign before deed execution.
                        </p>
                    </div>

                    <button id="close-esign-modal" class="text-gray-500 hover:text-gray-700 text-2xl">
                        ×
                    </button>
                </div>

                <div class="p-6">

                    <table class="w-full border border-gray-300">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border p-3 text-left">Party Name</th>
                                <th class="border p-3 text-left">Party Type</th>
                                <th class="border p-3 text-left">Status</th>
                                <th class="border p-3 text-center">Action</th>
                            </tr>
                        </thead>

                        <tbody id="party-table-body">
                    </table>

                </div>

            </div>

        </div> -->
        <div id="esign-modal"
            class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden flex items-center justify-center z-50">

            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl mx-4 max-h-[85vh] flex flex-col">

                <div class="border-b p-5 flex justify-between items-center shrink-0">
                    <div>
                        <h3 class="text-xl font-bold">Party e-Sign Verification</h3>
                        <p class="text-sm text-gray-500">
                            All parties must complete e-Sign before deed execution.
                        </p>
                    </div>

                    <button id="close-esign-modal" class="text-gray-500 hover:text-gray-700 text-2xl">
                        ×
                    </button>
                </div>

                <div class="p-6 overflow-y-auto">

                    <div class="max-h-[50vh] overflow-y-auto border border-gray-300 rounded">
                        <table class="w-full border-collapse">
                            <thead class="sticky top-0 bg-gray-100 z-10">
                                <tr>
                                    <th class="border p-3 text-left">Party Name</th>
                                    <th class="border p-3 text-left">Party Type</th>
                                    <th class="border p-3 text-left">Status</th>
                                    <th class="border p-3 text-center">Action</th>
                                </tr>
                            </thead>

                            <tbody id="party-table-body"></tbody>
                        </table>
                    </div>

                </div>

            </div>

        </div>
        <div id="preview-modal" class="hidden fixed inset-0 z-50 bg-black/50">

            <div class="bg-white h-full w-full">

                <div class="p-4 border-b flex justify-between">

                    <h2 class="font-bold">
                        Signed Deed Preview
                    </h2>

                    <button id="close-preview">
                        Close
                    </button>

                </div>

                <iframe id="preview-frame" class="w-full h-[90vh]">
                </iframe>

            </div>

        </div>

    </div>

    {{-- Page-specific JS (optional) --}}
    @push('page-scripts')
        @vite('resources/js/pages/edeed/main.ts')
    @endpush

</x-layout-app>
<script>
    window.articleId = @json($article->article_id ?? null);

    window.edeedParties = @json($existingParties);
    window.edeedWitnesses = @json($existingWitnesses);
    window.edeedIdentifiers = @json($existingIdentifier);
    window.edeedExistingLanguageType = @json($existingLanguageType ?? null);
    window.edeedRoutes = {
        articleFields: @json(route('edeed.article-fields')),
        saveArticleDetails: @json(route('edeed.article-details')),
        deleteArticleDetails: @json(route('edeed.delete-article-details')),


    };
    window.edeedRoutes = {
        ...(window.edeedRoutes || {}),
        generateDeed: "{{ route('edeed.generate') }}",
    };
</script>