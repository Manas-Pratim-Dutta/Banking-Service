import { validateField } from "./validate-field";

export function validateForm(container: HTMLElement): boolean {

    const inputs = container.querySelectorAll<HTMLInputElement>("[data-rules]");

    for (const input of inputs) {

        if (!validateField(input)) {
            input.focus();
            return false;
        }

    }

    return true;
}