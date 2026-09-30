import { ValidationResult } from "../../types/validation.types";
import { validationRules } from "./rules";
import { validationMessages } from "./warning";

/**
 * Validates an input field using the rule defined in its `data-rule` attribute.
 *
 * Responsibility:
 * 1. Extracts the validation rule from the input element.
 * 2. Resolves the corresponding validator from the `validationRules` registry.
 * 3. Executes the validator and returns the validation result.
 *
 * @param input - The HTML input element to validate.
 * @returns ValidationResult - Validation outcome including the failed rule and message.
 */
export function validateInput(input: HTMLInputElement): ValidationResult {

    const value = input.value.trim();
    const rule = input.dataset.rule;

    if (!rule) {
        return { valid: true };
    }

    const validator = validationRules[rule];

    if (!validator) {
        console.warn(`Validator "${rule}" not registered`);
        return { valid: true };
    }

    const passed = validator(value);

    if (!passed) {
        return {
            valid: false,
            failedRule: rule,
            message: validationMessages[rule],
        };
    }

    return { valid: true };
}