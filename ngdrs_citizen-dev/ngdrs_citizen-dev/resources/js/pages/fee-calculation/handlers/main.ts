import { byId } from "../../../utils/ui/dom";
import { notyf } from "../../../utils/ui/notyf";
import {
    fetchFeeRuleChecklist,
    fetchFeeRuleItems,
    fetchZonalValuation,
    calculateFees,
    verifyExServicemenExemption,
    removeExemption
} from "../../../services/fee-calculation/fee-calculation.service";
import { setTomSelectMapOptions } from "../../../utils/ui/setTomSelect";
import { CalculateFeesPayload, CalculateFeesResult, ExemptionData, FeeCalculationDetail } from "../../../types/fee-calculation.types";

let calculationPerformed = false;
let currentTokenNo = "";
let currentArticleId = "91";
let lastCalculationResult: CalculateFeesResult | null = null;
let originalCalculationResult: CalculateFeesResult | null = null;
let exemptionApplied = false;

export async function handleExServicemenToggle(el: HTMLElement) {
    const toggleExServicemen = el as HTMLInputElement;
    const sectionExServicemen = byId("section-ex-servicemen");

    if (toggleExServicemen.checked) {
        if (!calculationPerformed) {
            notyf.error("Please perform calculation first.");
            toggleExServicemen.checked = false;
            if (sectionExServicemen) {
                sectionExServicemen.classList.add("hidden");
            }
            return;
        }

        const isConfirmed = await window.showAlertModal("ex-servicemen-disclaimer-modal");

        if (isConfirmed) {
            if (sectionExServicemen) {
                sectionExServicemen.classList.remove("hidden");
            }
        } else {
            toggleExServicemen.checked = false;
            if (sectionExServicemen) {
                sectionExServicemen.classList.add("hidden");
            }
        }
    } else {
        if (exemptionApplied) {
            const isConfirmed = await window.showAlertModal("ex-servicemen-remove-modal");
            if (isConfirmed) {
                const result = await removeExemption(currentTokenNo);

                if (result.success) {
                    if (originalCalculationResult) {
                        lastCalculationResult = JSON.parse(JSON.stringify(originalCalculationResult));
                    }
                    exemptionApplied = false;
                    if (lastCalculationResult) {
                        renderCalculationResult(lastCalculationResult);
                    }
                    notyf.success("Exemption removed successfully.");

                    if (sectionExServicemen) {
                        sectionExServicemen.classList.add("hidden");
                    }
                    const detailsCard = byId("ex-servicemen-details-card");
                    if (detailsCard) {
                        detailsCard.classList.add("hidden");
                    }
                    const serviceNoEl = byId("service-no-input") as HTMLInputElement | null;
                    if (serviceNoEl) {
                        serviceNoEl.value = "";
                        serviceNoEl.disabled = false;
                    }
                    const verifyBtn = byId("verify-ex-servicemen-btn") as HTMLButtonElement | null;
                    if (verifyBtn) {
                        verifyBtn.disabled = false;
                        verifyBtn.textContent = "Click to Verify";
                    }
                } else {
                    notyf.error(result.message || "Failed to remove exemption.");
                    toggleExServicemen.checked = true;
                }
            } else {
                toggleExServicemen.checked = true;
            }
        } else {
            if (sectionExServicemen) {
                sectionExServicemen.classList.add("hidden");
            }
        }
    }
}

export function handleTeaGardenToggle(el: HTMLElement) {
    const toggleTeaGarden = el as HTMLInputElement;
    const sectionTeaGarden = byId("section-tea-garden");
    if (sectionTeaGarden) {
        sectionTeaGarden.classList.toggle("hidden", !toggleTeaGarden.checked);
    }
}

