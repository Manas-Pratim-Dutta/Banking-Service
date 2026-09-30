export function initializePanVisibility() {
    console.log("PAN HANDLER LOADED");
    const panType =
        document.getElementById(
            "pan_form_list"
        ) as HTMLSelectElement;
    if (!panType) {
        return;
    }

    const panField =
        document.getElementById(
            "panFieldWrapper"
        );

    if (!panType || !panField) {
        return;
    }

    const toggle = () => {

        console.log(
            "PAN SELECT:",
            panType
        );

        console.log(
            "VALUE:",
            panType.value
        );

        console.log(
            "INDEX:",
            panType.selectedIndex
        );

        console.log(
            "TEXT:",
            panType.options[
                panType.selectedIndex
            ]?.text
        );

        const text =
            panType.options[
                panType.selectedIndex
            ]?.text
                ?.toUpperCase() || "";

        if (
            text.includes("PAN")
        ) {

            panField.classList.remove(
                "hidden"
            );

        } else {

            panField.classList.add(
                "hidden"
            );

        }
    };

    toggle();

    panType.addEventListener(
        "change",
        toggle
    );

    const tomselect =
        (panType as any).tomselect;

        

    if (tomselect) {

        tomselect.on(
            "change",
            toggle
        );

    }
}