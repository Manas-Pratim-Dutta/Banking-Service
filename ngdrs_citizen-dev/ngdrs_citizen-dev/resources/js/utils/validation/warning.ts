/**
 * Registry of validation error messages.
 *
 * Responsibility:
 * 1. Maps validation rule names to their corresponding user-facing messages.
 */
export const validationMessages: Record<string, string> = {

    alphabet: "Only letters are allowed",

    numeric: "Only numbers are allowed",

    alphanumeric: "Only letters and numbers are allowed",

    alphanumeric_slash: "Only letters, numbers and '/' are allowed"

};