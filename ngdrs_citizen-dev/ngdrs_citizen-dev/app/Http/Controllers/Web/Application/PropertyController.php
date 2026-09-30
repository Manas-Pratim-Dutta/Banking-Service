<?php

namespace App\Http\Controllers\Web\Application;

use App\Http\Controllers\Controller;
use App\Models\PropertyAreaType;
use App\Models\PropertyType;
use App\Models\TrnGeneralInformation;

class PropertyController extends Controller
{
    public function index()
    {
        $areaType = PropertyAreaType::getAreaTypeOptions();
        $propertyType = PropertyType::getPropertyTypeOptions();
        // dd(session()->all());
        $tokenNo = session('token_no');
        // dd($tokenNo);
        // $tokenNo = '202600030672';
        $district = $tokenNo ? TrnGeneralInformation::getDistrictDetail($tokenNo) : [];
        // dd($district);
        // exit;
        $nocFlag = false;
        $nocNo = 'NOC/24/66469/2026';

        return view('pages.property', compact('areaType', 'propertyType', 'district', 'nocFlag', 'nocNo', 'tokenNo'));
    }
}
