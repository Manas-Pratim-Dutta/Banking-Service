import { byId } from "../../../utils/ui/dom";
import {
    getNocDetails,
    fetchPropertyFields,
    fetchSubdivisions,
    fetchCircle,
    fetchMouja,
    fetchVillage,
    fetchAreaType,
    verifyDagApi,
    getSavedProperty,
    fetchDagDetails,
} from "../../../services/property/property.service";
import {
    getAreaType,
    getPropertyType,
    resetPropertyPage,
    getDistrictId,
    getSubDivId,
    getCircleId,
    getMoujaId,
    showDagPattaContainer,
    getPropertyId,
    getAreaTypeId,
    getVillageId,
    getDagNo,
    validateRequiredFields,
    setPattaNo,
    applyPropertyConfig,
    renderPropertySections,
    hideElements,
    setLocationReadOnly,
    renderPropertyCard,
    validateLocationData,
    collectPropertyData,
} from "../../../services/property/action";
import { notyf } from "../../../utils/ui/notyf";
import { validateField } from "../../../utils/validation/validate-field";
import { initTomSelect } from "../../../global";
import {
    setBoundaries,
    setLocation,
    showLocationContainer,
} from "../../../services/property/action";
import { saveManualProperty, saveNocProperty } from "../../../services/property/submit";
import { setTomSelectMapOptions } from "../../../utils/ui/setTomSelect";
import {
    setPropFetchedFromNoc,
} from "../state/page-data";

export async function handlePropertyTypeChange(el: HTMLElement) {
    const propTypeEl = el as HTMLSelectElement;

    const areaTypeEl = document.getElementById(
        "area-type-id",
    ) as HTMLSelectElement | null;

    const areaVal = areaTypeEl?.value;
    const propVal = propTypeEl.value;

    if (!areaVal || !propVal) {
        notyf.error("Please select Area and Property Type");
        return;
    }

    try {
        const result = await fetchPropertyFields(areaVal, propVal);

        console.log(result);

        resetPropertyPage();

        if (!result.success) {
            notyf.error(result.message);
            return;
        }

        applyPropertyConfig(result.config);

        renderPropertySections(result.sections);
    } catch (error: any) {
        notyf.error(
            error?.response?.data?.message ||
                error?.message ||
                "Something went wrong",
        );
    }
}
export async function handleFetchNoc() {
    const nocNoInput = document.getElementById(
        "noc-no",
    ) as HTMLInputElement | null;
    const propertyTypeId = (
        document.getElementById("property-type-id") as HTMLSelectElement | null
    )?.value;
    setPropFetchedFromNoc(true);

    if (!nocNoInput || !propertyTypeId) {
        notyf.error("Please select property type and enter NOC number.");
        return;
    }

    if (!validateField(nocNoInput)) return;

    const nocNo = nocNoInput.value.trim();

    const result = await getNocDetails(nocNo, propertyTypeId);

    if (!result.success) {
        notyf.error(result.message);
        window.triggerChatbotError(result.message);
        return;
    }

    const data = result.noc.data;
    showLocationContainer();
    setLocation(data);
    hideElements();
    setLocationReadOnly();
    // Inject tabs and content
    const dagTabs = document.getElementById("dagTabs")!;
    const dagContents = document.getElementById("dagContents")!;
    dagTabs.innerHTML = result.tabs;
    dagContents.innerHTML = result.content;

    initTomSelect(dagContents);
    initDagTabs();
}

export function initDagTabs() {
    const tabs = document.querySelectorAll<HTMLButtonElement>(".dag-tab");
    const panels = document.querySelectorAll<HTMLElement>(".dag-content");
    const underlineBar = document.getElementById("dagUnderlineBar");
    const tabsContainer = document.getElementById("dagTabs");
    const dagSection = document.getElementById("dagSection");
    dagSection.classList.remove("hidden");

    function activateTab(tab: HTMLButtonElement) {
        const target = tab.dataset.target;

        tabs.forEach((t) => t.classList.remove("active"));
        tab.classList.add("active");

        panels.forEach((p) => p.classList.add("hidden"));
        document.getElementById(target!)?.classList.remove("hidden");

        if (underlineBar && tabsContainer) {
            const tabRect = tab.getBoundingClientRect();
            const parentRect = tabsContainer.getBoundingClientRect();
            underlineBar.style.left = `${tabRect.left - parentRect.left}px`;
            underlineBar.style.width = `${tabRect.width}px`;
        }
    }

    tabs.forEach((tab) => {
        tab.addEventListener("click", () => activateTab(tab));
    });

    if (tabs.length > 0) {
        activateTab(tabs[0]);
    }
}

export async function handleDistrictChange(el: HTMLElement) {
    const district = el as HTMLSelectElement;
    const districtId = district.value;

    const result = await fetchSubdivisions(districtId);

    if (!result.success) {
        notyf.error(result.message);
        return;
    }
    setTomSelectMapOptions("subdivision-input", result.subdivisions!);
}

export function handleFetchPropertyValuation() {
    console.log("Aahhh daddy.");
}

export function handleSaveProperty() {
    const areaType = getAreaType();
    const propertyType = getPropertyType();

    if (!areaType) {
        notyf.error("Please select Area");
        return;
    }

    if (!propertyType) {
        notyf.error("Please select Property Type");
        return;
    }

    switch (`${areaType}-${propertyType}`) {    
        case "6-2":
            saveNocProperty(areaType, propertyType);
            break;

        case "6-1":
            saveManualProperty(areaType, propertyType);
            break;
        case "6-7":
            saveManualProperty(areaType, propertyType);
            break;

        default:
            notyf.error("Unsupported property classification.");
            break;
    }
}

