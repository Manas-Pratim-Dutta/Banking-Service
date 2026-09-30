<?php

namespace App\Services\Module\UploadDocument;


use App\Models\FileConfig;
use App\Models\FileUploadInfo;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class UploadDocument
{


    /**
     * Build uploaded document folder path
     */
    private function getUploadFolder($token)
    {


        /*
        |--------------------------------------------------------------------------
        | TEMP STATIC
        |--------------------------------------------------------------------------
        */

        $district = "Kamrup";
        $talukaId = 12;
        $officeId = 105;



        $config = FileConfig::first();



        return
            $config->filepath
            . "/Documents/"
            . $district
            . "/"
            . $talukaId
            . "/"
            . $officeId
            . "/"
            . $token
            . "/Uploads/";
    }




    public function upload(
        $token,
        $documentId,
        $file
    ) {


        return DB::transaction(function () use (
            $token,
            $documentId,
            $file
        ) {



            /*
            |--------------------------------------------------------------------------
            | Get Folder
            |--------------------------------------------------------------------------
            */

            $folder = $this->getUploadFolder($token);



            if (!File::exists($folder)) {

                File::makeDirectory(
                    $folder,
                    0755,
                    true
                );
            }




            /*
            |--------------------------------------------------------------------------
            | Existing File
            |--------------------------------------------------------------------------
            */

            $existing = FileUploadInfo::where(
                'token_no',
                $token
            )
                ->where(
                    'document_id',
                    $documentId
                )
                ->first();





            /*
            |--------------------------------------------------------------------------
            | Delete Old File
            |--------------------------------------------------------------------------
            */

            if (
                $existing &&
                $existing->out_fname
            ) {


                $oldFile =
                    $folder .
                    $existing->out_fname;



                if (File::exists($oldFile)) {

                    File::delete($oldFile);
                }
            }




            /*
            |--------------------------------------------------------------------------
            | New File Name
            |--------------------------------------------------------------------------
            */

            $extension =
                strtolower(
                    $file->getClientOriginalExtension()
                );


            $fileName =
                $token
                . "_"
                . $documentId
                . "."
                . $extension;





            /*
            |--------------------------------------------------------------------------
            | Save Physical File
            |--------------------------------------------------------------------------
            */

            $file->move(
                $folder,
                $fileName
            );





            /*
            |--------------------------------------------------------------------------
            | Update DB
            |--------------------------------------------------------------------------
            */

            if ($existing) {


                $existing->update([

                    'input_fname' =>
                    $file->getClientOriginalName(),

                    'out_fname' =>
                    $fileName,

                    'updated' =>
                    now(),

                    'req_ip' =>
                    request()->ip(),

                    'user_id' =>
                    1,

                ]);

                $uploadedFile = $existing;
            } else {



                $uploadedFile =  FileUploadInfo::create([

                    'token_no' =>
                    $token,

                    'document_id' =>
                    $documentId,

                    'input_fname' =>
                    $file->getClientOriginalName(),

                    'out_fname' =>
                    $fileName,

                    'state_id' =>
                    18,

                    'req_ip' =>
                    request()->ip(),

                    'created' =>
                    now(),

                    'user_id' =>
                    1,

                    'user_type' =>
                    'C'

                ]);
            }


            // return true;
            return $uploadedFile;
        });
    }





    /**
     * Show uploaded file in browser
     */
    public function viewFile($id)
    {


        $fileInfo =
            FileUploadInfo::findOrFail($id);




        /*
        |--------------------------------------------------------------------------
        | Same folder used during upload
        |--------------------------------------------------------------------------
        */

        $folder =
            $this->getUploadFolder(
                $fileInfo->token_no
            );




        $filePath =
            $folder .
            $fileInfo->out_fname;





        if (!File::exists($filePath)) {

            abort(
                404,
                "File not found"
            );
        }




        return response()->file(
            $filePath
        );
    }
}
