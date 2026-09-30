import "./events";

import {
    initializePanVisibility
} from "./handlers/pan-handler";

import {
    handlePartyTypeChange
} from "./handlers/main";

import {
    getPartyFields
} from "../../services/party/party-fields.service";

import {
    initializeRelationshipStatus
} from "./handlers/relationship-handler";

import {
    initializeGender
} from "./handlers/gender-handler";

import {
    validateVisibleFields
} from "./handlers/save-validation";

declare global {
    interface Window {
        partyEntry: any;
    }
}

document.addEventListener(
    "DOMContentLoaded",
    async () => {

        console.log("MAIN LOADED");

        // =========================
        // FORM SUBMIT HANDLER (ALWAYS FIRST)
        // =========================

        const form =
            document.getElementById("partyForm") as HTMLFormElement;

        if (form) {

            form.addEventListener("submit", async (e) => {

                e.preventDefault(); // 🔴 STOP NORMAL SUBMIT

                console.log("Submit intercepted");

                const valid =
                    validateVisibleFields();

                if (!valid) {
                    console.log("Validation failed");
                    return;
                }

                // =========================
                // BUILD FORM DATA
                // =========================

                const formData =
                    new FormData(form);

                // =========================
                // SEND TO LARAVEL
                // =========================

                const response =
                    await fetch("api/party/save", {
                        method: "POST",
                        body: formData,
                        headers: {
                            "X-Requested-With": "XMLHttpRequest",
                            "X-CSRF-TOKEN":
                                (
                                    document.querySelector(
                                        'meta[name="csrf-token"]'
                                    ) as HTMLMetaElement
                                ).content
                        }
                    });

                const result =
                    await response.json();

                console.log("SAVE RESULT:", result);

                // =========================
                // SUCCESS FLOW
                // =========================

                if (result.status === "SUCCESS") {

                    alert("Party saved successfully");

                    window.location.reload();
                }
            });
        }


        // =========================
        // RESTORE DATA
        // =========================

        const partyEntry =
            window.partyEntry || {};

        if (!partyEntry.party_type_id) {
            console.log("No party entry found");
            return;
        }

        const partyTypeEl =
            document.getElementById(
                "partytype"
            ) as HTMLSelectElement;

        const tomPartyType =
            (partyTypeEl as any).tomselect;

        console.log(
            "Restoring Party Type:",
            partyEntry.party_type_id
        );

        tomPartyType.setValue(
            String(
                partyEntry.party_type_id
            )
        );

        await handlePartyTypeChange(
            partyTypeEl
        );

        const categoryEl =
            document.getElementById(
                "partycategory"
            ) as HTMLSelectElement;

        const tomCategory =
            (categoryEl as any).tomselect;

        console.log(
            "Restoring Category:",
            partyEntry.party_category_id
        );



        setTimeout(async () => {

            tomCategory.setValue(
                String(
                    partyEntry.party_category_id
                )
            );

            const exemptionWrapper = document.getElementById(
                "exemptionFromAppearanceWrapper"
            );

            const exemptionEl =
                document.getElementById(
                    "exemptionFromAppearance"
                ) as HTMLSelectElement;

            const tomExemption =
                (exemptionEl as any)?.tomselect;

            if (String(partyEntry.party_category_id) === "49") {

                exemptionWrapper?.classList.remove("hidden");

                if (tomExemption && partyEntry.exemption_under_88) {

                    console.log(
                        "Restoring Exemption:",
                        partyEntry.exemption_under_88
                    );

                    tomExemption.setValue(
                        String(partyEntry.exemption_under_88)
                    );

                }

            } else {
                exemptionWrapper?.classList.add("hidden");
            }


            tomPartyType.disable();
            tomCategory.disable();

            document
                .getElementById(
                    "addPartyBtn"
                )
                ?.classList.add("hidden");

            document
                .getElementById(
                    "partyFormWrapper"
                )
                ?.classList.remove("hidden");

            const response =
                await getPartyFields();

            console.log(
                "Initializing field logic"
            );

            initializePanVisibility();
            initializeRelationshipStatus();
            initializeGender();

            response.fields.forEach(
                (field: any) => {

                    console.log(
                        field.field_id_name_en
                    );

                }
            );

            console.log(
                "Party Fields Loaded",
                response
            );

        }, 500);

    }
);
document.addEventListener('click', async (event) => {
    const button = (event.target as HTMLElement).closest(
        '.delete-party-btn'
    ) as HTMLElement | null;

    if (!button) return;

    const id = button.dataset.id;

    if (!id) return;

    if (!confirm('Are you sure you want to delete this party?')) {
        return;
    }

    try {
        const response = await fetch(`/party/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN':
                    document.querySelector<HTMLMetaElement>(
                        'meta[name="csrf-token"]'
                    )?.content || '',
                'Accept': 'application/json',
            },
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(
                data.message || 'Failed to delete party.'
            );
        }

        document
            .getElementById(`party-${id}`)
            ?.remove();

    } catch (error) {
        console.error(error);

        alert(
            error instanceof Error
                ? error.message
                : 'Failed to delete party.'
        );
    }
});