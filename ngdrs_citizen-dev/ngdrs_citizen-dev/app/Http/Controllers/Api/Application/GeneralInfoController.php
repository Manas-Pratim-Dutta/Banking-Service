<?php

namespace App\Http\Controllers\Api\Application;

use App\Http\Controllers\Controller;
use App\Models\ArticleDetailDescription;
use App\Models\Office;
use Illuminate\Http\JsonResponse;
use App\Models\Taluka;

class GeneralInfoController extends Controller
{
    public function getTalukas(int $districtId): JsonResponse
    {
        if (!is_numeric($districtId)) {
            return response()->json([], 400);
        }

        $column = 'taluka_name_en'; // keep simple (no session in API)

        $talukas = Taluka::where('district_id', $districtId)
            ->orderBy($column)
            ->get([
                'taluka_id',
                $column . ' as name'
            ]);

        return response()->json($talukas);
    }

    public function getOffices(int $talukaId): JsonResponse
    {
        $offices = Office::where('taluka_id', $talukaId)
            ->get([
                'office_id',
                'office_name_en as name'
            ]);

        return response()->json($offices);
    }

    public function getTitle(int $articleId)
    {
        if (!is_numeric($articleId)) {
            return response()->json([
                'title' => null
            ], 400);
        }

        $title = ArticleDetailDescription::where('article_id', $articleId)
            ->value('articledescription_en');
        $title_id = ArticleDetailDescription::where('article_id', $articleId)
            ->value('articledescription_id');

        return response()->json([
            'title' => $title,
            'title_id' => $title_id
        ]);
    }  
}