export async function handleFeeRuleChange(el: HTMLElement) {
    const feeRuleSelect = el as HTMLSelectElement;
    const feeRuleCard = byId("fee-rule-card");
    if (!feeRuleCard) return;

    if (!feeRuleSelect.value) {
        feeRuleCard.classList.add("hidden");
        return;
    }

    const tokenEl = byId("pre-registration-no");
    const tokenInput = byId("token-no") as HTMLInputElement | null;
    const tokenNo = tokenEl?.textContent?.trim() || tokenInput?.value?.trim() || "";
    const result = await fetchFeeRuleItems(feeRuleSelect.value, currentArticleId, tokenNo);
    if (!result.success) {
        notyf.error(result.message);
        feeRuleCard.classList.add("hidden");
        return;
    }

    const feeItems = result.feeItems || [];
    const presentCodes = new Set(feeItems.map(item => item.fee_param_code));
    const resolvedValues = new Map<string, any>();
    feeItems.forEach(item => {
        resolvedValues.set(item.fee_param_code, item.resolved_value);
    });

    const STATIC_PARAM_CODES = ["FAA", "FBO", "FBG", "FBQ", "FBR", "FCA"];
    STATIC_PARAM_CODES.forEach(code => {
        const isHidden = !presentCodes.has(code);
        toggleElementVisibility(code, isHidden);

        if (!isHidden) {
            const element = byId(code) as HTMLInputElement | null;
            if (element) {
                const resolvedVal = resolvedValues.get(code);
                if (["FBG", "FBQ", "FBR", "FCA"].includes(code)) {
                    if (resolvedVal !== null && resolvedVal !== undefined && resolvedVal !== "") {
                        element.checked = Number(resolvedVal) === 1;
                        element.disabled = true;
                    }
                } else {
                    const parsedVal = resolvedVal !== null && resolvedVal !== undefined ? Number(resolvedVal) : 0;
                    if (code === "FAA" && parsedVal <= 0) {
                        const currentFaa = Number(element.value) || 0;
                        if (currentFaa <= 0 && tokenNo) {
                            syncZonalValuation(tokenNo, currentArticleId);
                        }
                    } else {
                        element.value = resolvedVal !== null && resolvedVal !== undefined ? String(resolvedVal) : "0";
                    }
                }
            }
        }
    });

    // Check if valuation / consideration amount is required for this rule
    // In registration rules, valuation applies when FAA (Zonal Value) is in fee_items or explicitly declared
    const hasValuation = presentCodes.has("FAA") || feeItems.some(item =>
        item.fee_param_code?.toUpperCase() === "CONS_AMT" ||
        item.description?.toLowerCase().includes("consideration")
    );

    toggleElementVisibility("consideration-amount", !hasValuation);

    // Hide or show the valuation inputs grid container if all contained inputs are hidden
    const valuationGrid = byId("valuation-inputs-grid");
    if (valuationGrid) {
        const hasVisibleInputs = hasValuation || presentCodes.has("FAA");
        valuationGrid.classList.toggle("hidden", !hasVisibleInputs);
    }

    // If consideration is not required, reset input value
    const consInput = byId("consideration-amount") as HTMLInputElement | null;
    if (consInput && !hasValuation) {
        consInput.value = "";
    }

    const rulesContainer = byId("fee-rules-toggles-container");
    if (rulesContainer) {
        const toggleCodes = ["FBG", "FBQ", "FBR", "FCA"];
        const allTogglesHidden = toggleCodes.every(code => !presentCodes.has(code));
        rulesContainer.classList.toggle("hidden", allTogglesHidden);
    }

    feeRuleCard.classList.remove("hidden");
}

function toggleElementVisibility(paramCode: string, isHidden: boolean) {
    const el = byId(paramCode);
    if (!el) return;

    let targetEl: HTMLElement | null = el;
    if (paramCode === "FAA") {
        targetEl = el.parentElement;
    } else if (paramCode === "consideration-amount") {
        targetEl = byId("consideration-amount-wrapper") || el.parentElement;
    } else if (["FBG", "FBQ", "FBR", "FCA"].includes(paramCode)) {
        targetEl = el.parentElement?.parentElement ?? null;
    }

    if (targetEl) {
        targetEl.classList.toggle("hidden", isHidden);
    }
}

