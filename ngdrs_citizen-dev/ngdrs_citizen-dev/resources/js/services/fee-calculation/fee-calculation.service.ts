import api from "../../utils/http/axios";
import {
    FeeRuleChecklistResult,
    FeeRuleItemsResult,
    CalculateFeesPayload,
    CalculateFeesResult,
    ExemptionResult,
    ZonalValuationResult
} from "../../types/fee-calculation.types";

/**
 * Fetch fee rule checklist for article.
 */
export async function fetchFeeRuleChecklist(
    articleId: string = "91"
): Promise<FeeRuleChecklistResult> {
    try {
        const { data } = await api.post("/v1/fee-calculation/rules", {
            article_id: articleId,
        }, {
            loadingMessage: "Loading Fee Rules...",
            loadingSubtext: "Retrieving registration fee rules"
        });

        if (!data?.success) {
            return {
                success: false,
                message: data?.message || "Failed to fetch fee rule checklist",
                rules: null,
            };
        }

        return {
            success: true,
            message: data.message || "Fee rule checklist retrieved successfully",
            rules: data.data,
        };
    } catch (error: any) {
        const message =
            error?.response?.data?.message ||
            error?.message ||
            "Unable to connect to fee calculation services.";
        return {
            success: false,
            message,
            rules: null,
        };
    }
}

/**
 * Fetch fee rule items.
 */
export async function fetchFeeRuleItems(
    feeRuleId: string,
    articleId: string = "91",
    docTokenNo: string = "202400004497",
    lang: string = "en"
): Promise<FeeRuleItemsResult> {
    try {
        const { data } = await api.post("/v1/fee-calculation/rule-items", {
            fee_rule_id: feeRuleId,
            article_id: articleId,
            doc_token_no: docTokenNo,
            lang: lang,
        }, {
            loadingMessage: "Loading Rule Parameters...",
            loadingSubtext: "Fetching applicable parameters and rates"
        });

        if (!data?.success) {
            return {
                success: false,
                message: data?.message || "Failed to fetch fee rule items",
                feeItems: null,
            };
        }

        return {
            success: true,
            message: data.message || "Fee rule items retrieved successfully",
            feeItems: data.data?.fee_items ?? [],
        };
    } catch (error: any) {
        const message =
            error?.response?.data?.message ||
            error?.message ||
            "Unable to connect to fee calculation services.";
        return {
            success: false,
            message,
            feeItems: null,
        };
    }
}

/**
 * Calculate registration fees.
 */
export async function calculateFees(
    payload: CalculateFeesPayload
): Promise<CalculateFeesResult> {
    try {
        const { data } = await api.post("/v1/fee-calculation/calculate", payload, {
            loadingMessage: "Calculating Registration Fees...",
            loadingSubtext: "Evaluating stamp duty & fee breakdown"
        });

        if (!data?.success) {
            return {
                success: false,
                message: data?.message || "Failed to calculate fees",
                totalFee: null,
                calculationDetails: null,
            };
        }

        const calcData = data.data ?? data;

        return {
            success: true,
            message: data.message || "Fees calculated successfully",
            feeCalcId: calcData?.fee_calc_id ?? null,
            totalFee: calcData?.total_fee ?? 0,
            calculationDetails: calcData?.calculation_details ?? [],
        };
    } catch (error: any) {
        const message =
            error?.response?.data?.message ||
            error?.message ||
            "Unable to connect to fee calculation services.";
        return {
            success: false,
            message,
            totalFee: null,
            calculationDetails: null,
        };
    }
}

/**
 * Calculate Market Value.
 */
export async function calculateMarketValue(
    feeRuleId: number,
    faa: number,
    param?: Record<string, any>
): Promise<any> {
    try {
        const { data } = await api.post("/v1/fee-calculation/calculate-mv", {
            fee_rule_id: feeRuleId,
            FAA: faa,
            param: param ?? { FAA: faa },
        }, {
            loadingMessage: "Calculating Market Value...",
            loadingSubtext: "Evaluating property classification rules"
        });

        return data;
    } catch (error: any) {
        const message =
            error?.response?.data?.message ||
            error?.message ||
            "Unable to calculate market value.";
        return {
            success: false,
            message,
        };
    }
}

