import { handleChange } from "./change";
import { handleClick } from "./click";

export function initEvents() {
    document.addEventListener("change", handleChange);
    document.addEventListener("click", handleClick);

    // Prevent changing number input values on scroll when focused
    document.addEventListener("wheel", (event: WheelEvent) => {
        const activeEl = document.activeElement as HTMLInputElement | null;
        if (activeEl && activeEl.tagName === "INPUT" && activeEl.type === "number") {
            event.preventDefault();
        }
    }, { passive: false });
}
