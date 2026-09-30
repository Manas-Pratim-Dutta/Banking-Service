import {
    setTomSelectValue,
    setTomSelectOptions,
} from "../../utils/ui/setTomSelect";
import { byId } from "../../utils/ui/dom";
import { setDateValue } from "../../utils/ui/setInputField";
import { notyf } from "../../utils/ui/notyf";
import { initTomSelect } from "../../global";
import { hideEl, setReadonly } from "../../utils/ui/ui";

export function setBoundaries(data: any) {
    if (data.data.length == 1) {
        const east = document.getElementById(
            "boundary-east",
        ) as HTMLInputElement;
        const west = document.getElementById(
            "boundary-west",
        ) as HTMLInputElement;
        const south = document.getElementById(
            "boundary-south",
        ) as HTMLInputElement;
        const north = document.getElementById(
            "boundary-north",
        ) as HTMLInputElement;

        east.value = data.data[0].eastside;
        west.value = data.data[0].westside;
        south.value = data.data[0].southside;
        north.value = data.data[0].northside;
    }
}

export function setLocation(data: LocationData): void {
    const el = byId("property-details");
    if (!el) return;
    el.classList.remove("hidden");

    setTomSelectValue("district-input", data.district_id, data.district_name);
    setTomSelectValue(
        "subdivision-input",
        data.subdivision_id,
        data.subdivision_name,
    );
    setTomSelectValue("circle-input", data.circle_id, data.circle_name);
    setTomSelectValue("mouja-input", data.moza_id, data.mouza_name);
    setTomSelectValue("village-input", data.village_id, data.village_name);
    setTomSelectValue("area-type-input", data.land_type_id, data.land_type);
    setTomSelectOptions("dag-no-select", data.dag_list);
    setDateValue("noc-date", data.noc_date);
}

export function hideElements() {
    hideDagPatta();
    hideAddPropBtn();
}

export function hideDagPatta(): void {
    hideEl("dag-patta-container");
}

export function hideAddPropBtn(): void {
    hideEl("add-property-btn");
}

export function showLocationContainer() {
    const locationContainer = document.getElementById("property-details");
    if (locationContainer) {
        locationContainer.classList.remove("hidden");
    }
}

export function showDagPattaContainer() {
    const dagPattaCotainer = document.getElementById("dag-patta-container");
    if (dagPattaCotainer) {
        dagPattaCotainer.classList.remove("hidden");
    }
}

export function setPropertyClassification(): void {
    const areaType = document.getElementById(
        "area-type-id",
    ) as TomSelectElement | null;

    const propertyType = document.getElementById(
        "property-type-id",
    ) as TomSelectElement | null;

    if (areaType) {
        console.log("area");

        if (areaType.tomselect) {
            areaType.tomselect.setValue("6");
            areaType.dispatchEvent(new Event("change"));
        } else {
            areaType.value = "6";
            areaType.dispatchEvent(new Event("change"));
        }

        console.log("Area Type:", areaType.value);
    }

    if (propertyType) {
        console.log("propertyType");

        if (propertyType.tomselect) {
            propertyType.tomselect.setValue("2");
            propertyType.dispatchEvent(new Event("change"));
        } else {
            propertyType.value = "2";
            propertyType.dispatchEvent(new Event("change"));
        }

        console.log("Property Type:", propertyType.value);
    }
}

export function setFetchNoc(nocNo: string): void {
    const nocInput = document.getElementById(
        "noc-no",
    ) as HTMLInputElement | null;

    const fetchButton = document.getElementById(
        "fetch-noc-btn",
    ) as HTMLButtonElement | null;

    if (!nocInput || !fetchButton) {
        return;
    }

    nocInput.value = nocNo;

    // Optional: trigger input/change events if validation depends on them
    nocInput.dispatchEvent(new Event("input", { bubbles: true }));
    nocInput.dispatchEvent(new Event("change", { bubbles: true }));

    fetchButton.click();
}

