import { handleChange } from "./change";

export function initEvents() {
    document.addEventListener("change", handleChange);
}
