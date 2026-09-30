<?php

namespace App\Utils;

use App\Models\ConfCircle;
use App\Models\ConfDistrict;
use App\Models\ConfSubdivision;
use App\Models\ConfTaluka;
use App\Models\ConfVillage;

class Helpers
{
    public static function getBasundharaCodes(array $data): array
    {
        return [
            'district' => ConfDistrict::getBasundharaDistrictCode($data['districtId']),
            'subdivision' => ConfSubdivision::getBasundharaSubdivCode($data['subDivId']),
            'circle' => ConfTaluka::getBasundharaCircleCode($data['circleId']),
            'mouja' => ConfCircle::getBasundharaMoujaCode($data['moujaId']),
            'lot' => ConfVillage::getBasundharaLotNo($data['villageId']),
            'village' => ConfVillage::getBasundharaVillageCode($data['villageId']),
        ];
    }

    /**
     * Map external codes to our database entities.
     */
    public static function mapToLocalEntities(array $data, array $fullResponse, string $dharitriDistrictCode): array
    {
        $dagNumbers = collect($data)->pluck('dagno')->toArray();
        $nocIssueDate = $data[0]['appissuedt'] ?? null;

        $district = ConfDistrict::getDistrictDetails($dharitriDistrictCode);

        if (empty($district)) {
            return ['success' => false, 'message' => 'District not found'];
        }

        $subDivision = ConfSubdivision::getSubDivisionDetails(
            $district->district_id,
            $data[0]['subcode']
        );

        if (empty($subDivision)) {
            return ['success' => false, 'message' => 'Subdivision not found'];
        }

        $taluka = ConfTaluka::getTalukaDetails($district->district_id, $subDivision->subdivision_id, $data[0]['circode']);
        if (empty($taluka)) {
            return ['success' => false, 'message' => 'Taluka not found'];
        }

        $circle = ConfCircle::getCircleDetails($district->district_id, $subDivision->subdivision_id, $taluka->taluka_id, $data[0]['mouzacode']);
        if (empty($circle)) {
            return ['success' => false, 'message' => 'Circle not found'];
        }

        $village = ConfVillage::getVillageDetails(
            $district->district_id,
            $subDivision->subdivision_id,
            $taluka->taluka_id,
            $circle->circle_id,
            $data[0]['lotno'],
            $data[0]['villcode']
        );

        $land_type = ($village->developed_land_types_id == 1) ? 'Urban' : 'Rural';

        if (empty($village)) {
            return ['success' => false, 'message' => 'Village not found'];
        }

        return [
            'success' => true,
            'data' => [
                'district_id' => $district->district_id,
                'district_name' => $district->district_name_en,
                'subdivision_id' => $subDivision->subdivision_id,
                'subdivision_name' => $subDivision->subdivision_name_en,
                'circle_id' => $taluka->taluka_id,
                'circle_name' => $taluka->taluka_name_en,
                'moza_id' => $circle->circle_id,
                'mouza_name' => $circle->circle_name_en,
                'village_id' => $village->village_id,
                'village_name' => $village->village_name_en,
                'land_type_id' => $village->developed_land_types_id,
                'land_type' => $land_type,
                'raw' => $fullResponse,
                'dag_list' => $dagNumbers,
                'patta_no' => $data[0]['pattano'],
                'noc_date' => $nocIssueDate
            ]
        ];
    }

    public static function formatIndianNumber($number): string
    {
        $number = (string) $number;

        $parts = explode('.', $number);
        $integer = $parts[0];

        $lastThree = substr($integer, -3);
        $remaining = substr($integer, 0, -3);

        if ($remaining !== '') {
            $remaining = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $remaining);
            $integer = $remaining . ',' . $lastThree;
        }

        return $integer . (isset($parts[1]) ? '.' . $parts[1] : '');
    }

}