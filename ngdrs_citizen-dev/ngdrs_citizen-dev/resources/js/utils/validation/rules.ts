import { ValidatorFn } from "../../types/validation.types";

/**
 * Registry of validation rule functions.
 *
 * Responsibility:
 * 1. Maps rule names to their corresponding validator functions.
 * 2. Each validator receives the input value and returns `true` if valid,
 *    otherwise `false`.
 * 3. Used by `validateInput` to dynamically execute validation rules
 *    defined in the input's `data-rules` attribute.
 */
export const validationRules: Record<string, ValidatorFn> = {
    alphabet: (value: string) => {
        return /^[A-Za-z]+$/.test(value);
    },

    numeric: (value: string) => {
        return /^[0-9]+$/.test(value);
    },

    alphanumeric: (value: string) => {
        return /^[A-Za-z0-9]+$/.test(value);
    },

    alphanumeric_slash: (value: string) => {
        return /^[A-Za-z0-9\/]+$/.test(value);
    },
};
