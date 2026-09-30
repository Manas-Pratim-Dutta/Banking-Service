export async function processWitnessEkyc() {

    const response =
        await fetch(
            '/add-witness',
            {
                method: 'POST',
                headers: {
                    'Content-Type':
                        'application/json',

                    'X-CSRF-TOKEN':
                        (
                            document.querySelector(
                                'meta[name="csrf-token"]'
                            ) as HTMLMetaElement
                        ).content
                },

                body: JSON.stringify({})
            }
        );

    return response.json();
}