export async function handleSubdivChange(el: HTMLSelectElement) {
    const districtId = getDistrictId();
    const subDivId = el.value;
    if (!districtId || !subDivId) {
        notyf.error("Please select District and Subdivision");
        return;
    }
    const result = await fetchCircle(districtId, subDivId);
    if (!result.success) {
        notyf.error(result.message);
        return;
    }
    setTomSelectMapOptions("circle-input", result.circle!);
}

export async function handleCircleChange(el: HTMLSelectElement) {
    const districtId = getDistrictId();
    const subDivId = getSubDivId();
    const circleId = el.value;
    if (!districtId || !subDivId || !circleId) {
        notyf.error("Please select District and Subdivision and Circle");
        return;
    }
    const result = await fetchMouja(districtId, subDivId, circleId);
    if (!result.success) {
        notyf.error(result.message);
        return;
    }
    setTomSelectMapOptions("mouja-input", result.mouja!);
}

export async function handleMoujaChange(el: HTMLSelectElement) {
    const districtId = getDistrictId();
    const subDivId = getSubDivId();
    const circleId = getCircleId();
    const moujaId = el.value;
    if (!districtId || !subDivId || !circleId || !moujaId) {
        notyf.error(
            "Please select District and Subdivision and Circle and Mouja",
        );
        return;
    }
    const result = await fetchVillage(districtId, subDivId, circleId, moujaId);
    if (!result.success) {
        notyf.error(result.message);
        return;
    }
    setTomSelectMapOptions("village-input", result.village!);
}

export async function handleVillageChange(el: HTMLSelectElement) {
    const districtId = getDistrictId();
    const subDivId = getSubDivId();
    const circleId = getCircleId();
    const moujaId = getMoujaId();
    const villageId = el.value;
    if (!districtId || !subDivId || !circleId || !moujaId || !villageId) {
        notyf.error(
            "Please select District and Subdivision and Circle and Mouja and Village",
        );
        return;
    }
    const result = await fetchAreaType(
        districtId,
        subDivId,
        circleId,
        moujaId,
        villageId,
    );
    if (!result.success) {
        notyf.error(result.message);
        return;
    }
    setTomSelectMapOptions("area-type-input", result.areaType!, true);
}

export async function handleVerifyDag() {
    const payload = {
        propertyId: getPropertyId(),
        areaTypeId: getAreaTypeId(),
        districtId: getDistrictId(),
        subDivId: getSubDivId(),
        circleId: getCircleId(),
        moujaId: getMoujaId(),
        villageId: getVillageId(),
        dagNo: getDagNo(),
    };

    const fields = [
        { value: payload.propertyId, label: "Property Type" },
        { value: payload.areaTypeId, label: "Area Type" },
        { value: payload.districtId, label: "District" },
        { value: payload.subDivId, label: "Subdivision" },
        { value: payload.circleId, label: "Circle" },
        { value: payload.moujaId, label: "Mouja" },
        { value: payload.villageId, label: "Village" },
        { value: payload.dagNo, label: "Dag No" },
    ];

    if (!validateRequiredFields(fields)) return;

    const result = await verifyDagApi(payload);

    if (!result.success) {
        notyf.error(result.message);
        return;
    }

    if (result.pattaNo) {
        setPattaNo(result.pattaNo);
    }
}

export async function showSavedProperty(): Promise<void> {
    const response = await getSavedProperty();

    if (!response.success || !response.data?.length) {
        return;
    }

    const container = document.getElementById("property-detail-container");

    if (!container) {
        return;
    }

    container.innerHTML = response.data
        .map((property) => renderPropertyCard(property))
        .join("");

    container.classList.remove("hidden");
}

export async function handleAddProperty(): Promise<void> {
    const validated = validateLocationData();
    setPropFetchedFromNoc(false);

    if (!validated) {
        return;
    }

    const propertyData = collectPropertyData();

    const response = await fetchDagDetails(propertyData);

    // console.log(response);
    if (!response.success) {
        notyf.error(response.message);
        return;
    }

    const dagTabs = document.getElementById("dagTabs")!;
    const dagContents = document.getElementById("dagContents")!;
    const dagSection = document.getElementById("dagSection")!;
    dagTabs.insertAdjacentHTML("beforeend", response.tab);
    dagContents.insertAdjacentHTML("beforeend", response.content);
    initTomSelect(dagContents);
    initDagTabs();
    dagSection.classList.remove("hidden");
}

export function handleRemoveDag(event: Event): void {
    const button = (event.target as HTMLElement).closest<HTMLButtonElement>(
        ".dag-remove-btn",
    );

    if (!button) {
        return;
    }

    const dagId = button.dataset.dagId;

    if (!dagId) {
        return;
    }

    const dagContent = document.getElementById(`dag-${dagId}`);
    dagContent?.remove();

    const dagTab = document.querySelector<HTMLButtonElement>(
        `.dag-tab[data-target="dag-${dagId}"]`,
    );
    dagTab?.remove();

    const remainingTab =
        document.querySelector<HTMLButtonElement>("#dagTabs .dag-tab");

    if (remainingTab) {
        remainingTab.click();
    } else {
        document.getElementById("dagSection")?.classList.add("hidden");
    }
}
