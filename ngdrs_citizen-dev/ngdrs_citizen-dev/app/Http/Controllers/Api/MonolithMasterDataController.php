<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Articles;
use App\Models\ConfVillage;
use App\Models\Regconfig;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class MonolithMasterDataController extends Controller
{
    /**
     * Get Article by ID.
     * GET /api/v1/monolith/articles/{article_id}
     */
    public function getArticle(int $articleId): JsonResponse
    {
        $article = Articles::where('article_id', $articleId)->first();

        if (!$article) {
            return response()->json([
                'status'  => 'error',
                'message' => "Article with ID {$articleId} not found",
            ], 404);
        }

        return response()->json([
            'article_id'               => (int) $article->article_id,
            'article_desc_en'          => (string) ($article->article_desc_en ?? ''),
            'valuation_applicable'     => (string) ($article->valuation_applicable ?? 'N'),
            'stamp_duty_applicable'    => (string) ($article->stamp_duty_applicable ?? 'N'),
            'exemption_applicable'     => (string) ($article->exemption_applicable ?? 'N'),
        ]);
    }

    /**
     * Get Village Details by ID.
     * GET /api/v1/monolith/villages/{village_id}
     */
    public function getVillage(int $villageId): JsonResponse
    {
        $village = ConfVillage::where('village_id', $villageId)->first();

        if (!$village) {
            return response()->json([
                'status'  => 'error',
                'message' => "Village with ID {$villageId} not found",
            ], 404);
        }

        $isCorp = false;
        if (!empty($village->is_corporatiion)) {
            $isCorp = strtoupper((string) $village->is_corporatiion) === 'Y' || $village->is_corporatiion === true || $village->is_corporatiion == 1;
        }

        return response()->json([
            'village_id'              => (int) $village->village_id,
            'village_name_en'         => (string) ($village->village_name_en ?? ''),
            'ulb_type_id'             => $village->ulb_type_id !== null ? (int) $village->ulb_type_id : null,
            'corp_id'                 => $village->corp_id !== null ? (int) $village->corp_id : null,
            'is_corporation'          => $isCorp,
            'developed_land_types_id' => $village->developed_land_types_id !== null ? (int) $village->developed_land_types_id : null,
        ]);
    }

    /**
     * Get Global Boolean Config.
     * GET /api/v1/monolith/bool-info/{reginfo_id}
     */
    public function getBoolInfo(int $reginfoId): JsonResponse
    {
        $config = Regconfig::where('reginfo_id', $reginfoId)->first();

        if (!$config) {
            return response()->json([
                'status'  => 'error',
                'message' => "Config with ID {$reginfoId} not found",
            ], 404);
        }

        return response()->json([
            'reginfo_id'       => (int) $config->reginfo_id,
            'conf_code'        => (string) ($config->conf_code ?? ''),
            'conf_bool_value'  => (string) ($config->conf_bool_value ?? 'N'),
        ]);
    }

    /**
     * Get Developed Land Type.
     * GET /api/v1/monolith/developed-land-types/{id}
     */
    public function getDevelopedLandType(int $id): JsonResponse
    {
        $landType = DB::table('ngdrstab_mst_developed_land_types')
            ->where('developed_land_types_id', $id)
            ->first();

        if (!$landType) {
            return response()->json([
                'status'  => 'error',
                'message' => "Developed land type with ID {$id} not found",
            ], 404);
        }

        return response()->json([
            'developed_land_types_id' => (int) $landType->developed_land_types_id,
            'land_type_desc_en'       => (string) ($landType->developed_land_types_desc_en ?? ''),
        ]);
    }
}