export async function handleCalculate() {
    const btn = byId("calculate-btn") as HTMLButtonElement | null;
    if (btn) {
        btn.disabled = true;
    }

    try {
        const feeRuleSelect = byId("fee-rule-select") as HTMLSelectElement | null;
        if (!feeRuleSelect || !feeRuleSelect.value) {
            notyf.error("Please select a Fee Rule first.");
            return;
        }

        const faaInput = byId("FAA") as HTMLInputElement | null;
        const faaValue = faaInput ? Number(faaInput.value) || 0 : 0;

        const consInput = byId("consideration-amount") as HTMLInputElement | null;
        const consWrapper = byId("consideration-amount-wrapper") || consInput?.parentElement;
        const isConsVisible = consWrapper ? !consWrapper.classList.contains("hidden") : true;
        const consAmt = consInput && isConsVisible ? Number(consInput.value) || 0 : 0;

        if (isConsVisible && !consAmt) {
            notyf.error("Please enter a valid Consideration Amount.");
            return;
        }

        const tokenEl = byId("pre-registration-no");
        const tokenInput = byId("token-no") as HTMLInputElement | null;
        const tokenNo = tokenEl?.textContent?.trim() || tokenInput?.value?.trim() || "";

        if (!tokenNo) {
            notyf.error("Pre-registration number is missing in session. Please start or select an application first.");
            return;
        }

        const propEl = document.querySelector(".property-id-val");
        const propertyId = propEl ? Number(propEl.textContent?.trim()) || 1 : 1;

        const feeRuleId = Number(feeRuleSelect.value) || 1;

        currentTokenNo = tokenNo;

        const payload: CalculateFeesPayload = {
            token_no: currentTokenNo,
            property_id: propertyId,
            article_id: Number(currentArticleId) || 91,
            village_id: 2088,
            FAA: faaValue,
            cons_amt: consAmt,
            user_id: 1,
            req_ip: "127.0.0.1",
            state_id: 15,
            fee_rule_id: feeRuleId,
            subrule_id: feeRuleId,
            context_params: {
                FAA: faaValue,
                FBG: (byId("FBG") as HTMLInputElement)?.checked ? 1 : 0,
                FCA: (byId("FCA") as HTMLInputElement)?.checked ? 1 : 0,
                FBR: (byId("FBR") as HTMLInputElement)?.checked ? 1 : 0,
                FBQ: (byId("FBQ") as HTMLInputElement)?.checked ? 1 : 0,
            }
        };
        console.log(payload);

        const result = await calculateFees(payload);

        if (!result.success) {
            notyf.error(result.message);
            return;
        }

        // Render dynamic calculation details and update total
        renderCalculationResult(result);

        notyf.success("Fees calculated successfully!");
        calculationPerformed = true;
        lastCalculationResult = JSON.parse(JSON.stringify(result));
        originalCalculationResult = JSON.parse(JSON.stringify(result));
        exemptionApplied = false;

        // Reset ex-servicemen inputs and buttons upon a fresh calculation
        const toggleExServicemen = byId("toggle-ex-servicemen") as HTMLInputElement | null;
        if (toggleExServicemen) {
            toggleExServicemen.checked = false;
        }
        const sectionExServicemen = byId("section-ex-servicemen");
        if (sectionExServicemen) {
            sectionExServicemen.classList.add("hidden");
        }
        const detailsCard = byId("ex-servicemen-details-card");
        if (detailsCard) {
            detailsCard.classList.add("hidden");
        }
        const serviceNoEl = byId("service-no-input") as HTMLInputElement | null;
        if (serviceNoEl) {
            serviceNoEl.value = "";
            serviceNoEl.disabled = false;
        }
        const verifyBtn = byId("verify-ex-servicemen-btn") as HTMLButtonElement | null;
        if (verifyBtn) {
            verifyBtn.disabled = false;
            verifyBtn.textContent = "Click to Verify";
        }

    } catch (err: any) {
        notyf.error(err.message || "An error occurred during calculation.");
    } finally {
        if (btn) {
            btn.disabled = false;
        }
    }
}

