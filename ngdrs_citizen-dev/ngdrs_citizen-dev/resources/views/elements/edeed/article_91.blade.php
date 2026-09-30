{{-- ============================================================
ARTICLE 91
Transaction Details
============================================================ --}}

{{-- <div class="article-91-wrapper"> --}}

    {{-- ========================================================
    TRANSACTION ENTRY CARD
    ======================================================== --}}
    <div class="article-card">

        <div class="article-card-header">
            <div>
                <h5 class="article-card-title">
                    Transaction Details
                </h5>

                <p class="article-card-subtitle">
                    Enter the transaction details and add them to the list.
                </p>
            </div>
        </div>


        <div class="article-card-body">

            {{-- ==================================================
            SELLER
            ================================================== --}}
            <div class="row">

                <div class="col-md-6 col-lg-5">

                    <x-select-input label="Seller" name="party_id" id="party_id" :required="true"
                        :options="$existingParties->pluck('party_full_name_en', 'id')->toArray()" />

                </div>

            </div>


            {{-- ==================================================
            TRANSACTION INFORMATION
            ================================================== --}}
            <div class="transaction-section">

                <div class="section-heading">
                    <span class="section-number">01</span>

                    <div>
                        <div class="section-title">
                            Payment / Transaction Information
                        </div>

                        <div class="section-description">
                            Provide the transaction reference, date and amount.
                        </div>
                    </div>
                </div>


                <div class="row">

                    {{-- Transaction Type --}}
                    <div class="col-md-6 col-lg-3">

                        <x-select-input label="Transaction Type" name="transaction_type" id="transaction_type"
                            :required="true" :options="[
        'cheque' => 'Cheque',
        'rtgs' => 'RTGS',
        'neft' => 'NEFT',
        'bank_transfer' => 'Bank Transfer',
        'cash' => 'Cash'
    ]" />
                    </div>


                    {{-- Transaction No --}}
                    <div class="col-md-6 col-lg-3">

                        <x-text-input label="Transaction No" name="transaction_no" id="transaction_no"
                            placeholder="Enter transaction number" :required="true"
                            tooltip="Enter cheque number or transaction reference number" />

                    </div>


                    {{-- Transaction Date --}}
                    <div class="col-md-6 col-lg-3">

                        <x-date-input label="Transaction Date" name="transaction_date" id="transaction_date"
                            :required="true" tooltip="Select the transaction date" />

                    </div>


                    {{-- Amount --}}
                    <div class="col-md-6 col-lg-3">

                        <x-number-input label="Amount" name="amount" id="amount" placeholder="Enter amount"
                            :required="true" tooltip="Enter the transaction amount" />

                    </div>

                </div>
        </div>
    </div>