export function resetPropertyPage() {
    console.log("resetPropertyPage");

    // 1. Reset NOC card
    const nocCard = document.getElementById("noc-card");
    const nocContainer = document.getElementById("noc-container");

    if (nocContainer) {
        nocContainer.innerHTML = "";
    }

    if (nocCard) {
        nocCard.classList.add("hidden");
    }

    // 2. Hide property details card
    const propertyDetails = document.getElementById("property-details");
    if (propertyDetails) {
        propertyDetails.classList.add("hidden");
    }

    // 3. Hide dag-patta-container
    const dagPattaContainer = document.getElementById("dag-patta-container");
    if (dagPattaContainer) {
        dagPattaContainer.classList.add("hidden");
    }

    // 4. Reset DAG section
    const dagSection = document.getElementById("dagSection");
    const dagTabs = document.getElementById("dagTabs");
    const dagContents = document.getElementById("dagContents");
    const dagUnderlineBar = document.getElementById("dagUnderlineBar");

    if (dagTabs) {
        dagTabs.innerHTML = "";
    }

    if (dagContents) {
        dagContents.innerHTML = "";
    }

    if (dagUnderlineBar) {
        dagUnderlineBar.style.width = "0";
        dagUnderlineBar.style.left = "0";
    }

    if (dagSection) {
        dagSection.classList.add("hidden");
    }
}

export function getAreaType(): string | null {
    const areaTypeEl = document.getElementById(
        "area-type-id",
    ) as HTMLSelectElement | null;

    return areaTypeEl?.value || null;
}

export function getPropertyType(): string | null {
    const propertyTypeEl = document.getElementById(
        "property-type-id",
    ) as HTMLSelectElement | null;

    return propertyTypeEl?.value || null;
}

export function collectDataA6P2() {
    const getValue = (id: string): string => {
        const element = document.getElementById(id) as
            | HTMLInputElement
            | HTMLSelectElement
            | null;

        return element?.value ?? "";
    };

    const getByName = (name: string, container: Element): string => {
        const element = container.querySelector(`[name="${name}"]`) as
            | HTMLInputElement
            | HTMLSelectElement
            | null;

        return element?.value ?? "";
    };

    // Common property-level data
    const commonData = {
        area_type_id: getValue("area-type-id"),
        property_type_id: getValue("property-type-id"),

        noc_no: getValue("noc-no"),

        district: getValue("district-input"),
        subdivision: getValue("subdivision-input"),
        circle: getValue("circle-input"),
        mouja: getValue("mouja-input"),
        village: getValue("village-input"),

        police_station: getValue("police_station"),
        post_office: getValue("post_office"),
        pin_code: getValue("pin_code"),
        description_of_property: getValue("description_of_property"),
    };

    const properties: any[] = [];

    // Collect every DAG separately
    const dagContents = document.querySelectorAll("#dagContents .dag-content");

    dagContents.forEach((dagElement) => {
        const dagId = dagElement.id.replace("dag-", "");

        const dagData = {
            // Common data
            ...commonData,

            // DAG / Patta
            dag_no: getByName("dag_no[]", dagElement),
            patta_no: getByName("patta_no[]", dagElement),

            // Land Schedule
            area_bigha: getByName(`area-bigha-${dagId}`, dagElement),
            area_kattha: getByName(`area-kattha-${dagId}`, dagElement),
            area_lessa: getByName(`area-lessa-${dagId}`, dagElement),

            // Patta
            patta_type_id: getByName(`patta-type-${dagId}`, dagElement),

            // Valuation
            zonal_rate: getByName("zonal_rate", dagElement),
            land_valuation: getByName("land_valuation", dagElement),
            zone_name: getByName("zone_name", dagElement),
            zone_class: getByName("zone_class", dagElement),
            dharitree_class: getByName("dharitree_class", dagElement),

            // Boundaries
            boundary_east: getByName(`boundary-east-${dagId}`, dagElement),

            boundary_west: getByName(`boundary-west-${dagId}`, dagElement),

            boundary_south: getByName(`boundary-south-${dagId}`, dagElement),

            boundary_north: getByName(`boundary-north-${dagId}`, dagElement),
        };

        properties.push(dagData);
    });

    return {
        properties,
    };
}

export function getDistrictId() {
    const selectDistrict = document.getElementById(
        "district-input",
    ) as HTMLSelectElement;
    if (selectDistrict) {
        const districtId = selectDistrict.value;
        return districtId;
    }
}

export function getSubDivId() {
    const selectSubdivision = document.getElementById(
        "subdivision-input",
    ) as HTMLSelectElement;
    if (selectSubdivision) {
        const subdivId = selectSubdivision.value;
        return subdivId;
    }
}

