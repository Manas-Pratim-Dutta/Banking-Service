import { initNocWorkflow } from "./page-load";
import { handleRemoveDag } from "../handlers/main";
export function initProperty() {
    initNocWorkflow();

    const dagContents = document.getElementById("dagContents");
    dagContents?.addEventListener("click", handleRemoveDag);
}
