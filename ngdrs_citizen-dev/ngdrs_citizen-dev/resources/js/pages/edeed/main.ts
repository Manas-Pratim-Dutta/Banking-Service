import jsPDF from "jspdf";
import html2canvas from "html2canvas";
declare const Quill: any;
import { notyf } from "../../utils/ui/notyf";
import { initTomSelect } from "../../global";
import api from "../../utils/http/axios";
const templates: Record<string, string> = {
    english: `
    <h2 style="text-align:center;">SALE DEED</h2>

<p>
    This Deed of Sale is executed on the 19th day of June, 2026, by and between <strong>Nitin Singh</strong>, aged 28 years, son of Rajesh Singh, residing at House No. 15, G.S. Road, Guwahati, District Kamrup Metropolitan, Assam, PIN 781005, hereinafter referred to as the <strong>"Seller"</strong>, of the First Part.
</p>
<p>
    In favour of
</p>
<p>
    <strong>Amlan Jyoti Bordoloi</strong>, aged 26 years, daughter of Pradip Bordoloi, residing at House No. 42, Zoo Road, Guwahati, District Kamrup Metropolitan, Assam, PIN 781024, hereinafter referred to as the <strong>"Purchaser"</strong>, of the Second Part.
</p>
<p>
    Whereas, the Seller is the sole and absolute owner of the immovable property situated at Village Beltola, Mouza Beltola, Revenue Circle Dispur, District Kamrup Metropolitan, Assam, measuring 2 Katha 10 Lecha, having acquired the same through a registered Sale Deed No. 4587/2018 registered before the Sub-Registrar, Guwahati.
</p>
<p>
    Whereas, the Seller is in peaceful possession and enjoyment of the said property and has full right, title, and authority to sell and transfer the same.
</p>
<p>
    Whereas, the Purchaser has approached the Seller with an offer to purchase the said property and the Seller has agreed to sell the same for a total consideration of <strong>Rs. 45,00,000/- (Rupees Forty Five Lakh Only)</strong>.
</p>
<p>
    Whereas, the Purchaser has agreed to purchase the said property and has paid the entire sale consideration to the Seller.
</p>
<p>
    NOW THIS DEED WITNESSETH AS FOLLOWS:
</p>
<p>
    <strong>1.</strong> In consideration of the sum of Rs. 45,00,000/- (Rupees Forty Five Lakh Only) paid by the Purchaser to the Seller, the receipt whereof the Seller hereby acknowledges, the Seller hereby grants, conveys, transfers, and assigns absolutely all rights, title, and interest in the Schedule Property unto the Purchaser.
</p>
<p>
    <strong>2.</strong> The Seller declares that the property is free from all encumbrances, mortgages, liens, charges, claims, litigation, acquisition proceedings, and adverse interests whatsoever.
</p>
<p>
    <strong>3.</strong> The Seller further confirms that all land revenue, taxes, and other dues payable in respect of the property have been paid up to date.
    </p>
<p>
    <strong>4.</strong> Vacant and peaceful possession of the property has been delivered to the Purchaser on the execution of this Sale Deed.
</p>
<p>
    <strong>5.</strong> The Purchaser shall henceforth be the sole and absolute owner of the property and shall be entitled to possess, enjoy, mortgage, lease, transfer, gift, or otherwise deal with the property as deemed fit.
</p>
<p>
    <strong>6.</strong> The Seller agrees to indemnify the Purchaser against any future claim, dispute, loss, or liability arising out of any defect in title.
</p>
<p>
    <strong>7.</strong> The parties agree that this Sale Deed shall be binding upon their respective heirs, successors, legal representatives, and assigns.
</p>
<h3>Schedule of Property</h3>
<p>
    Property Type: Residential Land<br>
    District: Kamrup Metropolitan<br>
    Revenue Circle: Dispur<br>
    Mouza: Beltola<br>
    Village/Town: Beltola<br>
    Dag No.: 1125<br>
    Patta No.: 87<br>
    Area: 2 Katha 10 Lecha<br>
    Sub-Registrar Office: Guwahati<br>
    Police Station: Dispur<br>
    Post Office: Beltola<br>
    PIN Code: 781028
</p>
<p>
    <strong>Description of Property:</strong><br>
    A plot of residential land measuring 2 Katha 10 Lecha together with all easementary rights, pathways, and appurtenances attached thereto, situated at Beltola under Dispur Revenue Circle, Kamrup Metropolitan District, Assam.
</p>
<p>
    <strong>Boundaries:</strong><br>
    East : Property of Mr. Anup Kalita<br>
    West : 20 Feet Wide Public Road<br>
    North : Property of Mrs. Rina Das<br>
    South : Open Community Land
</p>
<p>
    Market Value of Property: Rs. 48,50,000/- (Rupees Forty Eight Lakh Fifty Thousand Only)
</p>
<p>
    Stamp Duty Paid: Rs. 2,42,500/-<br>
    Registration Fee Paid: Rs. 48,500/-
</p>
<p>
    NOC Number: NOC/KAM/2026/45879<br>
    NOC Date: 15 June 2026
</p>
<p>
    IN WITNESS WHEREOF, the parties have signed and executed this Sale Deed on the date first mentioned above in the presence of the witnesses mentioned below.
</p>
<br>
<table style="width:100%;">
    <tr>
        <td>
            <strong>SELLER</strong><br><br>
            Nitin Singh
        </td>
        <td>
            <strong>PURCHASER</strong><br><br>
            Amlan Jyoti Bordoloi
        </td>
    </tr>
</table>

<p>
    <strong>Witnesses:</strong>
</p>
<p>
    1. Rahul Sharma, S/o Manoj Sharma, R.G. Baruah Road, Guwahati, Assam.
</p>
<p>
    2. Priyanka Das, D/o Dipak Das, Six Mile, Guwahati, Assam.
</p>
`,
    assamese: `
        <h2>অসমীয়া দলিল</h2>
        <p>এইটো এটা নমুনা অসমীয়া বিষয়বস্তু।</p>
        `,
    bengali: `
        <h2>বাংলা দলিল</h2>
        <p>এটি একটি নমুনা বাংলা বিষয়বস্তু।</p>
        `,
};

