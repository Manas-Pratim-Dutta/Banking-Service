export function showCollapsible(element: HTMLElement | null) {
    if (!element) return;
    element.classList.add("collapsible-open");
}

export function hideCollapsible(element: HTMLElement | null) {
    if (!element) return;
    element.classList.remove("collapsible-open");
}
