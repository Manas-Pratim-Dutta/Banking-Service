/**
 * Get element by ID with proper typing
 */
// export function byId<T extends HTMLInputElement >(id: string): T | null {
export function byId<T extends HTMLElement>(id: string): T | null {
    return document.getElementById(id) as T | null;
}

/**
 * Get first matching element
 */
export function qs<T extends HTMLElement>(selector: string): T | null {
    return document.querySelector(selector) as T | null;
}

/**
 * Get all matching elements
 */
export function qsa<T extends HTMLElement>(selector: string): NodeListOf<T> {
    return document.querySelectorAll(selector) as NodeListOf<T>;
}
