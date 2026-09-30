import {
    handleFetchNoc,
    handleFetchPropertyValuation,
    handleSaveProperty,
    handleVerifyDag,
    handleAddProperty
} from "../handlers/main";

export const clickHandlers: Record<string, () => void> = {

    "fetch-noc": handleFetchNoc,

    "fetch-property-valuation": handleFetchPropertyValuation,

    "save-property-btn": handleSaveProperty,

    "verifyDagBtn": handleVerifyDag,

    "add-property-btn": handleAddProperty

};