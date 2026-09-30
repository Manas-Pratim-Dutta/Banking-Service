import "./events";

import {
    initializeIdentifierEkyc
} from "./handlers/ekyc-handler";

import {
    validateVisibleFields
} from "./handlers/save-validation";

import {
    saveIdentifier
} from "./handlers/save-identifier";

document.addEventListener(
    "DOMContentLoaded",
    () => {

        initializeIdentifierEkyc();

        const form =
            document.getElementById(
                "identifierForm"
            ) as HTMLFormElement;

        if (!form) {
            return;
        }

        form.addEventListener(
            "submit",
            async (e) => {

                e.preventDefault();

                if (
                    !validateVisibleFields()
                ) {
                    return;
                }

                await saveIdentifier();
            }
        );
    }
);
document.addEventListener('click', async (event) => {
    const button = (event.target as HTMLElement).closest(
        '.delete-identifier-btn'
    ) as HTMLElement | null;

    if (!button) return;

    const id = button.dataset.id;

    if (!id) return;

    if (!confirm('Are you sure you want to delete this identifier?')) {
        return;
    }

    try {
        const response = await fetch(`/identifier/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN':
                    document.querySelector<HTMLMetaElement>(
                        'meta[name="csrf-token"]'
                    )?.content || '',
                'Accept': 'application/json',
            },
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(
                data.message || 'Failed to delete identifier.'
            );
        }

        // Remove identifier without reloading
        document
            .getElementById(`identifier-${id}`)
            ?.remove();

        // Identifier limit is 1
        const identifierCount = document.querySelectorAll(
            '.delete-identifier-btn'
        ).length;

        const addIdentifierBtn =
            document.getElementById('addIdentifierBtn');

        if (addIdentifierBtn && identifierCount < 1) {
            addIdentifierBtn.classList.remove('hidden');
        }

    } catch (error) {
        console.error(error);

        alert(
            error instanceof Error
                ? error.message
                : 'Failed to delete identifier.'
        );
    }
});