export async function syncZonalValuation(tokenNo: string, articleId: string, propertyId?: number): Promise<void> {
    if (!tokenNo) return;
    try {
        const result = await fetchZonalValuation(tokenNo, articleId, propertyId);
        if (result.success && result.data) {
            const faaInput = byId("FAA") as HTMLInputElement | null;
            if (faaInput && (Number(faaInput.value) <= 0 || !faaInput.value)) {
                faaInput.value = String(result.data.val);
            }
            const consInput = byId("consideration-amount") as HTMLInputElement | null;
            if (consInput && (!consInput.value || Number(consInput.value) <= 0) && result.data.cons > 0) {
                consInput.value = String(result.data.cons);
            }

            // Dynamically sync parameter flags (Municipality, Metro City, Home Visit, Tatkal)
            const syncToggle = (id: string, val?: number) => {
                if (val !== undefined && val !== null) {
                    const cb = byId(id) as HTMLInputElement | null;
                    if (cb) {
                        cb.checked = Number(val) === 1;
                    }
                }
            };

            syncToggle("FBG", result.data.FBG ?? result.data.municipality_flag);
            syncToggle("FBQ", result.data.FBQ ?? result.data.metro_city_flag);
            syncToggle("FBR", result.data.FBR ?? result.data.home_visit_flag);
            syncToggle("FCA", result.data.FCA ?? result.data.tatkal_flag);
        }
    } catch (e) {
        console.warn("Failed to sync zonal valuation:", e);
    }
}

export async function initFeeRuleChecklist(): Promise<void> {
    const urlParams = new URLSearchParams(window.location.search);
    currentArticleId = urlParams.get("article_id") || (byId("article-id") as HTMLInputElement)?.value || "87";

    const tokenEl = byId("pre-registration-no");
    const tokenInput = byId("token-no") as HTMLInputElement | null;
    const tokenNo = tokenEl?.textContent?.trim() || tokenInput?.value?.trim() || "";

    // Sync zonal valuation on load if FAA is 0 or empty
    const faaInput = byId("FAA") as HTMLInputElement | null;
    if (faaInput && (Number(faaInput.value) <= 0 || !faaInput.value) && tokenNo) {
        syncZonalValuation(tokenNo, currentArticleId);
    }

    const result = await fetchFeeRuleChecklist(currentArticleId);
    if (!result.success) {
        notyf.error(result.message);
        return;
    }
    if (result.rules) {
        setTomSelectMapOptions("fee-rule-select", result.rules);
    }
}

function renderCalculationResult(result: CalculateFeesResult, exemptionDiscount?: number) {
    const breakdownContainer = byId("payment-breakdown");
    if (breakdownContainer) {
        if (result.calculationDetails && result.calculationDetails.length > 0) {
            breakdownContainer.innerHTML = result.calculationDetails.map(detail => {
                const minVal = detail.min_value !== null && detail.min_value !== undefined
                    ? `₹${Number(detail.min_value).toLocaleString('en-IN')}`
                    : 'NA';
                const maxVal = detail.max_value !== null && detail.max_value !== undefined
                    ? `₹${Number(detail.max_value).toLocaleString('en-IN')}`
                    : 'NA';
                const totalVal = detail.final_value !== null && detail.final_value !== undefined
                    ? Number(detail.final_value).toLocaleString('en-IN')
                    : '0';
                const expBadge = detail.exp_amt && Number(detail.exp_amt) > 0
                    ? `
                        <div class="flex justify-between items-center text-xs text-emerald-700 bg-emerald-50/80 px-2 py-1 rounded border border-emerald-200/60 font-medium">
                            <span>Ex-Servicemen Concession</span>
                            <span class="font-semibold">-₹${Number(detail.exp_amt).toLocaleString('en-IN')}</span>
                        </div>
                    `
                    : '';

                return `
                    <div class="space-y-2 pb-4 border-b border-dotted border-gray-200 last:border-0 last:pb-0">
                        <div class="flex justify-between items-center text-sm font-medium text-gray-800">
                            <span>${detail.fee_subrule_desc}</span>
                            <span class="font-semibold text-gray-900">₹${Number(detail.final_value).toLocaleString('en-IN')}</span>
                        </div>
                        ${expBadge}
                        <div class="flex justify-between items-center text-xs text-gray-500">
                            <span>Min.</span>
                            <span>${minVal}</span>
                        </div>
                        <div class="flex justify-between items-center text-xs text-gray-500">
                            <span>Max.</span>
                            <span>${maxVal}</span>
                        </div>
                        <div class="flex justify-between items-center text-xs text-gray-500">
                            <span>Calculation</span>
                            <span class="font-mono text-gray-600">${detail.calc_detail || 'NA'}</span>
                        </div>
                        <div class="flex justify-between items-center text-xs text-gray-500">
                            <span>Total</span>
                            <span class="text-gray-600">₹${totalVal}</span>
                        </div>
                    </div>
                `;
            }).join("");
        } else {
            breakdownContainer.innerHTML = `
                <div class="text-sm text-gray-500 text-center py-2">
                    No calculation details available
                </div>
            `;
        }
    }

    const totalContainer = byId("payment-total");
    if (totalContainer) {
        const totalVal = result.totalFee ?? 0;
        totalContainer.textContent = `₹${totalVal.toLocaleString('en-IN')}`;
    }

    const discountEl = byId("exemption-discount");
    if (discountEl) {
        if (exemptionApplied) {
            const discountVal = exemptionDiscount ?? (
                originalCalculationResult && result.totalFee !== null && originalCalculationResult.totalFee !== null
                    ? originalCalculationResult.totalFee - result.totalFee
                    : 0
            );
            if (discountVal > 0) {
                discountEl.textContent = `Ex-Servicemen Concession: -₹${Number(discountVal).toLocaleString('en-IN')}`;
                discountEl.classList.remove("hidden");
            } else {
                discountEl.classList.add("hidden");
            }
        } else {
            discountEl.classList.add("hidden");
        }
    }
}

