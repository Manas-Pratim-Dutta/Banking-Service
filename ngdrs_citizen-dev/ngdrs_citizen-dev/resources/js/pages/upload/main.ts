import axios from "axios";

document.querySelectorAll<HTMLElement>('[data-upload-wrapper]')
    .forEach(wireField);


interface UploadResponse {
    status: boolean;
    message: string;
    data?: any;
}



function getToken(): string {

    const form =
        document.querySelector<HTMLElement>(
            '#uploadForm'
        );


    return form?.dataset.token ?? '';

}




function wireField(wrapper: HTMLElement) {

    const input =
        wrapper.querySelector<HTMLInputElement>('[data-upload-input]')!;


    const documentId =
        input.dataset.documentId!;



    const nameEl =
        wrapper.querySelector<HTMLElement>('[data-file-name]')!;


    const box =
        wrapper.querySelector<HTMLElement>('[data-upload-box]')!;



    const uploadBtn =
        wrapper.querySelector<HTMLButtonElement>('[data-upload-btn]')!;



    const previewBtn =
        wrapper.querySelector<HTMLButtonElement>('[data-preview-btn]')!;



    const downloadBtn =
        wrapper.querySelector<HTMLButtonElement>('[data-download-btn]')!;



    const clearBtn =
        wrapper.querySelector<HTMLButtonElement>('[data-clear-btn]')!;



    let currentFile: File | null = null;




    /*
    |--------------------------------------------------------------------------
    | File Selected
    |--------------------------------------------------------------------------
    */


    input.addEventListener(
        'change',
        () => {


            const file =
                input.files?.[0];


            if (!file)
                return;




            if (
                file.size >
                1024 * 1024
            ) {


                setError(
                    wrapper,
                    box,
                    "File size should not exceed 1 MB"
                );


                input.value = '';

                return;

            }




            clearError(
                wrapper,
                box
            );



            currentFile = file;



            nameEl.textContent =
                file.name;




            show(uploadBtn);

            show(clearBtn);



            hide(previewBtn);

            hide(downloadBtn);



        }
    );





    /*
    |--------------------------------------------------------------------------
    | Upload Button
    |--------------------------------------------------------------------------
    */


    uploadBtn.addEventListener(
        'click',
        () => {


            if (currentFile) {


                uploadFile(

                    wrapper,

                    currentFile,

                    documentId,

                    getToken()

                );


            }


        }
    );





    /*
    |--------------------------------------------------------------------------
    | Preview
    |--------------------------------------------------------------------------
    */


    previewBtn.addEventListener(
        'click',
        () => {


            if (currentFile)

                openModal(currentFile);


        }
    );





    /*
    |--------------------------------------------------------------------------
    | Download
    |--------------------------------------------------------------------------
    */


    downloadBtn.addEventListener(
        'click',
        () => {



            if (!currentFile)

                return;




            const url =
                URL.createObjectURL(
                    currentFile
                );



            const a =
                document.createElement('a');



            a.href = url;


            a.download =
                currentFile.name;



            a.click();



            URL.revokeObjectURL(url);



        }
    );





    /*
    |--------------------------------------------------------------------------
    | Clear
    |--------------------------------------------------------------------------
    */


    clearBtn.addEventListener(
        'click',
        () => {



            currentFile = null;


            input.value = '';



            nameEl.textContent =
                "No file chosen";



            hide(uploadBtn);

            hide(previewBtn);

            hide(downloadBtn);

            hide(clearBtn);



            box.classList.remove(
                'border-green-300'
            );



        }
    );


}






/*
|--------------------------------------------------------------------------
| Upload API
|--------------------------------------------------------------------------
*/


