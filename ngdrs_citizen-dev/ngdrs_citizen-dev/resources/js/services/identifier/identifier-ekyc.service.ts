export async function processIdentifierEkyc() {

    const response =
        await fetch(
            '/add-identifier',
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