{{-- ============================================================
STYLES
============================================================ --}}
{{-- <style>
    .article-91-wrapper {
        width: 100%;
        margin-top: 10px;
    }


    /* =========================================================
       CARD
       ========================================================= */

    .article-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        margin-bottom: 18px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }


    .article-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 18px 22px;
        background: #fafbfc;
        border-bottom: 1px solid #e9ecef;
    }


    .article-card-title {
        margin: 0;
        font-size: 17px;
        font-weight: 600;
        color: #263238;
    }


    .article-card-subtitle {
        margin: 4px 0 0;
        font-size: 13px;
        color: #7b8794;
    }


    .article-card-body {
        padding: 22px;
    }


    /* =========================================================
       TRANSACTION SECTION
       ========================================================= */

    .transaction-section {
        margin-top: 25px;
        padding-top: 20px;
        border-top: 1px solid #edf0f2;
    }


    .section-heading {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
    }


    .section-number {
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        background: #f1f5f9;
        color: #475569;
        font-size: 12px;
        font-weight: 700;
    }


    .section-title {
        font-size: 14px;
        font-weight: 600;
        color: #334155;
    }


    .section-description {
        margin-top: 2px;
        font-size: 12px;
        color: #94a3b8;
    }


    /* =========================================================
       ADD BUTTON
       ========================================================= */

    .add-button-wrapper {
        display: flex;
        justify-content: flex-end;
        margin-top: 8px;
    }


    .article-add-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-width: 165px;
        height: 40px;
        padding: 0 18px;
        border: 0;
        border-radius: 6px;
        background: #2563eb;
        color: #ffffff;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
    }


    .article-add-btn:hover {
        background: #1d4ed8;
        box-shadow: 0 4px 10px rgba(37, 99, 235, 0.18);
        transform: translateY(-1px);
    }


    .article-add-btn:active {
        transform: translateY(0);
    }


    .add-icon {
        font-size: 19px;
        line-height: 1;
        font-weight: 400;
    }


    /* =========================================================
       TRANSACTION COUNT
       ========================================================= */

    .transaction-count-wrapper {
        display: flex;
        align-items: center;
        gap: 7px;
    }


    .transaction-count {
        min-width: 28px;
        height: 28px;
        padding: 0 7px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 15px;
        background: #eff6ff;
        color: #2563eb;
        font-size: 12px;
        font-weight: 700;
    }


    .transaction-count-label {
        color: #64748b;
        font-size: 12px;
    }


    /* =========================================================
       EMPTY STATE
       ========================================================= */

    .transaction-empty-state {
        padding: 42px 20px;
        text-align: center;
        border: 1px dashed #d8dee6;
        border-radius: 8px;
        background: #fafbfc;
    }


    .empty-icon {
        width: 44px;
        height: 44px;
        margin: 0 auto 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: #f1f5f9;
        color: #64748b;
        font-size: 25px;
        font-weight: 300;
    }


    .empty-title {
        color: #475569;
        font-size: 14px;
        font-weight: 600;
    }


    .empty-description {
        max-width: 450px;
        margin: 6px auto 0;
        color: #94a3b8;
        font-size: 12px;
        line-height: 1.6;
    }


    /* =========================================================
       TABLE
       ========================================================= */

    .transaction-table-wrapper {
        width: 100%;
    }


    .article-transaction-table {
        width: 100%;
        margin: 0;
        border-collapse: separate;
        border-spacing: 0;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        overflow: hidden;
    }


    .article-transaction-table thead th {
        padding: 12px 13px;
        background: #f8fafc;
        border-bottom: 1px solid #e5e7eb;
        color: #475569;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.35px;
        white-space: nowrap;
    }


    .article-transaction-table tbody td {
        padding: 13px;
        border-bottom: 1px solid #edf0f2;
        color: #475569;
        font-size: 13px;
        vertical-align: middle;
    }


    .article-transaction-table tbody tr:last-child td {
        border-bottom: 0;
    }


    .article-transaction-table tbody tr:hover {
        background: #fafcff;
    }


    .article-transaction-table tfoot td {
        padding: 14px 13px;
        background: #f8fafc;
        border-top: 1px solid #e5e7eb;
        font-size: 13px;
    }


    .total-label {
        text-align: right;
        color: #475569;
        font-weight: 600;
    }


    .total-amount {
        text-align: right;
        color: #1e293b;
        font-size: 15px !important;
        font-weight: 700;
        white-space: nowrap;
    }


    .transaction-party {
        color: #1e293b;
        font-weight: 600;
    }


    .transaction-type-badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 9px;
        border-radius: 5px;
        background: #f1f5f9;
        color: #475569;
        font-size: 11px;
        font-weight: 600;
    }


    .transaction-amount {
        color: #1e293b;
        font-weight: 600;
        text-align: right;
        white-space: nowrap;
    }


    /* =========================================================
       DELETE BUTTON
       ========================================================= */

    .delete-transaction {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #fecaca;
        border-radius: 5px;
        background: #fff;
        color: #dc2626;
        font-size: 15px;
        cursor: pointer;
        transition: all 0.2s ease;
    }


    .delete-transaction:hover {
        background: #fef2f2;
        border-color: #fca5a5;
    }


    /* =========================================================
       SAVE SECTION
       ========================================================= */

    .save-section {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        padding: 16px 22px;
        background: #fafbfc;
        border-top: 1px solid #e9ecef;
    }


    .save-info {
        display: flex;
        align-items: center;
        gap: 10px;
    }


    .save-info-icon {
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: #ecfdf5;
        color: #059669;
        font-size: 14px;
        font-weight: 700;
    }


    .save-info-title {
        color: #475569;
        font-size: 13px;
        font-weight: 600;
    }


    .save-info-text {
        margin-top: 2px;
        color: #94a3b8;
        font-size: 11px;
    }


    .article-save-btn {
        min-width: 135px;
        height: 40px;
        padding: 0 18px;
        border: 0;
        border-radius: 6px;
        background: #059669;
        color: #ffffff;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
    }


    .article-save-btn:hover {
        background: #047857;
        box-shadow: 0 4px 10px rgba(5, 150, 105, 0.18);
    }


    .article-save-btn:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }


    .save-spinner {
        display: inline-block;
        width: 15px;
        height: 15px;
        margin-left: 5px;
        vertical-align: middle;
        border: 2px solid rgba(255, 255, 255, 0.35);
        border-top-color: #ffffff;
        border-radius: 50%;
        animation: articleSpinner 0.7s linear infinite;
    }


    @keyframes articleSpinner {
        to {
            transform: rotate(360deg);
        }
    }


    /* =========================================================
       RESPONSIVE
       ========================================================= */

    @media (max-width: 767px) {

        .article-card-body {
            padding: 16px;
        }


        .article-card-header {
            padding: 15px 16px;
        }


        .save-section {
            align-items: stretch;
            flex-direction: column;
        }


        .article-save-btn {
            width: 100%;
        }


        .add-button-wrapper {
            justify-content: stretch;
        }


        .article-add-btn {
            width: 100%;
        }


        .transaction-list-header {
            align-items: flex-start;
            gap: 10px;
            flex-direction: column;
        }

    }
