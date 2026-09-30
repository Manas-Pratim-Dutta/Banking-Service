<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EDeedPartyTransactionDetails extends Model
{
    protected $table = 'ngdrstab_trn_e_deed_party_transaction_details';

    protected $fillable = [
        'token_no',
        'party_id',
        'party_name',
        'transaction_type',
        'transaction_no',
        'transaction_date',
        'amount',

        'flat_no',
        'floor',
        'apartment_name',
        'super_built_area',
        'area_in_sqft',
        'no_of_floors',
        'holding_no',

        'partnership_name',
        'duration',
        'business_place',
        'business_activity',
        'commencement',
        'capital_contribution',
        'profit_loss',

        'bank',
        'relationship',
        'advance_paid',
        'advance_date',
        'scrutiny_days',
        'refund_days',
        'interest_rate',
        'approval_days',

        'document_nature',
        'date_of_original_document',
        'doc_reg_no',
        'cancellation_reason',
        'court_name',
        'court_case_no',
        'date_of_judgement',

        'total_amount',
        'total_number_of_installment',
        'account_number',
        'ifsc_code',
        'holder_name',
        'reason_will',
        'prop_desc',
        'purpose',
        'expiration',
        'interval',
        'interval_ll',
        'month',

        'account_holder_name',
        'account_no',
        'jurisdiction_location',
        'notice_period',
        'address',
        'name_ll',
        'act',
        'order_no',
        'date_ll',
        'date_lll',
        'office',
        'address_ll',

        'pan_no',
        'gstin',
        'name',
        'name_lll',
        'pan_no2',
        'gstin2',
        'address3',

        'deb_no',
        'issued_by',
        'val',
        'val2',
        'val3',
        'nature',
        'rate',
        'name4',
        'name5',
        'name6',

        'party_date_of_birth',

        'book_no',
        'volume_no',

        'boundary_east',
        'boundary_north',
        'boundary_west',
        'boundary_south',

        'noc_number',
        'district',
        'revenue_circle',
        'village',
        'mouza',
        'dag_no',
        'patta_no',
    ];
}