document.addEventListener("DOMContentLoaded", () => {
    const languageSelect = document.getElementById(
        "language",
    ) as HTMLSelectElement | null;
    const languageTypeToKey: Record<string, string> = {
        E: "english",
        A: "assamese",
        B: "bengali",
    };
    languageSelect.addEventListener("click", function () {
        console.log("xx");
        return;
    });
    const aiSummaryBtn = document.getElementById("ai-summary-btn");
    const generateBtn = document.getElementById("gen-edeed-btn");
    console.log("lesGO");
    const language = document.getElementById(
        "language",
    ) as HTMLSelectElement | null;

    const editorWrapper = document.getElementById("editor-wrapper");

    const hiddenInput = document.getElementById(
        "editor-content",
    ) as HTMLInputElement | null;

    const quill = new Quill("#editor", {
        theme: "snow",
        placeholder: "Document content...",
        modules: {
            toolbar: [
                ["bold", "italic", "underline"],
                [{ header: [1, 2, 3, false] }],
                [{ list: "ordered" }, { list: "bullet" }],
                ["clean"],
            ],
        },
    });
    const articleFieldsContainer = document.getElementById(
        "article-fields-container",
    );

    // ============================================================
    // COMMON ARTICLE TRANSACTION MANAGER
    // Add / Table / Delete / Save are common for all articles.
    // Article blades only provide field configuration.
    // ============================================================

    let articleTransactions: any[] = [];

    function getArticleConfig(): any {
        return (window as any).articleTransactionConfig || null;
    }

    function getArticleInput(
        selector: string,
    ): HTMLInputElement | HTMLSelectElement | null {
        return document.querySelector(selector) as
            | HTMLInputElement
            | HTMLSelectElement
            | null;
    }

    function getCsrfToken(): string {
        return (
            document
                .querySelector('meta[name="csrf-token"]')
                ?.getAttribute("content") || ""
        );
    }

    function escapeArticleHtml(value: any): string {
        const div = document.createElement("div");
        div.textContent =
            value === null || value === undefined ? "" : String(value);
        return div.innerHTML;
    }

    function getPartyName(partyId: any): string {
        const config = getArticleConfig();
        const selector = config?.inputs?.party_id || config?.selectors?.party;
        if (!selector) return "";

        const select = getArticleInput(selector) as HTMLSelectElement | null;
        if (!select) return "";

        const option = select.options[select.selectedIndex];
        return option?.text?.trim() || "";
    }

    function getTransactionTypeLabel(value: any): string {
        const config = getArticleConfig();
        const selector =
            config?.inputs?.transaction_type ||
            config?.selectors?.transactionType;

        const select = selector
            ? (getArticleInput(selector) as HTMLSelectElement | null)
            : null;

        if (!select) return value ?? "";

        const option = Array.from(select.options).find(
            (item: HTMLOptionElement) => String(item.value) === String(value),
        );

        return option?.text?.trim() || value || "";
    }

    function showArticleDetailsSection(): void {
        const section = document.getElementById("article-details-section");
        if (!section) return;

        section.classList.remove("hidden");
    }

    function renderArticleDetailsTable() {
        const body = document.getElementById("articleDetailsTableBody");
        const emptyState = document.getElementById("articleDetailsEmptyState");
        const wrapper = document.getElementById("articleDetailsTableWrapper");
        const count = document.getElementById("articleDetailsCount");
        const head = document.getElementById("articleDetailsTableHead");

        if (!body || !emptyState || !wrapper || !head) {
            return;
        }

        const columns = getArticleColumns();

        if (count) {
            count.textContent = String(articleTransactions.length);
        }

        /*
         * No records
         */
        if (articleTransactions.length === 0) {
            body.innerHTML = "";

            wrapper.classList.add("hidden");
            emptyState.classList.remove("hidden");

            return;
        }

        /*
         * Records exist
         */
        wrapper.classList.remove("hidden");
        emptyState.classList.add("hidden");

        /*
         * Dynamic headers
         */
        head.innerHTML = `
        <tr>

            ${columns
                .map(
                    (column) => `
                <th class="px-5 py-3.5 text-left
                           text-xs font-semibold
                           uppercase tracking-wider
                           text-gray-500
                           whitespace-nowrap">
                    ${formatColumnName(column)}
                </th>
            `,
                )
                .join("")}

            <th class="px-5 py-3.5 text-center
                       text-xs font-semibold
                       uppercase tracking-wider
                       text-gray-500">
                Action
            </th>

        </tr>
    `;

        /*
         * Dynamic rows
         */
        body.innerHTML = articleTransactions
            .map((row: any, index: number) => {
                return `
            <tr class="hover:bg-blue-50/40 transition-colors duration-100">

                ${columns
                        .map(
                            (column) => `
                    <td class="px-5 py-4 text-sm text-gray-700 whitespace-nowrap">
                        ${escapeArticleHtml(row[column] ?? "")}
                    </td>
                `,
                        )
                        .join("")}

                <td class="px-5 py-4 text-center">

                    <button
                        type="button"
                        class="delete-article-detail
                               inline-flex items-center justify-center
                               w-9 h-9
                               rounded-lg
                               text-red-500
                               hover:text-red-700
                               hover:bg-red-50
                               transition-colors"
                        data-index="${index}"
                        title="Delete">

                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="w-4 h-4"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2">

                            <path stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6V7M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3m-7 0h10" />

                        </svg>

                    </button>

                </td>

            </tr>
        `;
            })
            .join("");
    }
    function formatColumnName(column: string): string {
        return column
            .replace(/_/g, " ")
            .replace(/\b\w/g, (char) => char.toUpperCase());
    }

    function initializeArticleTransactions() {
        const config = getArticleConfig();

        if (!config) {
            console.warn("Article transaction config not loaded.");
            return;
        }

        const existing = Array.isArray(config.existingTransactions)
            ? config.existingTransactions
            : [];

        articleTransactions = existing.map((row: any) => ({ ...row }));

        console.log(
            "[DEBUG] initializeArticleTransactions -> articleTransactions:",
            articleTransactions,
        );

        const section = document.getElementById("article-details-section");

        if (section) {
            section.classList.remove("hidden");
        }

        renderArticleDetailsTable();
    }

    async function addArticleDetail() {
        const config = getArticleConfig();

        if (!config) {
            return;
        }

        // const partyId = getArticleInput(config.inputs.party_id)?.value || "";
        const transaction: any = {};
        let hasEmptyRequired = false;

        const transactionType =
            // getArticleInput(config.inputs.transaction_type)?.value || "";
            // Dynamically get all values from config.inputs
            Object.keys(config.inputs).forEach(key => {
                const selector = config.inputs[key];
                const value = getArticleInput(selector)?.value || "";

                if (!value) {
                    hasEmptyRequired = true;
                }
                transaction[key] = value;
            });


        const transactionNo =
            getArticleInput(config.inputs.transaction_no)?.value || "";

        // const transactionDate =
        //     getArticleInput(config.inputs.transaction_date)?.value || "";

        // const amount = getArticleInput(config.inputs.amount)?.value || "";

        // if (
        //     !partyId ||
        //     !transactionType ||
        //     !transactionNo ||
        //     !transactionDate ||
        //     !amount
        // ) {
        //     alert("Please fill all required fields.");
        //     return;
        // }

         if (hasEmptyRequired) {
            alert("Please fill all required fields.");
            return;
        }
        // const partyName = getPartyName(partyId);
        // If party_id exists, fetch party_name
        if (config.inputs.party_id) {
            transaction.party_name = getPartyName(transaction.party_id);
        } else if (config.articleId === "40") {
            // Hardcode Party Name for Article 40 as requested
            // transaction.party_name = "Hardcoded Party Name"; 
            transaction.party_name = "Mortgagee Name";
        }

        // const transaction = {
        //     party_id: partyId,
        //     party_name: partyName,
        //     transaction_type: transactionType,
        //     transaction_no: transactionNo,
        //     transaction_date: transactionDate,
        //     amount: amount,
        // };

        try {
            const response = await fetch(
                (window as any).edeedRoutes.saveArticleDetails,
                {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": getCsrfToken(),
                        Accept: "application/json",
                    },
                    body: JSON.stringify({
                        article_id: config.articleId,
                        transaction: transaction,
                    }),
                },
            );

            const data = await response.json();

            if (!response.ok || data.status !== "success") {
                throw new Error(
                    data.message || "Failed to save article detail.",
                );
            }

            // Database record returned from server
            articleTransactions.push(data.transaction);

            renderArticleDetailsTable();

            clearArticleInputs();

            // Deed is shown ONLY after DB save succeeds
            showDeedIfEntryExists();
        } catch (error: any) {
            console.error(error);

            alert(error.message || "Failed to save article detail.");
        }
    }
    function clearArticleInputs() {
        const config = getArticleConfig();

        if (!config || !config.inputs) {
            return;
        }

        Object.values(config.inputs).forEach((selector: any) => {
            const element = document.querySelector(selector) as
                | HTMLInputElement
                | HTMLSelectElement
                | HTMLTextAreaElement
                | null;

            if (!element) {
                return;
            }

            if (element instanceof HTMLSelectElement) {
                element.value = "";
            } else {
                element.value = "";
            }
        });
    }
    function getArticleColumns(): string[] {
        if (!articleTransactions.length) {
            return [];
        }

        const columns = new Set<string>();

        articleTransactions.forEach((row: any) => {
            Object.keys(row).forEach((key) => {
                if (
                    ["id", "token_no", "created_at", "updated_at"].includes(key)
                ) {
                    return;
                }

                const hasValue = articleTransactions.some(
                    (item: any) =>
                        item[key] !== null &&
                        item[key] !== undefined &&
                        String(item[key]).trim() !== "",
                );

                if (hasValue) {
                    columns.add(key);
                }
            });
        });

        return Array.from(columns);
    }

    async function deleteArticleDetail(index: number): Promise<void> {
        const row = articleTransactions[index];

        if (!row) return;

        if (!row.id) {
            articleTransactions.splice(index, 1);
            renderArticleDetailsTable();
            window.dispatchEvent(
                new CustomEvent("article-transactions-updated"),
            );
            return;
        }

        const routes = (window as any).edeedRoutes || {};
        if (!routes.deleteArticleDetails) {
            notyf.error("Delete route is not configured.");
            return;
        }

        try {
            const response = await fetch(routes.deleteArticleDetails, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": getCsrfToken(),
                    Accept: "application/json",
                },
                body: JSON.stringify({ id: row.id }),
            });

            const data = await response.json();

            if (!response.ok || data.status !== "success") {
                throw new Error(
                    data.message || "Unable to delete article detail.",
                );
            }

            articleTransactions.splice(index, 1);
            renderArticleDetailsTable();
            window.dispatchEvent(
                new CustomEvent("article-transactions-updated"),
            );
            notyf.success("Article detail deleted.");
        } catch (error: any) {
            console.error(error);
            notyf.error(error?.message || "Unable to delete article detail.");
        }
    }

    async function saveArticleDetails(): Promise<void> {
        const config = getArticleConfig();
        const routes = (window as any).edeedRoutes || {};

        if (!config) {
            notyf.error("Article configuration not loaded.");
            return;
        }

        if (!routes.saveArticleDetails) {
            notyf.error("Save route is not configured.");
            return;
        }

        if (articleTransactions.length === 0) {
            notyf.error("Add at least one article detail.");
            return;
        }

        const saveButton = document.getElementById(
            "saveArticleDetails",
        ) as HTMLButtonElement | null;

        if (saveButton) saveButton.disabled = true;

        try {
            const response = await fetch(routes.saveArticleDetails, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": getCsrfToken(),
                    Accept: "application/json",
                },
                body: JSON.stringify({
                    article_id: config.articleId,
                    language: language?.value || "",
                    transactions: articleTransactions,
                }),
            });

            const data = await response.json();

            if (!response.ok || data.status !== "success") {
                throw new Error(
                    data.message || "Unable to save article details.",
                );
            }

            if (Array.isArray(data.transactions)) {
                articleTransactions = data.transactions;
            }

            renderArticleDetailsTable();
            window.dispatchEvent(
                new CustomEvent("article-transactions-updated"),
            );
            notyf.success(
                data.message || "Article details saved successfully.",
            );
        } catch (error: any) {
            console.error(error);
            notyf.error(error?.message || "Unable to save article details.");
        } finally {
            if (saveButton) saveButton.disabled = false;
        }
    }

    document.addEventListener("click", (e) => {
        const target = e.target as HTMLElement;

        if (target.closest("#addArticleDetail")) {
            addArticleDetail();
            return;
        }

        if (target.closest("#saveArticleDetails")) {
            saveArticleDetails();
            return;
        }

        const deleteButton = target.closest(
            ".delete-article-detail",
        ) as HTMLElement | null;

        if (deleteButton) {
            const index = Number(deleteButton.dataset.index);
            deleteArticleDetail(index);
        }
    });

    initializeArticleTransactions();

    let articleFieldsRequestId = 0;

    async function loadArticleFieldsForLanguage(selectedLang: string) {
        console.log(
            "[DEBUG] loadArticleFieldsForLanguage called with:",
            selectedLang,
        );

        const articleId = "40";

        console.log("[DEBUG] window.articleId:", articleId);

        const requestId = ++articleFieldsRequestId;

        if (!selectedLang || !articleId) {
            console.log(
                "[DEBUG] bailing out early - missing selectedLang or articleId",
            );
            if (requestId === articleFieldsRequestId) {
                articleFieldsContainer.innerHTML = "";
            }
            return;
        }

        articleFieldsContainer.innerHTML =
            '<div class="text-gray-500">Loading fields...</div>';

        try {
            const response = await fetch(
                `/edeed/article-fields?article_id=${encodeURIComponent(articleId)}&language=${encodeURIComponent(selectedLang)}`,
                {
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        Accept: "application/json",
                    },
                },
            );

            const data = await response.json();

            console.log(
                "[DEBUG] fetch response.ok:",
                response.ok,
                "data.status:",
                data.status,
            );
            console.log("[DEBUG] fetch response data:", data);

            /*
             * A newer request has started since this one began.
             * Ignore this stale response entirely.
             */
            if (requestId !== articleFieldsRequestId) {
                console.log(
                    "[DEBUG] stale response discarded (requestId mismatch)",
                );
                return;
            }

            if (!response.ok || data.status !== "success") {
                throw new Error(
                    data.message || "Unable to load article fields.",
                );
            }

            articleFieldsContainer.innerHTML = data.html;
            initTomSelect(articleFieldsContainer);
            const scripts = articleFieldsContainer.querySelectorAll("script");

            scripts.forEach((oldScript) => {
                const newScript = document.createElement("script");

                Array.from(oldScript.attributes).forEach((attr) => {
                    newScript.setAttribute(attr.name, attr.value);
                });

                newScript.textContent = oldScript.textContent;

                oldScript.parentNode?.replaceChild(newScript, oldScript);
            });

            console.log(
                "[DEBUG] window.articleTransactionConfig after script injection:",
                (window as any).articleTransactionConfig,
            );

            initializeArticleTransactions();

            showDeedIfEntryExists();
        } catch (error) {
            if (requestId !== articleFieldsRequestId) {
                return;
            }

            console.error("[DEBUG] loadArticleFieldsForLanguage error:", error);

            articleFieldsContainer.innerHTML = `
            <div class="text-red-600">
                Unable to load article fields.
            </div>
        `;
        }
    }

    languageSelect?.addEventListener("change", function () {
        const selectedLang = this.value;

        console.log(
            "[DEBUG] language select change event fired, value:",
            selectedLang,
        );

        if (!selectedLang) {
            editorWrapper?.classList.add("hidden");
            generateBtn?.classList.add("hidden");
            aiSummaryBtn?.classList.add("hidden");

            if (hiddenInput) hiddenInput.value = "";
            quill.root.innerHTML = "";

            articleFieldsContainer.innerHTML = "";
            return;
        }

        loadArticleFieldsForLanguage(selectedLang);
    });

    window.addEventListener("article-transactions-updated", () => {
        showDeedIfEntryExists();
    });

    quill.on("text-change", () => {
        if (hiddenInput) {
            hiddenInput.value = quill.root.innerHTML;
        }
    });

    document.addEventListener("click", (e) => {
        const target = e.target as HTMLElement;

        if (target.closest("#gen-edeed-btn")) {
            // notyf.success("E-deed Generated Successfully");
        }
    });

    const modal = document.getElementById("ai-summary-modal");

    document.getElementById("ai-summary-btn")?.addEventListener("click", () => {
        modal?.classList.remove("hidden");
    });

    document
        .getElementById("close-ai-summary")
        ?.addEventListener("click", () => {
            modal?.classList.add("hidden");
        });

    modal?.addEventListener("click", (e) => {
        if (e.target === modal) {
            modal.classList.add("hidden");
        }
    });

    const dbParties = (window as any).edeedParties || [];
    const dbWitnesses = (window as any).edeedWitnesses || [];
    const dbIdentifiers = (window as any).edeedIdentifiers || [];

    const parties = [
        ...dbParties.map((party: any) => ({
            party_id: `party_${party.id}`,
            db_id: party.id,
            name:
                party.party_full_name_en ||
                party.party_full_name ||
                party.name ||
                "",
            type: party.party_type_name || party.party_type || "Party",
            category: "party",
            status: party.esign_status === "Y" ? "Signed" : "Pending",
            signedAt: party.esign_signed_at || null,
        })),

        ...dbWitnesses.map((witness: any) => ({
            party_id: `witness_${witness.id}`,
            db_id: witness.id,
            name:
                witness.witness_full_name_en ||
                witness.witness_full_name ||
                witness.name ||
                "",
            type: "Witness",
            category: "witness",
            status: witness.esign_status === "Y" ? "Signed" : "Pending",
            signedAt: witness.esign_signed_at || null,
        })),

        ...dbIdentifiers.map((identifier: any) => ({
            party_id: `identifier_${identifier.id}`,
            db_id: identifier.id,
            name:
                identifier.identification_full_name_en ||
                identifier.identification_full_name ||
                identifier.name ||
                "",
            type: "Identifier",
            category: "identifier",
            status: identifier.esign_status === "Y" ? "Signed" : "Pending",
            signedAt: identifier.esign_signed_at || null,
        })),
    ];
    let signedParties: any[] = [];

    // function showDeedIfEntryExists() {

    //     const articleFieldsContainer =
    //         document.getElementById("article-fields-container");

    //     console.log('[DEBUG] showDeedIfEntryExists called');
    //     console.log('[DEBUG] articleTransactions:', articleTransactions, 'length:', articleTransactions.length);
    //     console.log('[DEBUG] language?.value:', language?.value);

    //     if (!articleFieldsContainer) {
    //         return;
    //     }

    //     // Check the common article-details state/table, which lives in edeed.blade.php.
    //     const hasTransactions = articleTransactions.length > 0;

    //     if (!hasTransactions) {
    //         console.log('[DEBUG] no transactions -> hiding editor');
    //         editorWrapper?.classList.add("hidden");
    //         generateBtn?.classList.add("hidden");
    //         aiSummaryBtn?.classList.add("hidden");
    //         return;
    //     }

    //     const selectedLanguage = language?.value;

    //     if (!selectedLanguage) {
    //         console.log('[DEBUG] no selectedLanguage -> hiding editor');
    //         editorWrapper?.classList.add("hidden");
    //         return;
    //     }

    //     console.log('[DEBUG] showing editor for language:', selectedLanguage);

    //     editorWrapper?.classList.remove("hidden");
    //     generateBtn?.classList.remove("hidden");
    //     generateBtn?.classList.add("flex");
    //     aiSummaryBtn?.classList.remove("hidden");
    //     aiSummaryBtn?.classList.add("flex");

    //     quill.root.innerHTML =
    //         templates[selectedLanguage] ?? "";

    //     if (hiddenInput) {
    //         hiddenInput.value =
    //             quill.root.innerHTML;
    //     }
    // }

    async function fetchGeneratedDeedHtml(
        languageKey: string,
    ): Promise<string> {
        const routes = (window as any).edeedRoutes || {};
        const tokenNo = (document.getElementById("token") as HTMLInputElement)
            .value;
        // console.log(tokenNo);return;
        // const tokenNo = (window as any).edeedTokenNo;
        const articleId = (window as any).articleId;

        const languageTypeByKey: Record<string, string> = {
            english: "E",
            assamese: "A",
            bengali: "B",
        };

        const languageType = languageTypeByKey[languageKey];

        if (!routes.generateDeed || !articleId || !languageType) {
            console.warn("[EDeed] Missing config for deed generation", {
                hasRoute: !!routes.generateDeed,
                tokenNo,
                articleId,
                languageType,
            });
            return "";
        }

        // const response = await fetch(routes.generateDeed, {
        //     method: 'POST',
        //     headers: {
        //         'Content-Type': 'application/json',
        //         // 'X-CSRF-TOKEN': getCsrfToken(),
        //         'Accept': 'application/json',
        //     },
        //     body: JSON.stringify({
        //         token_no: tokenNo,
        //         article_id: articleId,
        //         language: languageType,
        //     }),
        // });

        const { data } = await api.post<any>("/edeed/generate", {
            token_no: tokenNo,
            article_id: articleId,
            language: languageType,
        });
        // console.log(data);

        // const data = await response.json();

        // if (!response.ok || data.status !== 'success') {
        //     throw new Error(data.message || 'Unable to generate e-deed content.');
        // }

        return data.html as string;
    }
    document
        .getElementById("download-edeed-btn")
        ?.addEventListener("click", () => {
            window.location.href = "/edeed/download";
        });

    document
        .getElementById("regenerate-edeed-btn")
        ?.addEventListener("click", async function (this: HTMLButtonElement) {
            if (
                !confirm(
                    "This will delete the current e-deed draft and regenerate it. Continue?",
                )
            ) {
                return;
            }

            this.disabled = true;

            try {
                const response = await fetch("/edeed/draft/delete", {
                    method: "DELETE",
                    headers: {
                        "X-CSRF-TOKEN":
                            document
                                .querySelector('meta[name="csrf-token"]')
                                ?.getAttribute("content") || "",
                        Accept: "application/json",
                    },
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || "Failed to delete draft.");
                }

                notyf.success(data.message || "Draft deleted. Regenerating...");

                setTimeout(() => {
                    window.location.reload();
                }, 800);
            } catch (error: any) {
                console.error(error);
                notyf.error(
                    error?.message || "Unable to regenerate e-deed content.",
                );
            } finally {
                this.disabled = false;
            }
        });

    async function showDeedIfEntryExists() {
        const articleFieldsContainer = document.getElementById(
            "article-fields-container",
        );

        if (!articleFieldsContainer) {
            return;
        }

        const hasTransactions = articleTransactions.length > 0;

        if (!hasTransactions) {
            editorWrapper?.classList.add("hidden");
            generateBtn?.classList.add("hidden");
            aiSummaryBtn?.classList.add("hidden");
            return;
        }

        const selectedLanguage = language?.value;

        if (!selectedLanguage) {
            editorWrapper?.classList.add("hidden");
            return;
        }

        editorWrapper?.classList.remove("hidden");
        generateBtn?.classList.remove("hidden");
        generateBtn?.classList.add("flex");
        aiSummaryBtn?.classList.remove("hidden");
        aiSummaryBtn?.classList.add("flex");

        try {
            const html = await fetchGeneratedDeedHtml(selectedLanguage);

            quill.clipboard.dangerouslyPasteHTML(html);

            if (hiddenInput) {
                hiddenInput.value = quill.root.innerHTML;
            }
        } catch (error: any) {
            console.error(error);
            notyf.error(error?.message || "Unable to generate e-deed content.");
        }
    }
    function buildSignedDeedHtml() {
        const deedHtml = hiddenInput?.value || "";

        return `
<div id="ngdrs-edeed-document"
    style="
        width:100%;
        max-width:900px;
        margin:0 auto;
        background:#fff;
        padding:50px;
        box-sizing:border-box;
        font-family:'Times New Roman',serif;
        color:#000;
    ">

    <!-- GOVERNMENT HEADER -->
    <div style="
        text-align:center;
        border-bottom:2px solid #000;
        padding-bottom:15px;
        margin-bottom:30px;
    ">

        <img
            src="/images/banner-img.png"
            style="
                width:70px;
                height:auto;
                margin:0 auto 10px auto;
                display:block;
            "
        >

        <div style="
            font-size:20px;
            font-weight:bold;
        ">
            GOVERNMENT OF ASSAM
        </div>

        <div style="
            font-size:15px;
            margin-top:5px;
        ">
            Mati Document Registration System
        </div>

        <div style="
            font-size:13px;
            color:#555;
            margin-top:5px;
        ">
            Electronically Executed Sale Deed
        </div>

    </div>


    <!-- ORIGINAL DEED -->
    <div id="deed-body">

        ${deedHtml}

    </div>


    <!-- MULTI PARTY SIGNATURE TABLE -->
    <div style="
        margin-top:60px;
        page-break-inside:avoid;
    ">

        <h2 style="
            text-align:center;
            font-size:22px;
            border-bottom:1px solid #ccc;
            padding-bottom:10px;
            margin-bottom:25px;
        ">
            EXECUTION & DIGITAL SIGNATURE DETAILS
        </h2>


        <table style="
            width:100%;
            border-collapse:collapse;
            table-layout:fixed;
        ">

            <thead>

                <tr style="
                    background:#f3f4f6;
                ">

                    <th style="
                        border:1px solid #000;
                        padding:12px;
                        text-align:left;
                    ">
                        Party Name
                    </th>

                    <th style="
                        border:1px solid #000;
                        padding:12px;
                        text-align:left;
                    ">
                        Party Type
                    </th>

                    <th style="
                        border:1px solid #000;
                        padding:12px;
                        text-align:center;
                    ">
                        e-Sign Status
                    </th>

                    <th style="
                        border:1px solid #000;
                        padding:12px;
                        text-align:center;
                    ">
                        Digital Signature
                    </th>

                </tr>

            </thead>


            <tbody>

                ${parties
                .map((party) => {
                    return `

                    <tr>

                        <td style="
                            border:1px solid #000;
                            padding:12px;
                        ">
                            <strong>
                                ${party.name}
                            </strong>
                        </td>


                        <td style="
                            border:1px solid #000;
                            padding:12px;
                        ">
                            ${party.type}
                        </td>


                        <td style="
                            border:1px solid #000;
                            padding:12px;
                            text-align:center;
                            font-weight:bold;
                            color:#16a34a;
                        ">
                            SIGNED
                        </td>


                        <td
    title="Signed by ${party.name} (${party.type})${party.signedAt ? " on " + party.signedAt : ""}"
    style="
        border:1px solid #000;
        padding:12px;
        text-align:center;
        height:70px;
        cursor:help;
    ">

    <div style="
        font-weight:bold;
        color:#15803d;
    ">
        DIGITALLY SIGNED BY
    </div>

    <div style="
        font-size:11px;
        margin-top:5px;
        font-weight:bold;
    ">
        ${party.name}
    </div>

</td>

                    </tr>

                    `;
                })
                .join("")}

            </tbody>

        </table>

    </div>


    <!-- FOOTER -->

    <div style="
        margin-top:50px;
        text-align:center;
        border-top:1px solid #ccc;
        padding-top:15px;
        font-size:12px;
        color:#666;
    ">

        This document has been electronically executed
        through MATi.

        <br>

        Generated on:
        ${new Date().toLocaleString()}

    </div>

</div>
`;
    }
    function openPreviewModal(html: string) {
        let modal = document.getElementById("pdf-preview-modal");

        if (!modal) {
            modal = document.createElement("div");

            modal.id = "pdf-preview-modal";

            modal.className =
                "fixed inset-0 z-50 bg-black/50 flex items-center justify-center";

            modal.innerHTML = `
            <div class="bg-white w-[90%] h-[90%] rounded-lg flex flex-col">

                <div class="p-4 border-b flex justify-between">

                    <h2>
                        E-Deed Preview
                    </h2>

                    <button id="close-preview">
                        ✕
                    </button>

                </div>

                <div
                    id="preview-content"
                    class="flex-1 overflow-auto p-5">
                </div>

                <div class="p-4 border-t">

                    
                </div>

            </div>
        `;

            document.body.appendChild(modal);

            document.addEventListener("click", (e) => {
                const target = e.target as HTMLElement;

                if (target.closest("#close-preview")) {
                    const modal = document.getElementById("pdf-preview-modal");

                    console.log("Closing Preview");

                    modal?.classList.add("hidden");
                }
            });
        }
        modal.classList.remove("hidden");
        const preview = document.getElementById("preview-content");

        if (preview) {
            preview.innerHTML = `
<div style="
    max-width:900px;
    margin:auto;
    background:white;
    padding:50px;
    font-family:'Times New Roman',serif;
    color:#000;
">

    <!-- Header -->

    

    <!-- Deed Content -->

    <div id="deed-body">

        ${html}

    </div>

    <!-- Signature Section -->

    
    <!-- Footer -->

   

</div>
`;
        }

        const pdfBtn = document.getElementById("download-pdf");

        if (pdfBtn) {
            pdfBtn.onclick = async () => {
                console.log("Generate PDF clicked");

                await generatePdf();
            };
        }
    }
    function generatePdf() {
        const preview = document.getElementById("preview-content");

        if (!preview) {
            return;
        }

        const win = window.open("", "_blank");

        if (!win) {
            return;
        }

        win.document.write(`
        <html>
        <head>
            <title>E-Deed</title>
            <style>
                body{
                    font-family:'Times New Roman';
                    padding:30px;
                }
            </style>
        </head>
        <body>
            ${preview.innerHTML}
        </body>
        </html>
    `);

        win.document.close();

        setTimeout(() => {
            win.print();
        }, 500);
    }
    const esignModal = document.getElementById("esign-modal");

    const esignPartyList = document.getElementById("esign-party-list");

    document.getElementById("gen-edeed-btn")?.addEventListener("click", () => {
        if (!esignPartyList) {
            return;
        }

        esignPartyList.innerHTML = "";

        parties.forEach((party, index) => {
            esignPartyList.innerHTML += `
                <tr>
                    <td class="border p-3">${index + 1}</td>

                    <td class="border p-3 font-medium">
                        ${party.name}
                    </td>

                    <td class="border p-3">
                        ${party.type}
                    </td>

                    <td class="border p-3 text-center">

    <button
        class="esign-btn px-3 py-2 rounded-lg bg-green-600 text-white"
        data-party="${party.party_id}">

        Proceed to eSign

    </button>

    <button
        class="preview-btn ml-2 px-3 py-2 rounded-lg bg-blue-600 text-white"
        data-party="${party.party_id}">

        Preview

    </button>

</td>
                </tr>
            `;
        });

        esignModal?.classList.remove("hidden");
    });
    document.addEventListener("click", (e) => {
        const target = e.target as HTMLElement;

        const previewBtn = target.closest(".preview-btn");

        if (!previewBtn) {
            return;
        }

        const html = buildSignedDeedHtml();

        openPreviewModal(html);
    });

    document
        .getElementById("close-esign-modal")
        ?.addEventListener("click", () => {
            esignModal?.classList.add("hidden");
        });

    esignModal?.addEventListener("click", (e) => {
        if (e.target === esignModal) {
            esignModal.classList.add("hidden");
        }
    });

    document.addEventListener("click", async (e) => {
        const target = e.target as HTMLElement;

        const esignBtn = target.closest(".esign-btn");

        if (!esignBtn) {
            return;
        }

        const partyId = esignBtn.getAttribute("data-party");

        const party = parties.find(
            (p: any) => String(p.party_id) === String(partyId),
        );

        if (!party) {
            alert("Party not found");
            return;
        }

        try {
            const selectedLanguage = language?.value;

            const languageType =
                selectedLanguage === "english"
                    ? "E"
                    : selectedLanguage === "assamese"
                        ? "A"
                        : selectedLanguage === "bengali"
                            ? "B"
                            : null;

            const response = await fetch("/esign/start", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN":
                        document
                            .querySelector('meta[name="csrf-token"]')
                            ?.getAttribute("content") || "",
                },
                body: JSON.stringify({
                    party_id: party.party_id,
                    db_id: party.db_id,
                    party_type: party.category,
                    party_name: party.name,
                    deed_html: buildSignedDeedHtml(),
                    language_type: languageType,
                }),
            });

            const html = await response.text();

            document.open();

            document.write(html);

            document.close();
        } catch (e) {
            console.error(e);

            notyf.error("Unable to start eSign");
        }
    });

    document.addEventListener("click", (e) => {
        const target = e.target as HTMLElement;

        const previewBtn = target.closest(".preview-btn");

        if (!previewBtn) {
            return;
        }

        const html = buildSignedDeedHtml();

        openPreviewModal(html);
    });
    function showPartyModal() {
        const tbody = document.getElementById("party-table-body");

        if (!tbody) {
            return;
        }

        tbody.innerHTML = "";

        parties.forEach((party) => {
            tbody.innerHTML += `
            <tr>

                <td class="border p-2">
                    ${party.name}
                </td>

                <td class="border p-2">
                    ${party.type}
                </td>

                <td class="border p-2">
                    ${party.status}
                </td>

                <td class="border p-5 text-center">

    <div class="flex justify-center gap-2">

        <button
            class="esign-btn px-4 py-2 rounded-lg
            bg-green-600 text-white hover:bg-green-700"
            data-party="${party.party_id}">

            Proceed to eSign

        </button>

        <button
            class="preview-btn px-4 py-2 rounded-lg
            bg-blue-600 text-white hover:bg-blue-700"
            data-party="${party.party_id}">

            Preview

        </button>

    </div>

</td>

            </tr>
        `;
        });

        document.getElementById("esign-modal")?.classList.remove("hidden");
    }

    document.addEventListener("click", (e) => {
        const target = e.target as HTMLElement;

        if (target.closest("#gen-edeed-btn")) {
            showPartyModal();
        }

        if (target.closest("#ai-summary-btn")) {
            document
                .getElementById("ai-summary-modal")
                ?.classList.remove("hidden");
        }

        if (target.closest("#close-ai-summary")) {
            document
                .getElementById("ai-summary-modal")
                ?.classList.add("hidden");
        }

        if (target.id === "ai-summary-modal") {
            target.classList.add("hidden");
        }
    });
    document.addEventListener("click", (e) => {
        const target = e.target as HTMLElement;

        const btn = target.closest(".esign-btn");

        if (!btn) {
            return;
        }

        const partyId = btn.getAttribute("data-party");

        const party = parties[partyId];

        alert(`Proceeding to eSign `);
    });

    const existingLanguageType =
        (window as any).edeedExistingLanguageType || null;

    if (
        existingLanguageType &&
        languageTypeToKey[existingLanguageType] &&
        language &&
        !language.value
    ) {
        const existingLangKey = languageTypeToKey[existingLanguageType];

        const tomInstance = (language as any).tomselect;

        if (tomInstance) {
            tomInstance.setValue(existingLangKey);
            tomInstance.disable();
        } else {
            language.value = existingLangKey;
            language.disabled = true;
        }

        let hiddenLangInput = document.getElementById(
            "language-hidden",
        ) as HTMLInputElement | null;

        if (!hiddenLangInput) {
            hiddenLangInput = document.createElement("input");
            hiddenLangInput.type = "hidden";
            hiddenLangInput.id = "language-hidden";
            hiddenLangInput.name = "language";
            language.insertAdjacentElement("afterend", hiddenLangInput);
        }

        hiddenLangInput.value = existingLangKey;

        loadArticleFieldsForLanguage(existingLangKey);
    }

});

