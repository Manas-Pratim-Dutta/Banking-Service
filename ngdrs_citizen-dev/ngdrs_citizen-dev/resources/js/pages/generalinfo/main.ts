import { initEvents } from "./events/index";

document.addEventListener("DOMContentLoaded", () => {
    initEvents();
    const nocElement = document.getElementById("noc-config");

    if (!nocElement) {
        return;
    }

    const isNocFlow =
        nocElement.getAttribute("data-enabled") === "1";

    if (!isNocFlow) {
        return;
    }

    const districtId =
        nocElement.getAttribute("data-district-id");

    if (!districtId) {
        return;
    }

    const districtEl =
        document.getElementById("district_id") as HTMLSelectElement;

    if (!districtEl) {
        return;
    }

    const tomDistrict = (districtEl as any).tomselect;

    tomDistrict.setValue(districtId);

    districtEl.dispatchEvent(
        new Event("change")
    );
    console.log({
        isNocFlow,
        districtId
    });
});
