export interface FeeRuleChecklistResponse {
    status: string;
    data: Record<string, string>;
}

export interface FeeRuleChecklistResult {
    success: boolean;
    message: string;
    rules: Record<string, string> | null;
}

export interface FeeRuleItem {
    fee_item_id: number;
    fee_param_code: string;
    description: string | null;
    display_order: number | null;
    is_hidden: boolean;
    list_flag: boolean;
    resolved_value: number | string | null;
    saved_value: number | string | null;
    options: any[];
}

export interface FeeRuleItemsResponse {
    status: string;
    data: {
        fee_items: FeeRuleItem[];
        optional_fees: any[];
    };
}

export interface FeeRuleItemsResult {
    success: boolean;
    message: string;
    feeItems: FeeRuleItem[] | null;
}

export interface CalculateFeesPayload {
    token_no: string;
    property_id: number;
    article_id: number;
    village_id?: number | null;
    FAA: number;
    cons_amt: number;
    user_id?: number;
    req_ip?: string;
    state_id?: number;
    context_params?: Record<string, any>;
    [key: string]: any;
}

export interface FeeCalculationDetail {
    fee_subrule_desc: string;
    calc_detail: string;
    fee_subrule_id: number;
    fee_item_id: number;
    min_value: number | null;
    max_value: number | null;
    final_value: number;
    exp_amt?: number | null;
    item_type_id: number;
}

export interface CalculateFeesResponse {
    status?: string;
    success?: boolean;
    message?: string;
    data: {
        fee_calc_id: number;
        token_no?: string;
        property_id?: number;
        article_id?: number;
        total_fee: number;
        calculation_details: FeeCalculationDetail[];
    };
}

export interface CalculateFeesResult {
    success: boolean;
    message: string;
    feeCalcId?: number | null;
    totalFee: number | null;
    calculationDetails: FeeCalculationDetail[] | null;
}

export interface ExemptionSummary {
    original_total_fee: number;
    total_exemption_amount: number;
    new_total_fee: number;
    reg_fee_original?: number;
    reg_fee_concession_75?: number;
    reg_fee_payable_25?: number;
    stamp_duty_payable?: number;
}

export interface ExemptionFeeItem {
    fee_item_id: number;
    fee_item_desc: string;
    original_value: number;
    concession_percent: number;
    exemption_amount: number;
    final_value: number;
}

export interface ExemptionCalculationDetail {
    fee_item_id: number;
    fee_subrule_desc: string;
    final_value: number;
    exp_amt: number;
}

export interface ExemptionData {
    token_no: string;
    service_no: string;
    article_id: number;
    ex_serviceman_flag: string;
    summary?: ExemptionSummary;
    fee_items?: ExemptionFeeItem[];
    calculation_details?: ExemptionCalculationDetail[];
    original_fee?: number;
    reg_fee?: number;
    stamp_duty?: number;
    exemption_amt?: number;
    exemption_amount?: number;
    new_reg_fee?: number;
    new_fee?: number;
    new_total_fee?: number;
    total_fee?: number;
}

export interface ExemptionResult {
    success: boolean;
    message: string;
    originalFee?: number | null;
    exemptionAmount?: number | null;
    newTotalFee?: number | null;
    newFee?: number | null;
    totalFee?: number | null;
    data?: ExemptionData;
}

export interface ZonalValuationData {
    val: number;
    cons: number;
    party: number;
    property_id?: number;
    val_ids?: number[];
    token_no?: string;
    tatkal_flag?: number;
    home_visit_flag?: number;
    municipality_flag?: number;
    metro_city_flag?: number;
    no_of_pages?: number;
    FCA?: number;
    FBR?: number;
    FBG?: number;
    FBQ?: number;
    FAJ?: number;
}

export interface ZonalValuationResult {
    success: boolean;
    message: string;
    data: ZonalValuationData | null;
}
