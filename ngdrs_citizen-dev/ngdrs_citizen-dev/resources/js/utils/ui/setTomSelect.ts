export function setTomSelectValue(
    elementId: string,
    value: number,
    text: string,
): void {
    const select = document.getElementById(elementId) as HTMLSelectElement & {
        tomselect?: any;
    };

    if (!select) return;

    const tomSelect = select.tomselect;

    if (!tomSelect) return;

    tomSelect.clearOptions();

    tomSelect.addOption({
        value,
        text,
    });

    tomSelect.setValue(value, true);

    tomSelect.refreshOptions(false);
}

/**
 * Populates a TomSelect instance using an array of strings.
 *
 * Each string is used as both the option value and display text.
 * Existing options and selections are cleared before adding the new ones.
 * If the array is not empty, the first option is automatically selected.
 *
 * @param elementId - ID of the select element initialized with TomSelect.
 * @param options - Array of option labels/values.
 */
export function setTomSelectOptions(
    elementId: string,
    options: string[],
): void {
    const select = document.getElementById(elementId) as HTMLSelectElement & {
        tomselect?: any;
    };
    if (!select) return;
    const tomSelect = select.tomselect;
    if (!tomSelect) return;

    // Fix 1: Properly clear all options including the underlying <select> element
    tomSelect.clear(true); // clear selected value, silent = true
    tomSelect.clearOptions(); // clear TomSelect's internal options

    // Also clear the underlying native <select> to prevent stale options
    select.innerHTML = "";

    // Add new options
    options.forEach((option) => {
        tomSelect.addOption({
            value: option,
            text: option,
        });
    });

    // Refresh internal option cache
    tomSelect.sync();

    // Select first option
    if (options.length > 0) {
        tomSelect.setValue(options[0], true);
    }

    tomSelect.refreshOptions(false);
}

/**
 * Populates a TomSelect instance using key-value pairs.
 *
 * The object key is used as the option value, and the object value
 * is used as the display text. Existing options and selections are
 * cleared before adding the new ones.
 *
 * @param elementId - ID of the select element initialized with TomSelect.
 * @param options - Object containing option value/text pairs.
 * @param selectFirst - If true, automatically selects the first option. Defaults to false.
 */
export function setTomSelectMapOptions(
    elementId: string,
    options: Record<string, string>,
    selectFirst: boolean = false,
): void {
    const select = document.getElementById(elementId) as HTMLSelectElement & {
        tomselect?: any;
    };

    if (!select) return;

    const tomSelect = select.tomselect;
    if (!tomSelect) return;

    tomSelect.clear(true);
    tomSelect.clearOptions();
    select.innerHTML = "";

    Object.entries(options).forEach(([value, text]) => {
        tomSelect.addOption({
            value,
            text,
        });
    });

    tomSelect.sync();

    if (selectFirst) {
        const firstValue = Object.keys(options)[0];
        if (firstValue) {
            tomSelect.setValue(firstValue, true);
        }
    }

    tomSelect.refreshOptions(false);
}
