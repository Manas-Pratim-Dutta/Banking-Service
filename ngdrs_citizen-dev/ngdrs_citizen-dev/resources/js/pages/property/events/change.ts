import { changeHandlers } from "./change-map";

export async function handleChange(event: Event) {
    const target = event.target as HTMLElement;

    if (!target.id) return;

    const handler = changeHandlers[target.id];
    if (handler) {
        await handler(target);
    }
}