export function getCircleId() {
    const selectCircle = document.getElementById(
        "circle-input",
    ) as HTMLSelectElement;
    if (selectCircle) {
        const circleId = selectCircle.value;
        return circleId;
    }
}

export function getMoujaId() {
    const selectMouja = document.getElementById(
        "mouja-input",
    ) as HTMLSelectElement;
    if (selectMouja) {
        const moujaId = selectMouja.value;
        return moujaId;
    }
}

export function getVillageId() {
    const selectVillage = document.getElementById(
        "village-input",
    ) as HTMLSelectElement;
    if (selectVillage) {
        const villageId = selectVillage.value;
        return villageId;
    }
}

export function getPropertyId() {
    const selectProepetyType = document.getElementById(
        "property-type-id",
    ) as HTMLSelectElement;
    if (selectProepetyType) {
        const propertyTypeId = selectProepetyType.value;
        return propertyTypeId;
    }
}

export function getAreaTypeId() {
    const selectAreaType = document.getElementById(
        "area-type-id",
    ) as HTMLSelectElement;
    if (selectAreaType) {
        const areaTypeId = selectAreaType.value;
        return areaTypeId;
    }
}

export function getDagNo() {
    const dagNoInput = document.getElementById(
        "dag-no-input",
    ) as HTMLInputElement;
    if (dagNoInput) {
        const dagNo = dagNoInput.value;
        return dagNo;
    }
}

export function validateRequiredFields(
    fields: { value: any; label: string }[],
): boolean {
    const missingField = fields.find(
        ({ value }) => value === null || value === undefined || value === "",
    );

    if (missingField) {
        notyf.error(`Please select/enter ${missingField.label}.`);
        return false;
    }

    return true;
}

export function setPattaNo(pattaNo: string) {
    const pattaInput = document.getElementById(
        "patta-input",
    ) as HTMLInputElement | null;

    if (pattaInput) {
        pattaInput.value = pattaNo;
    }
}

export function applyPropertyConfig(config: PropertyConfig): void {
    if (config.showLocationCard) {
        showLocationContainer();
    }

    if (config.showDagPattaContainer) {
        showDagPattaContainer();
    }
    applyDagPattaInputMode(config.dagPattaInputMode);
}

export function applyDagPattaInputMode(mode: DagPattaInputMode): void {
    const dagInput = document.getElementById(
        "dag-no-input",
    ) as HTMLInputElement | null;

    const pattaInput = document.getElementById(
        "patta-input",
    ) as HTMLInputElement | null;

    const verifyButton = document.getElementById(
        "verifyDagBtn",
    ) as HTMLButtonElement | null;

    if (!dagInput || !pattaInput || !verifyButton) {
        return;
    }

    if (mode === "VERIFY") {
        verifyButton.classList.remove("hidden");
        pattaInput.readOnly = true;
    } else {
        verifyButton.classList.add("hidden");
        pattaInput.readOnly = false;
    }
}

const sectionMap = {
    noc: {
        cardId: "noc-card",
        containerId: "noc-container",
    },
    "dag-patta-other": {
        cardId: "dag-patta-other-card",
        containerId: "dag-patta-other-container",
    },
};

export function renderPropertySections(
    sections: Record<string, string> | null,
): void {
    if (!sections) {
        return;
    }

    Object.entries(sectionMap).forEach(([sectionKey, config]) => {
        const container = byId(config.containerId);
        const card = byId(config.cardId);

        if (!container || !card) return;

        const html = sections[sectionKey];

        container.innerHTML = "";

        if (html) {
            container.innerHTML = html;
            card.classList.remove("hidden");
            initTomSelect(container);
        } else {
            card.classList.add("hidden");
        }
    });
}

export function setLocationReadOnly() {
    setReadonly("district-input");
}

