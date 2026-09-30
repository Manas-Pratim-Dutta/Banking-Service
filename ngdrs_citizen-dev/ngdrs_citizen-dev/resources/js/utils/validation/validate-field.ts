import { validateInput } from "./validator";
import { showFieldError, clearFieldError } from "../ui/validation-ui";

/**
 * Validates a single input field and updates the UI accordingly.
 *
 * Responsibility:
 * 1. Delegates the actual validation logic to `validateInput`.
 * 2. Displays validation errors in the UI when the input is invalid.
 * 3. Clears existing validation errors when the input becomes valid.
 *
 * @param input - The HTML input element to validate.
 * @returns boolean - Returns `true` if the field is valid, otherwise `false`.
 */
export function validateField(input: HTMLInputElement): boolean {
    const result = validateInput(input);

    if (!result.valid) {
        showFieldError(input, result.message || "Invalid value");
        return false;
    }

    clearFieldError(input);

    return true;
}
