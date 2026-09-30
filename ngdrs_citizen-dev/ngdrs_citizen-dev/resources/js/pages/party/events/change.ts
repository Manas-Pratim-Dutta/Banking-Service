import {
    handlePartyTypeChange
} from "../handlers/main";

import {
    setExemptionUnder88
} from "../../../services/party/party.services";

const partyType =
    document.getElementById(
        "partytype"
    ) as HTMLSelectElement;

partyType?.addEventListener(
    "change",
    function () {

        handlePartyTypeChange(this);

    }
);

const partyCategory =
    document.getElementById(
        "partycategory"
    ) as HTMLSelectElement;

partyCategory?.addEventListener(
    "change",
    function (this: HTMLSelectElement) {

        const POA_CATEGORY_ID = "XX"; // ← replace with actual Power of Attorney category id

        if (this.value === "4") {

            const poaApplicable =
                (window as any).poa_applicable;

            if (poaApplicable === "N") {

                alert(
                    "Please first add the party types before selecting Power of Attorney."
                );

                // Reset selection since POA isn't allowed yet
                const tomCategory =
                    (this as any).tomselect;

                if (tomCategory) {
                    tomCategory.setValue("");
                } else {
                    this.value = "";
                }

                return;
            }

        }

        const exemptionWrapper =
            document.getElementById(
                "exemptionFromAppearanceWrapper"
            );

        if (this.value === "49") {
            exemptionWrapper?.classList.remove("hidden");
        } else {
            exemptionWrapper?.classList.add("hidden");

            const exemptionSelect =
                document.getElementById(
                    "exemptionFromAppearance"
                ) as HTMLSelectElement;

            if (exemptionSelect) exemptionSelect.value = "";
        }

    }
);
const exemptionSelect =
    document.getElementById(
        "exemptionFromAppearance"
    ) as HTMLSelectElement;

exemptionSelect?.addEventListener(
    "change",
    async function (this: HTMLSelectElement) {

        if (this.value === "Y") {

            try {
                await setExemptionUnder88(this.value);
            } catch (error) {
                console.error(error);
            }

        } else if (this.value === "N") {

            try {
                await setExemptionUnder88(this.value);
            } catch (error) {
                console.error(error);
            }

            document
                .getElementById("addPartyBtn")
                ?.classList.remove("hidden");

            document
                .getElementById("partyFormWrapper")
                ?.classList.add("hidden");

        }

    }
);