export function validateA6P2(): boolean {
    let isValid = true;

    // Remove previous validation errors
    document.querySelectorAll(".validation-error").forEach((el) => {
        el.remove();
    });

    document.querySelectorAll(".border-red-500").forEach((el) => {
        el.classList.remove("border-red-500");
    });

    const getValue = (id: string): string => {
        const element = document.getElementById(id) as
            | HTMLInputElement
            | HTMLSelectElement
            | null;

        return element?.value?.trim() ?? "";
    };

    const showError = (element: HTMLElement, message: string) => {
        isValid = false;

        element.classList.add("border-red-500");

        const error = document.createElement("p");
        error.className = "validation-error mt-1 text-xs text-red-500";
        error.textContent = message;

        element.parentElement?.appendChild(error);
    };

    /*
     * Common property-level fields
     */
    const requiredFields = [
        {
            id: "area-type-id",
            message: "Please select Area.",
        },
        {
            id: "property-type-id",
            message: "Please select Property Type.",
        },
        {
            id: "district-input",
            message: "Please select District.",
        },
        {
            id: "subdivision-input",
            message: "Please select Subdivision.",
        },
        {
            id: "circle-input",
            message: "Please select Circle.",
        },
        {
            id: "mouja-input",
            message: "Please select Mouja.",
        },
        {
            id: "village-input",
            message: "Please select Village.",
        },
        {
            id: "area-type-input",
            message: "Please select Area Type.",
        },
        {
            id: "police_station",
            message: "Please enter Police Station.",
        },
        {
            id: "post_office",
            message: "Please enter Post Office.",
        },
        {
            id: "pin_code",
            message: "Please enter PIN Code.",
        },
        {
            id: "description_of_property",
            message: "Please enter Description of the Property.",
        },
    ];

    requiredFields.forEach(({ id, message }) => {
        const element = document.getElementById(id) as
            | HTMLInputElement
            | HTMLSelectElement
            | null;

        if (!element) {
            console.warn(`Validation field not found: ${id}`);
            return;
        }

        if (!element.value?.trim()) {
            showError(element, message);
        }
    });

    /*
     * PIN Code
     */
    const pinCode = document.getElementById(
        "pin_code",
    ) as HTMLInputElement | null;

    if (pinCode && pinCode.value.trim() !== "") {
        if (!/^\d{6}$/.test(pinCode.value.trim())) {
            showError(pinCode, "PIN Code must be exactly 6 digits.");
        }
    }

    /*
     * DAG-WISE VALIDATION
     *
     * Every .dag-content represents one DAG.
     *
     * Example:
     * #dag-679
     * #dag-523
     */
    const dagContents = document.querySelectorAll("#dagContents .dag-content");

    if (dagContents.length === 0) {
        console.warn("No DAG records found.");
    }

    dagContents.forEach((dagElement) => {
        const dagId = dagElement.id.replace("dag-", "");

        if (!dagId) {
            return;
        }

        /*
         * DAG Number
         *
         * dag-no-input is the input used to add/verify a DAG.
         * The actual DAG records are represented by the dag-content
         * containers, so don't use offsetParent() here.
         */

        /*
         * Land Area
         */
        const areaBigha = dagElement.querySelector(
            `[name="area-bigha-${dagId}"]`,
        ) as HTMLInputElement | null;

        const areaKattha = dagElement.querySelector(
            `[name="area-kattha-${dagId}"]`,
        ) as HTMLInputElement | null;

        const areaLessa = dagElement.querySelector(
            `[name="area-lessa-${dagId}"]`,
        ) as HTMLInputElement | null;

        if (areaBigha && areaKattha && areaLessa) {
            const bigha = Number(areaBigha.value || 0);
            const kattha = Number(areaKattha.value || 0);
            const lessa = Number(areaLessa.value || 0);

            if (bigha === 0 && kattha === 0 && lessa === 0) {
                isValid = false;

                areaBigha.classList.add("border-red-500");
                areaKattha.classList.add("border-red-500");
                areaLessa.classList.add("border-red-500");

                const error = document.createElement("p");
                error.className = "validation-error mt-1 text-xs text-red-500";
                error.textContent = `Land area for Dag ${dagId} must be greater than zero.`;

                areaLessa.parentElement?.appendChild(error);
            }
        }

        /*
         * Boundary validation
         */
        const boundaries = [
            {
                name: `boundary-east-${dagId}`,
                message: `Please enter East Boundary for Dag ${dagId}.`,
            },
            {
                name: `boundary-west-${dagId}`,
                message: `Please enter West Boundary for Dag ${dagId}.`,
            },
            {
                name: `boundary-south-${dagId}`,
                message: `Please enter South Boundary for Dag ${dagId}.`,
            },
            {
                name: `boundary-north-${dagId}`,
                message: `Please enter North Boundary for Dag ${dagId}.`,
            },
        ];

        boundaries.forEach(({ name, message }) => {
            const element = dagElement.querySelector(
                `[name="${name}"]`,
            ) as HTMLInputElement | null;

            if (!element) {
                console.warn(
                    `DAG ${dagId}: validation field not found: ${name}`,
                );
                return;
            }

            if (!element.value?.trim()) {
                showError(element, message);
            }
        });
    });

    return isValid;
}

