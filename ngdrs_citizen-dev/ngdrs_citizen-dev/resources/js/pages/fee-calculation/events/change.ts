import { 
    handleExServicemenToggle, 
    handleTeaGardenToggle, 
    handleFeeRuleChange 
} from "../handlers/main";

const changeHandlers: Record<string, (el: HTMLElement) => void | Promise<void>> = {
    "toggle-ex-servicemen": handleExServicemenToggle,
    "toggle-tea-garden": handleTeaGardenToggle,
    "fee-rule-select": handleFeeRuleChange
};

export async function handleChange(event: Event) {
    const target = event.target as HTMLElement;

    if (!target.id) return;

    const handler = changeHandlers[target.id];
    if (handler) {
        await handler(target);
    }
}
