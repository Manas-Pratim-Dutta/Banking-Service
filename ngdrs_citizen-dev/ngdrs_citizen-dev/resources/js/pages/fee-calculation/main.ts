import { initEvents } from "./events/index";
import { initFeeRuleChecklist } from "./handlers/main";

document.addEventListener("DOMContentLoaded", () => {
    initEvents();
    initFeeRuleChecklist();
});