export function validateA6P1(): boolean {
    let isValid = true;

    // Remove previous validation errors
    document.querySelectorAll(".validation-error").forEach((el) => {
        el.remove();
    });

    document.querySelectorAll(".border-red-500").forEach((el) => {
        el.classList.remove("border-red-500");
    });

    const getValue = (id: string): string => {
        const element = document.getElementById(id) as
            | HTMLInputElement
            | HTMLSelectElement
            | null;

        return element?.value?.trim() ?? "";
    };

    const showError = (element: HTMLElement, message: string) => {
        isValid = false;

        element.classList.add("border-red-500");

        const error = document.createElement("p");
        error.className = "validation-error mt-1 text-xs text-red-500";
        error.textContent = message;

        element.parentElement?.appendChild(error);
    };

    /*
     * Common property-level fields
     */
    const requiredFields = [
        {
            id: "area-type-id",
            message: "Please select Area.",
        },
        {
            id: "property-type-id",
            message: "Please select Property Type.",
        },
        {
            id: "district-input",
            message: "Please select District.",
        },
        {
            id: "subdivision-input",
            message: "Please select Subdivision.",
        },
        {
            id: "circle-input",
            message: "Please select Circle.",
        },
        {
            id: "mouja-input",
            message: "Please select Mouja.",
        },
        {
            id: "village-input",
            message: "Please select Village.",
        },
        {
            id: "area-type-input",
            message: "Please select Area Type.",
        },
        {
            id: "police_station",
            message: "Please enter Police Station.",
        },
        {
            id: "post_office",
            message: "Please enter Post Office.",
        },
        {
            id: "pin_code",
            message: "Please enter PIN Code.",
        },
        {
            id: "description_of_property",
            message: "Please enter Description of the Property.",
        },
    ];

    requiredFields.forEach(({ id, message }) => {
        const element = document.getElementById(id) as
            | HTMLInputElement
            | HTMLSelectElement
            | null;

        if (!element) {
            console.warn(`Validation field not found: ${id}`);
            return;
        }

        if (!element.value?.trim()) {
            showError(element, message);
        }
    });

    /*
     * PIN Code
     */
    const pinCode = document.getElementById(
        "pin_code",
    ) as HTMLInputElement | null;

    if (pinCode && pinCode.value.trim() !== "") {
        if (!/^\d{6}$/.test(pinCode.value.trim())) {
            showError(pinCode, "PIN Code must be exactly 6 digits.");
        }
    }

    /*
     * DAG-WISE VALIDATION
     *
     * Every .dag-content represents one DAG.
     *
     * Example:
     * #dag-679
     * #dag-523
     */
    const dagContents = document.querySelectorAll("#dagContents .dag-content");

    if (dagContents.length === 0) {
        // console.warn("No DAG records found.");
        notyf.error('Please add a property before proceeding.');
        return;
    }

    dagContents.forEach((dagElement) => {
        const dagId = dagElement.id.replace("dag-", "");

        if (!dagId) {
            return;
        }

        /*
         * DAG Number
         *
         * dag-no-input is the input used to add/verify a DAG.
         * The actual DAG records are represented by the dag-content
         * containers, so don't use offsetParent() here.
         */

        /*
         * Land Area
         */
        const areaBigha = dagElement.querySelector(
            `[name="area-bigha-${dagId}"]`,
        ) as HTMLInputElement | null;

        const areaKattha = dagElement.querySelector(
            `[name="area-kattha-${dagId}"]`,
        ) as HTMLInputElement | null;

        const areaLessa = dagElement.querySelector(
            `[name="area-lessa-${dagId}"]`,
        ) as HTMLInputElement | null;

        if (areaBigha && areaKattha && areaLessa) {
            const bigha = Number(areaBigha.value || 0);
            const kattha = Number(areaKattha.value || 0);
            const lessa = Number(areaLessa.value || 0);

            if (bigha === 0 && kattha === 0 && lessa === 0) {
                isValid = false;

                areaBigha.classList.add("border-red-500");
                areaKattha.classList.add("border-red-500");
                areaLessa.classList.add("border-red-500");

                const error = document.createElement("p");
                error.className = "validation-error mt-1 text-xs text-red-500";
                error.textContent = `Please Enter Land area.`;

                areaBigha.parentElement?.appendChild(error);
            }
        }

        /*
         * Patta Type validation
         */
        const pattaType = dagElement.querySelector(
            `[name="patta-type-${dagId}"]`,
        ) as HTMLSelectElement | null;

        if (pattaType && !pattaType.value?.trim()) {
            showError(
                pattaType,
                `Please select Patta Type for Dag ${dagId}.`,
            );
        }

        /*
         * Boundary validation
         */
        const boundaries = [
            {
                name: `boundary-east-${dagId}`,
                message: `Please enter East Boundary for Dag ${dagId}.`,
            },
            {
                name: `boundary-west-${dagId}`,
                message: `Please enter West Boundary for Dag ${dagId}.`,
            },
            {
                name: `boundary-south-${dagId}`,
                message: `Please enter South Boundary for Dag ${dagId}.`,
            },
            {
                name: `boundary-north-${dagId}`,
                message: `Please enter North Boundary for Dag ${dagId}.`,
            },
        ];

        boundaries.forEach(({ name, message }) => {
            const element = dagElement.querySelector(
                `[name="${name}"]`,
            ) as HTMLInputElement | null;

            if (!element) {
                console.warn(
                    `DAG ${dagId}: validation field not found: ${name}`,
                );
                return;
            }

            if (!element.value?.trim()) {
                showError(element, message);
            }
        });
    });

    return isValid;
}

