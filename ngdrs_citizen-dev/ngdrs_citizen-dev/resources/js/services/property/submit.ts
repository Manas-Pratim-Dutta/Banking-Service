import { showSavedProperty } from "../../pages/property/handlers/main";
import { getPropFetchedFromNoc } from "../../pages/property/state/page-data";
import { notyf } from "../../utils/ui/notyf";
import { collectDataA6P2, resetPropertyForm, validateA6P1, validateA6P2 } from "./action";
import { saveProperty, postManualProperty } from "./property.service";

export async function saveNocProperty(areaType: string, propertyType: string) {
    //Area Type : 6 (Other District), Property Type: 2 (Only Land)
    const validated = validateA6P2();
    if (!validated) {
        console.log("validationFailed");
        return;
    }
    const payload = collectDataA6P2();

    const response = await saveProperty(payload);

    if (!response.success) {
        notyf.error(response.message);
    }

    if (response.success) {
        notyf.success("Property Saved Successfully.");
        resetPropertyForm();
        showSavedProperty();
    }
}

export async function saveManualProperty(
    areaType: string,
    propertyType: string,
) {
    //Area Type : 6 (Other District), Property Type: 1 (Only Apartment/Flat)
    const validated = validateA6P1();
    if (!validated) {
        console.log("validationFailed");
        return;
    }
    const nocPropFlag = getPropFetchedFromNoc();
    // console.log(nocPropFlag)
    if (nocPropFlag) {
        saveNocProperty(areaType, propertyType);
        return;
    }

    const payload = collectDataA6P2();
    const response = await postManualProperty(payload);
    // console.log(response);
    // return;

    if (!response.success) {
        notyf.error(response.message);
    }

    if (response.success) {
        notyf.success("Property Saved Successfully.");
        resetPropertyForm();
        showSavedProperty();
    }
}
