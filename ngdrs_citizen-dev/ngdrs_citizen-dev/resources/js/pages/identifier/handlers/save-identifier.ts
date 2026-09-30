export async function saveIdentifier() {

    const form =
        document.getElementById(
            "identifierForm"
        ) as HTMLFormElement;

    if (!form) {
        return;
    }

    const formData =
        new FormData(form);

    const response =
        await fetch(
            "/save-identifier",
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
            "Identifier saved successfully"
        );

        window.location.reload();

    } else {

        alert(
            result.message ??
            "Failed to save Identifier"
        );
    }
}