export function resetPropertyForm(): void {
    // --------------------------------
    // 1. Hide sections
    // --------------------------------
    [
        "noc-card",
        "property-details",
        "dag-patta-container",
        "dag-patta-other-card",
        "dagSection",
        "verifyDagBtn",
    ].forEach((id) => {
        document.getElementById(id)?.classList.add("hidden");
    });

    // --------------------------------
    // 2. Clear dynamically generated content
    // --------------------------------
    ["noc-container", "dag-patta-other-container"].forEach((id) => {
        document.getElementById(id)?.replaceChildren();
    });

    console.log("Property form hidden/reset");
}

export function renderPropertyCard(property: SavedProperty): string {
    return `
        <div class="relative w-full bg-white rounded-2xl border border-orange-100 shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 overflow-hidden mb-4">

            <div class="absolute top-0 left-0 h-full w-1.5 bg-gradient-to-b from-[#ff6900] to-orange-400"></div>

            <div class="flex flex-col md:flex-row">

                <!-- Locality -->
                <div class="md:w-1/4 p-5 pl-6 flex flex-col justify-center gap-2.5 border-b md:border-b-0 md:border-r border-dashed border-orange-100">

                    <div class="flex items-center gap-2 mb-1">
                        <svg class="w-4 h-4 text-[#ff6900]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>

                        <span class="text-xs font-semibold text-[#ff6900] uppercase tracking-wider">
                            Location
                        </span>
                    </div>

                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">District</span>
                        <span class="font-semibold text-gray-800">Kamrup Metro</span>
                    </div>

                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">Circle</span>
                        <span class="font-semibold text-gray-800">Dispur</span>
                    </div>

                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">Mouja</span>
                        <span class="font-semibold text-gray-800">Beltala</span>
                    </div>

                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">Village</span>
                        <span class="font-semibold text-gray-800">Jyotikuchi</span>
                    </div>
                </div>


                <!-- Dag / Patta -->
                <div class="md:w-[15%] flex md:flex-col items-center justify-center gap-4 p-5 border-b md:border-b-0 md:border-r border-dashed border-orange-100 bg-gradient-to-br from-orange-50/60 to-transparent">

                    <div class="text-center px-3 py-2 rounded-xl bg-white shadow-sm border border-orange-100 w-full">
                        <div class="text-[10px] uppercase tracking-wide text-gray-400 font-medium">
                            Dag No.
                        </div>

                        <div class="text-xl font-extrabold text-[#ff6900]">
                            ${property.dag_no}
                        </div>
                    </div>

                    <div class="text-center px-3 py-2 rounded-xl bg-white shadow-sm border border-orange-100 w-full">
                        <div class="text-[10px] uppercase tracking-wide text-gray-400 font-medium">
                            Patta No.
                        </div>

                        <div class="text-xl font-extrabold text-[#ff6900]">
                            ${property.patta_no}
                        </div>
                    </div>

                </div>


                <!-- Area + Patta Type -->
                <div class="md:w-1/4 p-5 flex flex-col justify-center gap-3 border-b md:border-b-0 md:border-r border-dashed border-orange-100">

                    <div>
                        <div class="text-xs text-gray-400 mb-0.5">
                            Total area
                        </div>

                        <div class="font-semibold text-gray-800">
                            ${property.area_bigha} Bigha
                            ${property.area_kattha} Katha
                            ${property.area_lessa} Lessa
                        </div>
                    </div>

                    <div>
                        <div class="text-xs text-gray-400 mb-0.5">
                            Patta type
                        </div>

                        <span class="inline-block px-2.5 py-1 rounded-full bg-orange-50 text-[#ff6900] text-xs font-semibold border border-orange-100">
                            ${getPattaTypeName(property.patta_type_id)}
                        </span>
                    </div>

                </div>


                <!-- Valuation -->
                <div class="md:w-[30%] p-5 flex flex-col justify-center gap-2 bg-gradient-to-br from-[#fff4ec] to-white">

                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">
                            Zonal value rate
                        </span>

                        <span class="font-medium text-gray-700">
                            ₹ ${formatAmount(property.zonal_rate)}
                        </span>
                    </div>

                    <div class="h-px bg-orange-100 my-1"></div>

                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 font-medium text-sm">
                            Property valuation
                        </span>

                        <span class="font-extrabold text-[#ff6900] text-lg">
                            ₹ ${formatAmount(property.land_valuation)}
                        </span>
                    </div>

                </div>

            </div>
        </div>
    `;
}

