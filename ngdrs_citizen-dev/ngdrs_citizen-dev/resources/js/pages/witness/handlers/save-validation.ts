export function validateVisibleFields(): boolean {

    console.log("Validation started");

    let isValid = true;

    const invalidFields: string[] = [];

    // =========================
    // VALIDATE SELECTS
    // =========================

    const selects =
        document.querySelectorAll(
            '#witnessForm select'
        );

    selects.forEach((field: any) => {

        if (!field) return;

        if (field.disabled) return;

        // Skip hidden TomSelect original select
        if (
            field.classList?.contains(
                'ts-hidden-accessible'
            )
        ) {
            return;
        }

        let value = '';

        if (field.tomselect) {

            value =
                (field.tomselect.getValue() ?? '')
                    .toString()
                    .trim();

        } else {

            value =
                (field.value ?? '')
                    .toString()
                    .trim();
        }

        console.log(
            'SELECT:',
            field.name || field.id,
            '=>',
            value
        );

        if (!value) {

            isValid = false;

            invalidFields.push(
                field.name || field.id
            );

            field.classList.add(
                'border-red-500'
            );

        } else {

            field.classList.remove(
                'border-red-500'
            );
        }
    });

    // =========================
    // VALIDATE INPUTS
    // =========================

    const inputs =
        document.querySelectorAll(
            '#witnessForm input, #witnessForm textarea'
        );

    inputs.forEach((field: any) => {

        if (!field) return;

        if (field.disabled) return;

        if (field.type === 'hidden') return;

        // Skip TomSelect generated inputs
        if (
            field.id?.includes(
                '-ts-control'
            )
        ) {
            return;
        }

        if (
            field.classList?.contains(
                'ts-control'
            )
        ) {
            return;
        }

        if (
            field.offsetParent === null
        ) {
            return;
        }

        const value =
            (field.value ?? '')
                .toString()
                .trim();

        console.log(
            'INPUT:',
            field.name || field.id,
            '=>',
            value
        );

        if (!value) {

            isValid = false;

            invalidFields.push(
                field.name || field.id
            );

            field.classList.add(
                'border-red-500'
            );

        } else {

            field.classList.remove(
                'border-red-500'
            );
        }
    });

    console.log(
        'INVALID FIELDS:',
        invalidFields
    );

    if (!isValid) {

        alert(
            'Please fill all required fields'
        );
    }

    return isValid;
}