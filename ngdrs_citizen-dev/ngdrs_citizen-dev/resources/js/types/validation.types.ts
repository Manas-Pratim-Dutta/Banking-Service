export interface ValidationResult {
    valid: boolean;
    failedRule?: string;
    message? : string;
}

export type ValidatorFn = (value: string) => boolean;