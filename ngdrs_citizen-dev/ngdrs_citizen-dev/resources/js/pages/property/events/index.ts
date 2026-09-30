import { handleChange } from "./change";
import { handleClick } from "./click";

export function initEventListeners() {
    document.addEventListener("change", handleChange);
    document.addEventListener("click", handleClick);
}