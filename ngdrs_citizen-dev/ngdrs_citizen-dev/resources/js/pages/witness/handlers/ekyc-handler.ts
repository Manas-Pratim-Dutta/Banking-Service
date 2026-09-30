import {
    processWitnessEkyc
} from '../../../services/witness/witness-ekyc.service';

export function initializeWitnessEkyc() {

    const addBtn =
        document.getElementById(
            'addWitnessBtn'
        );

    if (!addBtn) {
        return;
    }

    addBtn.addEventListener(
        'click',
        async () => {

            const result =
                await processWitnessEkyc();

            if (
                result.success
            ) {

                window.location.href =
                    result.redirect_url;
            }
        }
    );
}