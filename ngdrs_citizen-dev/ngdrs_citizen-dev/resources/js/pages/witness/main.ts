import "./events";

import {
    initializeWitnessEkyc
} from "./handlers/ekyc-handler";

import {
    validateVisibleFields
} from "./handlers/save-validation";

import {
    saveWitness
} from "./handlers/save-witness";

document.addEventListener(
    "DOMContentLoaded",
    () => {

        initializeWitnessEkyc();

        const form =
            document.getElementById(
                "witnessForm"
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

                await saveWitness();
            }
        );
    }
);
document.addEventListener('click', async (event) => {
    const button = (event.target as HTMLElement).closest(
        '.delete-witness-btn'
    ) as HTMLElement | null;

    if (!button) return;

    const id = button.dataset.id;

    if (!id) return;

    if (!confirm('Are you sure you want to delete this witness?')) {
        return;
    }

    try {
        const response = await fetch(`/witness/${id}`, {
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
            throw new Error(data.message || 'Failed to delete witness.');
        }

        // Remove from UI without reloading
        document
            .getElementById(`witness-${id}`)
            ?.remove();

        // Show ADD button again if less than 2 witnesses
        const witnessCount =
            document.querySelectorAll('.delete-witness-btn').length;

        const addWitnessBtn =
            document.getElementById('addWitnessBtn');

        if (addWitnessBtn && witnessCount < 2) {
            addWitnessBtn.classList.remove('hidden');
        }

    } catch (error) {
        console.error(error);

        alert(
            error instanceof Error
                ? error.message
                : 'Failed to delete witness.'
        );
    }
});