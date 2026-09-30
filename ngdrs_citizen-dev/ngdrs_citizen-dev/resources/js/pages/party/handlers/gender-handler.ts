declare global {
    interface Window {
        partyGender: string;
    }
}

export function initializeGender() {
    console.log(
        "window.partyGender",
        (window as any).partyGender
    );

    const gender =
        document.getElementById(
            "gender_id"
        ) as HTMLSelectElement;

    if (!gender) {
        return;
    }

    const tomselect =
        (gender as any).tomselect;

    if (!tomselect) {
        return;
    }

    const ekycGender =
        (
            window.partyGender || ""
        )
            .toString()
            .trim()
            .toUpperCase();

    let genderValue = "";

    switch (ekycGender) {

        case "1":
        case "M":
        case "MALE":
            genderValue = "1";
            break;

        case "2":
        case "F":
        case "FEMALE":
            genderValue = "2";
            break;

        case "3":
        case "O":
        case "OTHER":
        case "OTHERS":
        case "TRANSGENDER":
            genderValue = "3";
            break;
    }

    if (!genderValue) {
        return;
    }
    console.log("ekycGender =", ekycGender);
    console.log("genderValue =", genderValue);
    console.log("tomselect =", tomselect);
    tomselect.setValue(
        genderValue
    );

    tomselect.disable();
}