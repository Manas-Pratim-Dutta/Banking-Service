import {
    processIdentifierEkyc
} from '../../../services/identifier/identifier-ekyc.service';

export function initializeIdentifierEkyc() {

    const addBtn =
        document.getElementById(
            'addIdentifierBtn'
        );

    if (!addBtn) {
        return;
    }

    addBtn.addEventListener(
        'click',
        async () => {

            const result =
                await processIdentifierEkyc();

            if (
                result.success
            ) {

                window.location.href =
                    result.redirect_url;
            }
        }
    );
}