function renderExemptionDetailsCard(exData: ExemptionData) {
    const card = byId("ex-servicemen-details-card");
    if (!card) return;

    const badge = byId("ex-service-no-badge");
    if (badge) {
        badge.textContent = `No: ${exData.service_no || ''}`;
    }

    const itemsContainer = byId("ex-servicemen-items-table");
    if (itemsContainer) {
        if (exData.fee_items && exData.fee_items.length > 0) {
            itemsContainer.innerHTML = exData.fee_items.map(item => {
                const isDiscounted = item.exemption_amount > 0;
                return `
                    <div class="flex justify-between items-center py-1 border-b border-emerald-100/80 last:border-0">
                        <div class="flex items-center gap-1.5">
                            <span class="font-medium text-emerald-900">${item.fee_item_desc}</span>
                            ${item.concession_percent > 0 ? `<span class="px-1.5 py-0.2 bg-emerald-200/80 text-emerald-800 text-[10px] font-bold rounded">-${item.concession_percent}%</span>` : ''}
                        </div>
                        <div class="text-right">
                            ${isDiscounted ? `<span class="line-through text-emerald-600/70 mr-1.5 text-[11px]">₹${Number(item.original_value).toLocaleString('en-IN')}</span>` : ''}
                            <span class="font-semibold text-emerald-950">₹${Number(item.final_value).toLocaleString('en-IN')}</span>
                            ${isDiscounted ? `<div class="text-[10px] text-emerald-700 font-medium">Saved ₹${Number(item.exemption_amount).toLocaleString('en-IN')}</div>` : ''}
                        </div>
                    </div>
                `;
            }).join("");
        } else if (exData.summary) {
            const sum = exData.summary;
            itemsContainer.innerHTML = `
                <div class="flex justify-between items-center py-1 border-b border-emerald-100/80">
                    <span class="text-emerald-900">Registration Fee (75% Concession)</span>
                    <span class="font-semibold text-emerald-950">₹${Number(sum.reg_fee_payable_25 ?? 0).toLocaleString('en-IN')}</span>
                </div>
                <div class="flex justify-between items-center py-1">
                    <span class="text-emerald-900">Stamp Duty</span>
                    <span class="font-semibold text-emerald-950">₹${Number(sum.stamp_duty_payable ?? 0).toLocaleString('en-IN')}</span>
                </div>
            `;
        }
    }

    const totalSavings = byId("ex-total-savings");
    if (totalSavings) {
        const totalDiscount = exData.summary?.total_exemption_amount ?? exData.exemption_amount ?? exData.exemption_amt ?? 0;
        totalSavings.textContent = `-₹${Number(totalDiscount).toLocaleString('en-IN')}`;
    }

    card.classList.remove("hidden");
}

