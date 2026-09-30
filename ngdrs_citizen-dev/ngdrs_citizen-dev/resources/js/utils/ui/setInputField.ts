export function setDateValue(inputId: string, value: string) {
    const input = document.getElementById(inputId) as HTMLInputElement | null;

    if (!input) return;

    input.value = value;
}