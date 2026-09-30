<?php

namespace App\Http\Controllers\Web\Application;

use App\Http\Controllers\Controller;


use Illuminate\Http\Request;
use App\Services\Actions\Esign\EsignService;


class EsignController extends Controller
{


    protected $esign;


    public function __construct(
        EsignService $esign
    ) {

        $this->esign = $esign;

    }



    public function start(Request $request)
    {

        return $this->esign
            ->start($request);

    }
    public function indexNew(Request $request)
    {
        return $this->esign
            ->indexNew($request);
    }



    public function response(Request $request)
    {

        return $this->esign
            ->response($request);

    }



    public function servePdf($file)
    {

        $path = base64_decode($file);


        if (!file_exists($path)) {

            abort(404);

        }


        return response()
            ->file($path);

    }



}