export async function handleVerifyExServicemen() {
    const serviceNoEl = byId("service-no-input") as HTMLInputElement | null;
    const serviceNo = serviceNoEl?.value?.trim();

    if (!serviceNo) {
        notyf.error("Please enter a valid Service Number.");
        return;
    }

    if (!calculationPerformed || !currentTokenNo) {
        notyf.error("Please calculate fees first.");
        return;
    }

    if (exemptionApplied) {
        notyf.error("Exemption has already been applied.");
        return;
    }

    const btn = byId("verify-ex-servicemen-btn") as HTMLButtonElement | null;
    if (btn) {
        btn.disabled = true;
    }

    try {
        const currentFee = lastCalculationResult?.totalFee ?? 0;
        const result = await verifyExServicemenExemption(currentTokenNo, serviceNo, Number(currentArticleId) || 91, currentFee);

        if (result.success && result.data) {
            const exData = result.data;
            const new_fee = exData.summary?.new_total_fee ?? result.newTotalFee ?? result.newFee ?? exData.new_total_fee ?? result.totalFee;
            const total_fee = Number(new_fee ?? 0);
            const total_exemption = exData.summary?.total_exemption_amount ?? result.exemptionAmount ?? exData.exemption_amount ?? 0;

            exemptionApplied = true;

            // Update calculationDetails if provided in response
            if (exData.calculation_details && exData.calculation_details.length > 0) {
                const updatedDetails: FeeCalculationDetail[] = exData.calculation_details.map(cd => {
                    const originalDetail = originalCalculationResult?.calculationDetails?.find(
                        d => d.fee_item_id === cd.fee_item_id
                    );
                    return {
                        fee_subrule_desc: cd.fee_subrule_desc,
                        calc_detail: cd.exp_amt ? `Concession: -₹${Number(cd.exp_amt).toLocaleString('en-IN')}` : (originalDetail?.calc_detail || 'NA'),
                        fee_subrule_id: originalDetail?.fee_subrule_id ?? 0,
                        fee_item_id: cd.fee_item_id,
                        min_value: originalDetail?.min_value ?? null,
                        max_value: originalDetail?.max_value ?? null,
                        final_value: cd.final_value,
                        exp_amt: cd.exp_amt,
                        item_type_id: originalDetail?.item_type_id ?? 2,
                    };
                });

                lastCalculationResult = {
                    success: true,
                    message: result.message,
                    totalFee: total_fee,
                    calculationDetails: updatedDetails,
                };
            } else if (lastCalculationResult) {
                lastCalculationResult.totalFee = total_fee;
                if (lastCalculationResult.calculationDetails && exData.new_reg_fee !== undefined) {
                    lastCalculationResult.calculationDetails = lastCalculationResult.calculationDetails.map(detail => {
                        if (detail.fee_item_id === 1) {
                            return { ...detail, final_value: exData.new_reg_fee!, exp_amt: exData.exemption_amount ?? 0 };
                        }
                        return detail;
                    });
                }
            }

            // Update UI
            if (lastCalculationResult) {
                renderCalculationResult(lastCalculationResult, total_exemption);
            }

            // Render detailed concession breakdown card
            renderExemptionDetailsCard(exData);

            notyf.success("Service Number verified and exemption applied successfully!");

            if (serviceNoEl) {
                serviceNoEl.disabled = true;
            }
            if (btn) {
                btn.disabled = true;
                btn.textContent = "Applied";
            }
        } else {
            notyf.error(result.message || "Failed to verify service number.");
        }
    } catch (err: any) {
        const msg = err?.response?.data?.message || err?.message || "An error occurred during verification.";
        notyf.error(msg);
    } finally {
        if (!exemptionApplied && btn) {
            btn.disabled = false;
            btn.textContent = "Click to Verify";
        }
    }
}
