import TomSelect from "tom-select";

type TomSelectElement = HTMLSelectElement & {
    tomselect?: TomSelect;
};

export function hideEl(id: string): void {
    const el = document.getElementById(id);

    if (el) {
        el.classList.add("hidden");
    }
}

export function setReadonly(id: string): void {
    const el = document.getElementById(id) as TomSelectElement | null;

    if (!el) return;

    if (el.tomselect) {
        el.tomselect.lock();
        el.tomselect.wrapper.classList.add("readonly-field");
    } else {
        el.classList.add("readonly-field");
    }
}

export function removeReadonly(id: string): void {
    const el = document.getElementById(id);

    if (!el) return;

    el.classList.remove("readonly-field");
}
