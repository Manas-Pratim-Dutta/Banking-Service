/**
 * Displays a validation error message for an input field.
 *
 * Responsibility:
 * 1. Adds a visual error state to the input element.
 * 2. Creates an error message element if one does not exist.
 * 3. Updates the error message content.
 *
 * @param input - The HTML input element associated with the error.
 * @param message - The validation message to display.
 */
export function showFieldError(
    input: HTMLInputElement,
    message: string
) {

    input.classList.add("border-red-500");

    let error = input.nextElementSibling as HTMLElement | null;

    if (!error || !error.classList.contains("field-error")) {

        error = document.createElement("p");
        error.className = "field-error text-red-500 text-xs mt-1";

        input.insertAdjacentElement("afterend", error);
    }

    error.textContent = message;
}

/**
 * Clears the validation error state for an input field.
 *
 * Responsibility:
 * 1. Removes the visual error styling from the input element.
 * 2. Removes the associated error message element if present.
 *
 * @param input - The HTML input element whose error state should be cleared.
 */
export function clearFieldError(input: HTMLInputElement) {

    input.classList.remove("border-red-500");

    const error = input.nextElementSibling as HTMLElement | null;

    if (error && error.classList.contains("field-error")) {
        error.remove();
    }
}