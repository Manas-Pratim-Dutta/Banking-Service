import {
    fetchPartyCategory,
    addParty
} from "../../../services/party/party.services";
import {
    initializePanVisibility
} from "./pan-handler";

import {
    getPartyFields
} from "../../../services/party/party-fields.service";

import {
    initializeRelationshipStatus
} from "./relationship-handler";

import {
    initializeGender
} from "./gender-handler";




export async function handlePartyTypeChange(
    el: HTMLElement
) {

    const partyTypeEl =
        el as HTMLSelectElement;


    const partyTypeId =
        partyTypeEl.value;

    console.log(
        "Party Type Changed:",
        partyTypeId
    );

    const categoryEl =
        document.getElementById(
            "partycategory"
        ) as HTMLSelectElement;

    if (!categoryEl) return;

    const tomCategory =
        (categoryEl as any).tomselect;

    tomCategory.clear();

    tomCategory.clearOptions();

    if (!partyTypeId) return;

    try {

        const result =
            await fetchPartyCategory(
                partyTypeId
            );

        if (result.success) {

            tomCategory.clear(true);

            tomCategory.clearOptions();

            tomCategory.addOption({
                value: "",
                text: "Select"
            });

            Object.entries(result.categories)
                .forEach(([value, text]) => {

                    tomCategory.addOption({
                        value: value,
                        text: text as string
                    });

                });

            tomCategory.refreshOptions(false);

            tomCategory.sync();
            console.log(
                "Loaded Categories:",
                result.categories
            );

        }
    } catch (error) {

        console.error(error);

    }
}

// export async function handleAddParty() {

//     const partyTypeEl =
//         document.getElementById(
//             "partytype"
//         ) as HTMLSelectElement;

//     const partyCategoryEl =
//         document.getElementById(
//             "partycategory"
//         ) as HTMLSelectElement;

//     const partyTypeId =
//         partyTypeEl?.value;

//     const partyTypeName =
//         partyTypeEl?.selectedOptions[0]?.text;

//     const partyCategoryId =
//         partyCategoryEl?.value;

//     const partyCategoryName =
//         partyCategoryEl?.selectedOptions[0]?.text;

//     if (!partyTypeId) {

//         alert(
//             "Please select Party Type"
//         );

//         return;

//     }

//     if (!partyCategoryId) {

//         alert(
//             "Please select Party Category"
//         );

//         return;

//     }

//     try {

//         const result =
//             await addParty({

//                 party_type_id:
//                     partyTypeId,

//                 party_type_name:
//                     partyTypeName,

//                 party_category_id:
//                     partyCategoryId,

//                 party_category_name:
//                     partyCategoryName

//             });

//         if (result.success) {

//             window.location.href =
//                 result.redirect_url;

//         }

//     } catch (error) {

//         console.error(error);

//     }

// }

export async function handleAddParty() {

    const partyTypeEl =
        document.getElementById(
            "partytype"
        ) as HTMLSelectElement;

    const partyCategoryEl =
        document.getElementById(
            "partycategory"
        ) as HTMLSelectElement;

    const exemptionEl =
        document.getElementById(
            "exemptionFromAppearance"
        ) as HTMLSelectElement;

    const partyTypeId =
        partyTypeEl?.value;

    const partyTypeName =
        partyTypeEl?.selectedOptions[0]?.text;

    const partyCategoryId =
        partyCategoryEl?.value;

    const partyCategoryName =
        partyCategoryEl?.selectedOptions[0]?.text;

    const exemptionValue =
        exemptionEl?.value || "";

    if (!partyTypeId) {
        alert("Please select Party Type");
        return;
    }

    if (!partyCategoryId) {
        alert("Please select Party Category");
        return;
    }

    if (partyCategoryId === "49" && !exemptionValue) {
        alert("Please select Exemption from Appearance under Article 88");
        return;
    }

    try {

        const result =
            await addParty({

                party_type_id: partyTypeId,
                party_type_name: partyTypeName,
                party_category_id: partyCategoryId,
                party_category_name: partyCategoryName,
                exemption_under_88: exemptionValue

            });

        if (result.success) {

            const skipEkyc =
                (partyCategoryId === "49" && exemptionValue === "Y") ||
                partyCategoryId === "50";

            if (skipEkyc) {

                // Directly show fields, bypass eKYC redirect
                document
                    .getElementById("addPartyBtn")
                    ?.classList.add("hidden");

                document
                    .getElementById("partyFormWrapper")
                    ?.classList.remove("hidden");

                partyTypeEl && ((partyTypeEl as any).tomselect)?.disable();
                partyCategoryEl && ((partyCategoryEl as any).tomselect)?.disable();

                const fieldsResponse = await getPartyFields();

                initializePanVisibility();
                initializeRelationshipStatus();
                initializeGender();
                window.location.href = '/party';
                console.log("Party Fields Loaded (exemption bypass)", fieldsResponse);

            } else {

                window.location.href = result.redirect_url;

            }

        }

    } catch (error) {

        console.error(error);

    }

}