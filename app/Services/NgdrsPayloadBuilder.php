<?php

namespace App\Services;

use App\Models\BankMasterUser;
use App\Models\Branch;
use App\Models\BranchUser;
use App\Models\Deed;

class NgdrsPayloadBuilder
{
    /**
     * Build standard payload strictly compatible with ngdrs_citizen-dev ArrayBuilder & LoginServices
     */
    public static function build(Deed $deed, BranchUser $officer, Branch $branch, BankMasterUser $bank): array
    {
        // Split officer name into first and last name
        $nameParts = preg_split('/\s+/', trim($officer->name), 2);
        $fname = $nameParts[0] ?? 'Authorized';
        $lname = $nameParts[1] ?? 'Officer';

        // Standardized transaction ID format: {BANK_CODE}/{YEAR}/{BRANCH_CODE}/{SEQ}
        // e.g. AGB/2026/BR001/00001
        $transId = $deed->deed_no;

        // Clean mobile number (digits only)
        $cleanMobile = preg_replace('/[^0-9]/', '', $officer->mobile_number);

        return [
            // Statutory Bank & Branch Identifiers
            'bank_id'          => $bank->id,
            'bank_code'        => $bank->bank_code,
            'bank_name'        => $bank->bank_name,
            'bank_user_id'     => $officer->officer_id, // Identifies branch user in ArrayBuilder line 72 & 131

            // Unique Transaction IDs (Dual compatibility for bank_trans_id and rtps_trans_id)
            'bank_trans_id'    => $transId, // Saved into session('sso_payload.bank_trans_id') and trn_general_info
            'rtps_trans_id'    => $transId, // Saved into trn_single_window.rtps_trans_id by ArrayBuilder::trnSingleWindowData

            // User Classification
            'user_type'        => 'B', // Bank User: unlocks mortgage articles [51, 40, 92]
            'process'          => 'I', // Ingestion flow: auto-redirects directly to general-info
            'service_id'       => 1,
            'portal_no'        => 1,

            // Authorized Officer Particulars (ArrayBuilder::arrCitizenUserRegSave)
            'contact_fname'    => $fname,
            'contact_mname'    => null,
            'contact_lname'    => $lname,
            'mobile_no'        => $cleanMobile,
            'email_id'         => $officer->email,
            'pan_no'           => $officer->pan_number,
            'tan_no'           => $officer->tan_number,
            'officer_age'      => $officer->age,

            // Branch Location & Postal Info (ArrayBuilder::trnSingleWindowData)
            'building'         => $branch->address ?? $officer->address ?? 'Branch Office',
            'street'           => $branch->address ?? 'Main Road',
            'city'             => $branch->district,
            'pincode'          => '781005',
            'state_id'         => 18, // Assam State ID
            'district_id'      => null,

            // Branch Network Codes
            'branch_code'      => $branch->branch_code,
            'branch_name'      => $branch->branch_name,
            'ifsc_code'        => $branch->ifsc_code,
            'district'         => $branch->district,

            // Mortgage Deed & Loan Particulars
            'deed_no'          => $deed->deed_no,
            'borrower_name'    => $deed->borrower_name,
            'borrower_mobile'  => $deed->borrower_mobile,
            'borrower_pan'     => $deed->borrower_pan,
            'loan_amount'      => (float) $deed->loan_amount,
            'stamp_duty'       => (float) $deed->stamp_duty,
            'property_details' => $deed->property_details,

            // Callback & Metadata
            'response_url'     => route('deeds.show', $deed->id),
            'timestamp'        => now()->toIso8601String(),
        ];
    }
}
