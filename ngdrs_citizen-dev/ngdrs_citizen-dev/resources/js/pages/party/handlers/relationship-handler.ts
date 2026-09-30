export function initializeRelationshipStatus() {

    const fatherName =
        (
            document.getElementById(
                "father_full_name_en"
            ) as HTMLInputElement
        )?.value;

    if (
        !fatherName ||
        fatherName.trim() === "" ||
        fatherName.trim().toUpperCase() === "NA"
    ) {
        return;
    }

    const relationship =
        document.getElementById(
            "relationship_status"
        ) as HTMLSelectElement;

    if (!relationship) {
        return;
    }

    const tomselect =
        (relationship as any).tomselect;

    if (tomselect) {

        tomselect.setValue(
            "F"
        );

        tomselect.disable();

    } else {

        relationship.value =
            "F";

        relationship.disabled =
            true;
    }
}