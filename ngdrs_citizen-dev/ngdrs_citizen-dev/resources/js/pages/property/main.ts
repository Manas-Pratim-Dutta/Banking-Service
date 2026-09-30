import { initEventListeners } from "./events/index";
import { initProperty } from "./init";


document.addEventListener("DOMContentLoaded", () => {
    let propFetchedFromNoc = "N";
    initEventListeners();
    initProperty();
});