async function uploadFile(

    wrapper: HTMLElement,

    file: File,

    documentId: string,

    token: string

) {



    const uploadBtn =
        wrapper.querySelector<HTMLButtonElement>(
            '[data-upload-btn]'
        )!;



    const previewBtn =
        wrapper.querySelector<HTMLButtonElement>(
            '[data-preview-btn]'
        )!;



    const downloadBtn =
        wrapper.querySelector<HTMLButtonElement>(
            '[data-download-btn]'
        )!;



    const box =
        wrapper.querySelector<HTMLElement>(
            '[data-upload-box]'
        )!;



    const formData =
        new FormData();




    /*
    |--------------------------------------------------------------------------
    | Token
    |--------------------------------------------------------------------------
    */

    formData.append(
        "token",
        token
    );



    /*
    |--------------------------------------------------------------------------
    | Laravel expects:
    |
    | documents[document_id] => file
    |
    |--------------------------------------------------------------------------
    */


    formData.append(

        `documents[${documentId}]`,

        file

    );

    try {


        uploadBtn.disabled = true;


        uploadBtn.innerText =
            "Uploading...";




        const response = await axios.post(

            "/api/documents/upload",

            formData,

            {
                headers: {
                    "Content-Type":
                        "multipart/form-data"
                }
            }

        );




        const result: UploadResponse =
            response.data;





        console.log(result);





        if (result.status) {



            hide(uploadBtn);


            show(previewBtn);


            show(downloadBtn);




            box.classList.add(
                "border-green-300"
            );

            // Create View File link immediately
            if (result.data?.up_id) {


                const viewContainer =
                    wrapper.querySelector<HTMLElement>(
                        '[data-view-file]'
                    );


                if (viewContainer) {


                    viewContainer.innerHTML = `

                <a href="/documents/view/${result.data.up_id}"
                   target="_blank"
                   class="ml-2 text-orange-500 hover:text-orange-700 text-xs font-semibold">

                    View File

                </a>

            `;


                }

            }



        }
        else {



            setError(

                wrapper,

                box,

                result.message ??
                "Upload failed"

            );


        }





    }
    catch (error: any) {



        console.error(
            "Upload Error:",
            error
        );




        if (
            error.response?.data?.message
        ) {


            setError(

                wrapper,

                box,

                error.response.data.message

            );


        }
        else {


            setError(

                wrapper,

                box,

                "Upload failed"

            );


        }



    }
    finally {


        uploadBtn.disabled = false;


        uploadBtn.innerText =
            "Upload";


    }



}









/*
|--------------------------------------------------------------------------
| Upload All
|--------------------------------------------------------------------------
*/


document
    .getElementById('uploadAllBtn')
    ?.addEventListener(
        'click',
        () => {



            document
                .querySelectorAll<HTMLElement>(
                    '[data-upload-wrapper]'
                )
                .forEach(wrapper => {



                    const input =
                        wrapper.querySelector<HTMLInputElement>(
                            '[data-upload-input]'
                        )!;




                    const file =
                        input.files?.[0];





                    if (file) {



                        uploadFile(

                            wrapper,

                            file,

                            input.dataset.documentId!,

                            getToken()

                        );


                    }




                });



        }
    );










/*
|--------------------------------------------------------------------------
| Preview Modal
|--------------------------------------------------------------------------
*/


function openModal(file: File) {



    const modal =
        document.getElementById(
            'previewModal'
        )!;



    const content =
        document.getElementById(
            'previewContent'
        )!;



    const title =
        document.getElementById(
            'previewModalTitle'
        )!;





    title.textContent =
        file.name;



    content.innerHTML =
        '';




    const url =
        URL.createObjectURL(
            file
        );





    if (
        file.type ===
        "application/pdf"
    ) {



        const iframe =
            document.createElement(
                'iframe'
            );



        iframe.src =
            url;



        iframe.className =
            "w-full rounded-lg";



        iframe.style.height =
            "65vh";



        content.appendChild(
            iframe
        );



    }



    else if (
        file.type.startsWith(
            'image/'
        )
    ) {



        const img =
            document.createElement(
                'img'
            );



        img.src =
            url;



        img.className =
            "max-w-full mx-auto";



        content.appendChild(
            img
        );



    }





    modal.classList.remove(
        'hidden'
    );


    modal.classList.add(
        'flex'
    );



}









function closePreview() {



    const modal =
        document.getElementById(
            'previewModal'
        )!;



    modal.classList.add(
        'hidden'
    );


    modal.classList.remove(
        'flex'
    );



}





(window as any)
    .closePreview =
    closePreview;









/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/


function show(
    el: HTMLElement
) {

    el.classList.remove(
        'hidden'
    );


    el.classList.add(
        'flex'
    );

}



function hide(
    el: HTMLElement
) {

    el.classList.add(
        'hidden'
    );


    el.classList.remove(
        'flex'
    );

}





function setError(

    wrapper: HTMLElement,

    box: HTMLElement,

    msg: string

) {



    let err =
        wrapper.querySelector(
            '[data-error]'
        );




    if (!err) {



        err =
            document.createElement(
                'p'
            );



        err.className =
            "text-xs text-red-500";



        err.setAttribute(
            'data-error',
            ''
        );



        wrapper.appendChild(
            err
        );


    }




    err.textContent =
        msg;




    box.classList.add(
        'border-red-300'
    );



}







function clearError(

    wrapper: HTMLElement,

    box: HTMLElement

) {



    wrapper
        .querySelector(
            '[data-error]'
        )
        ?.remove();




    box.classList.remove(
        'border-red-300'
    );



}