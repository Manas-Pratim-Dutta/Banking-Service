import { byId } from "../../../utils/ui/dom";
import { notyf } from "../../../utils/ui/notyf";
import {
    fetchDocumentTitle,
    fetchTalukas,
    fetchOffices,
} from "../../../services/generalinfo/generalinfo.service";

export async function handleArticleChange(el: HTMLElement) {
    const articleEl = el as HTMLSelectElement;
    const articleId = articleEl.value;
    const docTitleInput = byId<HTMLInputElement>("document-title");
    const docTitleIdInput = document.getElementById(
        "document-title-id",
    ) as HTMLInputElement;
    const wrapper = byId<HTMLDivElement>("doc-title-wrapper");
    const leaseTypeWrapper = byId<HTMLDivElement>("lease-type-wrapper");
    // Show Lease Type only for Article 89
    if (articleId === "89") {
        leaseTypeWrapper?.classList.remove("hidden");
    } else {
        leaseTypeWrapper?.classList.add("hidden");
    }

    if (!articleId) {
        wrapper?.classList.add("hidden");
        if (docTitleInput) docTitleInput.value = "";
        if (docTitleIdInput) docTitleIdInput.value = "";
        return;
    }

    const result = await fetchDocumentTitle(articleId);

    if (result.success && result.title) {
        if (docTitleInput) docTitleInput.value = result.title;
        // if (docTitleIdInput) docTitleIdInput.value = result.titleId; // saves the ID
        if (docTitleIdInput) {
            docTitleIdInput.value = String(result.titleId); // hidden ID
            console.log(
                "Hidden input value after assignment:",
                docTitleIdInput.value,
            );
        }
        wrapper?.classList.remove("hidden");
    } else {
        wrapper?.classList.add("hidden");
        if (docTitleInput) docTitleInput.value = "";
        if (docTitleIdInput) docTitleIdInput.value = "";
        if (!result.success) {
            notyf.error(result.message || "Failed to load document title");
        }
    }
}

export async function handleDistrictChange(el: HTMLElement) {
    const districtEl = el as HTMLSelectElement;
    const districtId = districtEl.value;
    const circleEl = byId<HTMLSelectElement>("taluka_id");
    const officeEl = byId<HTMLSelectElement>("office_id");

    if (!circleEl) return;

    const tomCircle = (circleEl as any).tomselect;
    tomCircle.clear();
    tomCircle.clearOptions();

    if (officeEl) {
        const tomOffice = (officeEl as any).tomselect;
        tomOffice.clear();
        tomOffice.clearOptions();
    }

    if (!districtId) {
        circleEl.innerHTML = `<option value="">Select Taluka</option>`;
        return;
    }

    const result = await fetchTalukas(districtId);
    // console.log("Talukas",result);

    if (result.success) {
        result.talukas.forEach((item: any) => {
            tomCircle.addOption({
                value: item.taluka_id,
                text: item.name,
            });
        });
        tomCircle.refreshOptions(false);
        const nocElement = document.getElementById("noc-config");

        if (nocElement && nocElement.getAttribute("data-enabled") === "1") {
            const circleId = nocElement.getAttribute("data-circle-id");

            if (circleId) {
                tomCircle.setValue(circleId);

                circleEl.dispatchEvent(new Event("change"));
            }
            console.log("Auto selecting circle", circleId);
        }
    } else {
        notyf.error(result.message || "Failed to load talukas");
    }
}

export async function handleCircleChange(el: HTMLElement) {
    const circleEl = el as HTMLSelectElement;
    const talukaId = circleEl.value;
    const officeEl = byId<HTMLSelectElement>("office_id");

    if (!officeEl) return;

    const tomOffice = (officeEl as any).tomselect;
    tomOffice.clear();
    tomOffice.clearOptions();

    if (!talukaId) return;

    const result = await fetchOffices(talukaId);
    // console.log("Offices",result);

    if (result.success) {
        result.offices.forEach((item: any) => {
            tomOffice.addOption({
                value: item.office_id,
                text: item.name,
            });
        });
        tomOffice.refreshOptions(false);
    } else {
        notyf.error(result.message || "Failed to load offices");
    }
}

export function handleExecutionTypeChange(el: HTMLElement) {
    const executionTypeEl = el as HTMLSelectElement;

    const entryDateWrapper = byId<HTMLDivElement>("entry-date-wrapper");
    const courtOrderWrapper = byId<HTMLDivElement>("court-order-date-wrapper");

    // Hide all first
    entryDateWrapper?.classList.add("hidden");
    courtOrderWrapper?.classList.add("hidden");

    // Show based on selected value
    if (executionTypeEl.value === "2") {
        entryDateWrapper?.classList.remove("hidden");
    }

    if (executionTypeEl.value === "3") {
        courtOrderWrapper?.classList.remove("hidden");
    }
}

export function handleCourtCaseTypeChange(el: HTMLElement) {
    const executionTypeEl = el as HTMLSelectElement;

    const courtDateWrapper = byId<HTMLDivElement>("court-date-wrapper");
    const courtOrderNumberWrapper = byId<HTMLDivElement>(
        "court-order-number-wrapper",
    );

    // Hide all first
    courtDateWrapper?.classList.add("hidden");
    courtOrderNumberWrapper?.classList.add("hidden");

    // Show based on selected value
    if (executionTypeEl.value === "2") {
        courtDateWrapper?.classList.remove("hidden");
    }

    if (executionTypeEl.value === "2") {
        courtOrderNumberWrapper?.classList.remove("hidden");
    }
}

export function handleDeedWriterChange(el: HTMLElement) {
    const executionTypeEl = el as HTMLSelectElement;

    const deedWriterWrapper = byId<HTMLDivElement>("document_writer_name");
    const numberOfPages = byId<HTMLDivElement>("number-of-pages");
    const advocatename = byId<HTMLDivElement>("advocate-name");

    // Hide all first
    deedWriterWrapper?.classList.add("hidden");
    numberOfPages?.classList.add("hidden");
    advocatename?.classList.add("hidden");

    // Show based on selected value
    if (executionTypeEl.value === "Deed Writer") {
        deedWriterWrapper?.classList.remove("hidden");
        numberOfPages?.classList.remove("hidden");
    }

    if (executionTypeEl.value === "advocate name") {
        advocatename?.classList.remove("hidden");
    }
}

export function handleHomeVisitChange(el: HTMLElement) {
    const homeVisitEl = el as HTMLSelectElement;
    const addressWrapper = byId<HTMLDivElement>(
        "home-visit-address-wrapper"
    );

    if (!addressWrapper) return;

    if (homeVisitEl.value === "Y") {
        addressWrapper.classList.remove("hidden");
    } else {
        addressWrapper.classList.add("hidden");
    }
}
