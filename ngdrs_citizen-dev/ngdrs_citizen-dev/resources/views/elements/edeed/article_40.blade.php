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