/**
 * Verify service number and apply ex-servicemen exemption.
 */
export async function verifyExServicemenExemption(
    tokenNo: string,
    serviceNo: string,
    articleId?: number,
    currentTotalFee?: number
): Promise<ExemptionResult> {
    try {
        const { data } = await api.post("/v1/fee-calculation/exemptions/ex-serviceman", {
            token_no: tokenNo,
            service_no: serviceNo,
            article_id: articleId,
            current_total_fee: currentTotalFee,
        }, {
            loadingMessage: "Verifying Exemption...",
            loadingSubtext: "Checking ex-servicemen service record"
        });

        if (!data?.success) {
            return {
                success: false,
                message: data?.message || "Failed to verify service number.",
            };
        }

        const resData = data.data ?? data;
        const newFee = resData?.summary?.new_total_fee ?? data.new_total_fee ?? data.new_fee ?? resData.new_total_fee ?? resData.new_fee ?? resData.total_fee;
        const originalFee = resData?.summary?.original_total_fee ?? data.original_fee ?? resData.original_fee;
        const exemptionAmount = resData?.summary?.total_exemption_amount ?? data.exemption_amount ?? resData.exemption_amount ?? resData.exemption_amt;

        return {
            success: true,
            message: data.message || "Exemption applied successfully",
            originalFee: originalFee !== undefined && originalFee !== null ? Number(originalFee) : null,
            exemptionAmount: exemptionAmount !== undefined && exemptionAmount !== null ? Number(exemptionAmount) : null,
            newTotalFee: newFee !== undefined && newFee !== null ? Number(newFee) : null,
            newFee: newFee !== undefined && newFee !== null ? Number(newFee) : null,
            totalFee: newFee !== undefined && newFee !== null ? Number(newFee) : null,
            data: resData,
        };
    } catch (error: any) {
        const message =
            error?.response?.data?.message ||
            error?.message ||
            "Unable to connect to fee calculation services.";
        return {
            success: false,
            message,
        };
    }
}

/**
 * Remove exemption.
 */
export async function removeExemption(
    tokenNo: string
): Promise<ExemptionResult> {
    try {
        const { data } = await api.delete("/v1/fee-calculation/exemptions", {
            data: {
                token_no: tokenNo,
            },
            loadingMessage: "Removing Exemption...",
            loadingSubtext: "Restoring standard calculation amounts"
        });

        if (!data?.success) {
            return {
                success: false,
                message: data?.message || "Failed to remove exemption.",
            };
        }

        return {
            success: true,
            message: data.message || "Exemption removed successfully",
        };
    } catch (error: any) {
        const message =
            error?.response?.data?.message ||
            error?.message ||
            "Unable to connect to fee calculation services.";
        return {
            success: false,
            message,
        };
    }
}

/**
 * Fetch dynamic zonal valuation and consideration amount for a token/property.
 * Ported from legacy CakePHP get_valuation_amt_sd().
 */
export async function fetchZonalValuation(
    tokenNo?: string,
    articleId: string = "91",
    propertyId?: number
): Promise<ZonalValuationResult> {
    try {
        const { data } = await api.post("/v1/fee-calculation/valuation", {
            token_no: tokenNo,
            article_id: articleId,
            property_id: propertyId,
        });

        if (!data?.success) {
            return {
                success: false,
                message: data?.message || "Failed to fetch zonal valuation",
                data: null,
            };
        }

        return {
            success: true,
            message: data.message || "Zonal valuation fetched successfully",
            data: data.data,
        };
    } catch (error: any) {
        const message =
            error?.response?.data?.message ||
            error?.message ||
            "Unable to fetch zonal valuation.";
        return {
            success: false,
            message,
            data: null,
        };
    }
}

