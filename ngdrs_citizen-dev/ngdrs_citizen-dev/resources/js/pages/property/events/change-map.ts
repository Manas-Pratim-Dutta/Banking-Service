import {
    handlePropertyTypeChange,
    handleDistrictChange,
    handleSubdivChange,
    handleCircleChange,
    handleMoujaChange,
    handleVillageChange,
} from "../handlers/main";

export const changeHandlers: Record<
    string,
    (el: HTMLElement) => void | Promise<void>
> = {
    "property-type-id": handlePropertyTypeChange,
    "district-input": handleDistrictChange,
    "subdivision-input": handleSubdivChange,
    "circle-input": handleCircleChange,
    "mouja-input": handleMoujaChange,
    "village-input": handleVillageChange,
};
