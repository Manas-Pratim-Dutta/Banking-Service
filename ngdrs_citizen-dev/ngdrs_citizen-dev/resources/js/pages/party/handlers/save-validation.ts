// export function validateVisibleFields(): boolean {

//     console.log("Validation started");

//     let isValid = true;

//     const invalidFields: string[] = [];

//     // =========================
//     // CHECK EXEMPTION BYPASS CONDITION
//     // =========================

//     const partyCategoryEl =
//         document.getElementById(
//             "partycategory"
//         ) as HTMLSelectElement;

//     const exemptionEl =
//         document.getElementById(
//             "exemptionFromAppearance"
//         ) as HTMLSelectElement;

//     const partyCategoryValue =
//         (partyCategoryEl as any)?.tomselect
//             ? (partyCategoryEl as any).tomselect.getValue()
//             : partyCategoryEl?.value;

//     const exemptionValue =
//         (exemptionEl as any)?.tomselect
//             ? (exemptionEl as any).tomselect.getValue()
//             : exemptionEl?.value;

//     const isExemptedCategory49 =
//         String(partyCategoryValue) === "49" &&
//         String(exemptionValue) === "Y";

//     // Only these fields are compulsory when exempted
//     const requiredOnlyFields = [
//         'salutation_id',
//         'party_full_name_en'
//     ];

//     // =========================
//     // VALIDATE SELECT (TomSelect SAFE)
//     // =========================

//     const selects =
//         document.querySelectorAll(
//             '#partyForm select'
//         );

//     selects.forEach((field: any) => {

//         if (!field) return;

//         if (field.disabled) return;

//         // Skip hidden TomSelect original select
//         if (
//             field.classList?.contains(
//                 'ts-hidden-accessible'
//             )
//         ) {
//             return;
//         }

//         const fieldName = field.name || field.id;

//         // Skip fields not required under exemption bypass
//         if (
//             isExemptedCategory49 &&
//             !requiredOnlyFields.includes(fieldName)
//         ) {
//             field.classList.remove('border-red-500');
//             return;
//         }

//         let value = '';

//         // Prefer TomSelect value
//         if (field.tomselect) {

//             value =
//                 (field.tomselect.getValue() ?? '')
//                     .toString()
//                     .trim();

//         } else {

//             value =
//                 (field.value ?? '')
//                     .toString()
//                     .trim();
//         }

//         console.log(
//             'SELECT:',
//             fieldName,
//             '=>',
//             value
//         );

//         if (!value) {

//             isValid = false;

//             invalidFields.push(fieldName);

//             field.classList.add(
//                 'border-red-500'
//             );

//         } else {

//             field.classList.remove(
//                 'border-red-500'
//             );
//         }
//     });

//     // =========================
//     // VALIDATE INPUTS + TEXTAREA
//     // =========================

//     const inputs =
//         document.querySelectorAll(
//             '#partyForm input, #partyForm textarea'
//         );

//     inputs.forEach((field: any) => {

//         if (!field) return;

//         if (field.disabled) return;

//         if (field.type === 'hidden') return;

//         // Skip TomSelect generated controls
//         if (field.id?.includes('-ts-control')) return;
//         if (field.classList?.contains('ts-control')) return;

//         if (field.offsetParent === null) return;

//         const fieldName = field.name || field.id;

//         // Skip fields not required under exemption bypass
//         if (
//             isExemptedCategory49 &&
//             !requiredOnlyFields.includes(fieldName)
//         ) {
//             field.classList.remove('border-red-500');
//             return;
//         }

//         const value =
//             (field.value ?? '')
//                 .toString()
//                 .trim();

//         console.log(
//             'INPUT:',
//             fieldName,
//             '=>',
//             value
//         );

//         if (!value) {

//             isValid = false;

//             invalidFields.push(fieldName);

//             field.classList.add(
//                 'border-red-500'
//             );

//         } else {

//             field.classList.remove(
//                 'border-red-500'
//             );
//         }
//     });

//     console.log(
//         'INVALID FIELDS:',
//         invalidFields
//     );

//     if (!isValid) {

//         alert(
//             'Please fill all required fields'
//         );
//     }

//     return isValid;
// }
export function validateVisibleFields(): boolean {

    console.log("Validation started");

    let isValid = true;

    const invalidFields: string[] = [];

    // =========================
    // CHECK EXEMPTION BYPASS CONDITION
    // =========================

    const partyCategoryEl =
        document.getElementById(
            "partycategory"
        ) as HTMLSelectElement;

    const exemptionEl =
        document.getElementById(
            "exemptionFromAppearance"
        ) as HTMLSelectElement;

    const partyCategoryValue =
        (partyCategoryEl as any)?.tomselect
            ? (partyCategoryEl as any).tomselect.getValue()
            : partyCategoryEl?.value;

    const exemptionValue =
        (exemptionEl as any)?.tomselect
            ? (exemptionEl as any).tomselect.getValue()
            : exemptionEl?.value;

    const isExemptedCategory49 =
        String(partyCategoryValue) === "49" &&
        String(exemptionValue) === "Y";

    // Only these fields are compulsory when exempted
    const requiredOnlyFields = [
        'salutation_id',
        'party_full_name_en'
    ];

    // =========================
    // VALIDATE SELECT (TomSelect SAFE)
    // =========================

    const selects =
        document.querySelectorAll(
            '#partyForm select'
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

        const fieldName = field.name || field.id;

        // Skip fields not required under exemption bypass
        if (
            isExemptedCategory49 &&
            !requiredOnlyFields.includes(fieldName)
        ) {
            field.classList.remove('border-red-500');
            return;
        }

        let value = '';

        // Prefer TomSelect value
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
            fieldName,
            '=>',
            value
        );

        if (!value) {

            isValid = false;

            invalidFields.push(fieldName);

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
    // VALIDATE INPUTS + TEXTAREA
    // =========================

    const inputs =
        document.querySelectorAll(
            '#partyForm input, #partyForm textarea'
        );

    inputs.forEach((field: any) => {

        if (!field) return;

        if (field.disabled) return;

        if (field.type === 'hidden') return;

        // Skip TomSelect generated controls
        if (field.id?.includes('-ts-control')) return;
        if (field.classList?.contains('ts-control')) return;

        if (field.offsetParent === null) return;

        const fieldName = field.name || field.id;

        // Skip fields not required under exemption bypass
        if (
            isExemptedCategory49 &&
            !requiredOnlyFields.includes(fieldName)
        ) {
            field.classList.remove('border-red-500');
            return;
        }

        const value =
            (field.value ?? '')
                .toString()
                .trim();

        console.log(
            'INPUT:',
            fieldName,
            '=>',
            value
        );

        if (!value) {

            isValid = false;

            invalidFields.push(fieldName);

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