</style> --}}

{{-- ============================================================
ARTICLE 91 CONFIGURATION
Only Article 91 fields are defined here.
Add / Table / Delete / Save are handled by edeed.blade.php + main.ts.
============================================================ --}}
{{-- <script>
window.articleTransactionConfig = {
    articleId: 91,

    inputs: {
        party_id: '#party_id',
        transaction_type: '#transaction_type',
        transaction_no: '#transaction_no',
        transaction_date: '#transaction_date',
        amount: '#amount'
    },

    existingTransactions: @json($existingArticleDetails ?? [])
};
</script> --}}


<div class="article-91-wrapper">
    <div class="article-card">
        <div class="article-card-header">
            <div>
                <h5 class="article-card-title">Mortgage Agreement Details</h5>
                <p class="article-card-subtitle">Enter the mortgage details and add them to the list.</p>
            </div>
        </div>

        <div class="article-card-body">
            <div class="transaction-section">
                <div class="row">
                    <!-- Loan Amount -->
                    <div class="col-md-6 col-lg-3">
                        <x-number-input label="Loan Amount (INR)" name="loan_amount" id="loan_amount" placeholder="Enter loan amount" :required="true" />
                    </div>

                    <!-- Interest Rate -->
                    <div class="col-md-6 col-lg-3">
                        <x-number-input label="Interest Rate (%)" name="interest_rate" id="interest_rate" placeholder="Enter interest rate" :required="true" />
                    </div>

                    <!-- Interest Frequency -->
                    <div class="col-md-6 col-lg-3">
                        <x-select-input label="Interest Payment Frequency" name="interest_frequency" id="interest_frequency" :required="true" :options="[
                            'monthly' => 'Monthly',
                            'quarterly' => 'Quarterly',
                            'annually' => 'Annually'
                        ]" />
                    </div>

                    <!-- Repayment Duration -->
                    <div class="col-md-6 col-lg-3">
                        <x-number-input label="Repayment Duration (years)" name="repayment_years" id="repayment_years" placeholder="Enter years" :required="true" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Re-use the <style> block exactly as it is in article_91.blade.php here -->

<script>
// Configure the JS to recognize your fields so main.ts handles the Add/Table automatically
window.articleTransactionConfig = {
    articleId: '40',
    inputs: {
        loan_amount: '#loan_amount',
        interest_rate: '#interest_rate',
        interest_frequency: '#interest_frequency',
        repayment_years: '#repayment_years'
    },
    existingTransactions: []
};
</script>
