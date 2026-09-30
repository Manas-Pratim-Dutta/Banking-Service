import { clickHandlers } from "./click-map";

export function handleClick(event: Event) {
    const target = event.target as HTMLElement;

    const el = target.closest("[data-action], [id]");
    if (!el) return;

    const action =
        el.getAttribute("data-action") ||
        el.getAttribute("id");

    if (!action) return;

    const handler = clickHandlers[action];
    if (handler) handler();
}