function formatAmount(value: string): string {
    return Number(value).toLocaleString("en-IN");
}

function getPattaTypeName(pattaTypeId: string): string {
    const pattaTypes: Record<string, string> = {
        "2": "Kheraj Patta (KP)",
        "3": "Nisfy Kheraj Patta (NKP)",
    };

    return pattaTypes[pattaTypeId] ?? "Unknown";
}

export function validateLocationData(): boolean {
    const fields = [
        {
            id: "district-input",
            name: "District",
        },
        {
            id: "subdivision-input",
            name: "Subdivision",
        },
        {
            id: "circle-input",
            name: "Circle",
        },
        {
            id: "mouja-input",
            name: "Mouja",
        },
        {
            id: "village-input",
            name: "Village",
        },
        {
            id: "area-type-input",
            name: "Area Type",
        },
        {
            id: "police_station",
            name: "Police Station",
        },
        {
            id: "post_office",
            name: "Post Office",
        },
        {
            id: "pin_code",
            name: "PIN Code",
        },
        { id: "dag-no-input", name: "Dag No" },
        { id: "patta-input", name: "Patta No" },
    ];

    for (const field of fields) {
        const element = document.getElementById(field.id) as
            | HTMLInputElement
            | HTMLSelectElement
            | null;

        if (!element || !element.value.trim()) {
            notyf.error(`${field.name} is required.`);
            element?.focus();
            return false;
        }
    }

    return true;
}

export const collectPropertyData = () => {
    const getValue = (id: string): string => {
        const element = document.getElementById(id) as
            | HTMLInputElement
            | HTMLSelectElement
            | null;
        return element?.value?.trim() ?? "";
    };

    return {
        district: getValue("district-input"),
        subdivision: getValue("subdivision-input"),
        circle: getValue("circle-input"),
        mouja: getValue("mouja-input"),
        village: getValue("village-input"),
        area_type: getValue("area-type-input"),
        dag_no: getValue("dag-no-input"),
        patta_no: getValue("patta-input")
    };
};