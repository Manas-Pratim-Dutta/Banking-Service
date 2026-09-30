<?php

namespace App\Http\Controllers\Api\Application;

use App\Http\Controllers\Controller;
use App\Services\Module\UploadDocument\UploadDocument;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function upload(
        Request $request,
        UploadDocument $service
    ) {


        // $token = 202620450558;
        $token = $request->token;



        $request->validate([

            'documents' => 'required|array',

            'documents.*' => [
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:1024'
            ]

        ]);




        // foreach (
        //     $request->file('documents')
        //     as $documentId => $file
        // ) {


        //     $service->upload(

        //         token: $token,

        //         documentId: $documentId,

        //         file: $file

        //     );
        // }

        $uploadedFiles = [];


        foreach (
            $request->file('documents')
            as $documentId => $file
        ) {


            $uploadedFile = $service->upload(

                token: $token,

                documentId: $documentId,

                file: $file

            );
        }

        return response()->json([

            'status' => true,

            'message' => 'Documents uploaded successfully',
            'data' => [
                'up_id' => $uploadedFile->up_id,
                'file_name' => $uploadedFile->input_fname,
            ]

        ]);
    }
}
