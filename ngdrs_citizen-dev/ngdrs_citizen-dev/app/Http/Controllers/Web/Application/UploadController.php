<?php

namespace App\Http\Controllers\Web\Application;

use App\Http\Controllers\Controller;
use App\Models\ArticleDocumentMap;
use App\Models\FileConfig;
use App\Models\TrnGeneralInformation;
use App\Models\FileUploadInfo;
use App\Services\Module\UploadDocument\UploadDocument;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;

// class UploadController extends Controller
// {
//     // public function index()
//     // {
//     //     return view('pages.upload-document', [
//     //         'preRegNo' => 202500021456
//     //     ]);
//     // }



class UploadController extends Controller
{
    public function index()
    {

        /*
        |--------------------------------------------------------------------------
        | TEMP STATIC VALUES
        |--------------------------------------------------------------------------
        */

        $token = session('token_no');

        $articleId = TrnGeneralInformation::getArticleId($token);


        $documents = ArticleDocumentMap::with('document')

            ->join(
                'ngdrstab_mst_upload_document',
                'ngdrstab_mst_article_document_mapping.document_id',
                '=',
                'ngdrstab_mst_upload_document.document_id'
            )

            ->where(
                'ngdrstab_mst_article_document_mapping.article_id',
                $articleId
            )

            ->where(
                'ngdrstab_mst_upload_document.partywise_flag',
                'N'
            )

            ->orderBy(
                'ngdrstab_mst_upload_document.display_order',
                'ASC'
            )

            ->select(
                'ngdrstab_mst_article_document_mapping.*'
            )

            ->get();



        /*
        |--------------------------------------------------------------------------
        | Already Uploaded Documents
        |--------------------------------------------------------------------------
        |
        | Same as:
        |
        | uploaded_file_trn->find()
        |
        */


        $uploadedFiles = FileUploadInfo::where(
            'token_no',
            $token
        )
            ->get()
            ->keyBy('document_id');



        /*
        |--------------------------------------------------------------------------
        | Return View
        |--------------------------------------------------------------------------
        */


        return view(
            'pages.upload-document',
            [

                'documents' => $documents,

                'uploadedFiles' => $uploadedFiles,

                'token' => $token,

                'preRegNo' => $token

            ]
        );
    }

    public function viewFile(
        $id,
        UploadDocument $service
    ) {

        return $service->viewFile($id);
    }
}
