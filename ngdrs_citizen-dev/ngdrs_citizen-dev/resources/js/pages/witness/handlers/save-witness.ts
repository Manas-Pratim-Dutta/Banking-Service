export async function saveWitness() {

    const form =
        document.getElementById(
            "witnessForm"
        ) as HTMLFormElement;

    if (!form) {
        return;
    }

    const formData =
        new FormData(form);

    const response =
        await fetch(
            "/save-witness",
            {
                method: "POST",
                body: formData,
                headers: {
                    "X-CSRF-TOKEN":
                        (
                            document.querySelector(
                                'meta[name="csrf-token"]'
                            ) as HTMLMetaElement
                        ).content
                }
            }
        );

    const result =
        await response.json();

    if (
        result.status ===
        "SUCCESS"
    ) {

        alert(
            "Witness saved successfully"
        );

        window.location.reload();

    } else {

        alert(
            result.message ??
            "Failed to save witness"
        );
    }
}