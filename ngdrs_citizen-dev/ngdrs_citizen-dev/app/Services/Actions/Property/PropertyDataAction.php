<?php

namespace App\Services\Actions\Property;

use App\Services\Integrations\Property\Dharitri;
use App\Services\Integrations\Property\Noc;
use App\Utils\Helpers;
use App\Models\ConfListItems;

class PropertyDataAction
{
    protected Noc $nocClient;
    protected Dharitri $dharitriClient;

    public function __construct(Noc $nocClient, Dharitri $dharitriClient)
    {
        $this->nocClient = $nocClient;
        $this->dharitriClient = $dharitriClient;
    }

    /**
     * Fetch and process the NOC data into internal application models.
     */
    public function fetchNoc(string $nocNo, string $dharitriDistrictCode): ?array
    {
        $resData = $this->nocClient->fetchNocDetails($nocNo, $dharitriDistrictCode);
        // dd($resData);
        if (isset($resData['msg'])) {
            return [
                'success' => false,
                'data' => null,
                'message' => $resData['msg'] ?? 'Something Went Wrong',
            ];
        }

        if (isset($resData['data']) && is_array($resData['data'])) {
            return Helpers::mapToLocalEntities($resData['data'], $resData, $dharitriDistrictCode);
        }

        return [
            'success' => false,
            'data' => null,
            'message' => 'Unexpected API response format',
        ];
    }

    public function fetchIndustrialLeaseNoc(string $nocNo, string $dharitriDistrictCode): ?array
    {
        $resData = $this->nocClient->fetchIndustrialLeaseDetails($nocNo, $dharitriDistrictCode);
        // dd($resData);
        if (isset($resData['msg'])) {
            return [
                'success' => false,
                'data' => null,
                'message' => $resData['msg'] ?? 'Something Went Wrong',
            ];
        }

        if (isset($resData['data']) && is_array($resData['data'])) {
            return Helpers::mapToLocalEntities($resData['data'], $resData, $dharitriDistrictCode);
        }

        return [
            'success' => false,
            'data' => null,
            'message' => 'Unexpected API response format',
        ];
    }

    public function verifyDag(array $validated): array
    {
        $codes = Helpers::getBasundharaCodes($validated);

        if ($validated['propertyId'] == 3) {
            $response = $this->dharitriClient->verifyBtrDag([
                'apikey' => config('services.land.veriy_dag.btr_api.apikey'),
                'distcode' => $codes['district'],
                'subdivcode' => $codes['subdivision'],
                'circode' => $codes['circle'],
                'mouzacode' => $codes['mouja'],
                'lotcode' => $codes['lot'],
                'villagecode' => $codes['village'],
                'dagno' => (int) $validated['dagNo'],
            ]);
        } else {
            $response = $this->dharitriClient->verifyDharitriDag([
                'apikey' => config('services.land.veriy_dag.dharitri_api.apikey'),
                'dist_code' => $codes['district'],
                'subdiv_code' => $codes['subdivision'],
                'cir_code' => $codes['circle'],
                'mouza_code' => $codes['mouja'],
                'lot_no' => $codes['lot'],
                'vill_code' => $codes['village'],
                'dag_no' => (int) $validated['dagNo'],
            ]);
        }

        if (!$response['success']) {
            return [
                'success' => false,
                'patta' => null,
                'message' => 'Unable to verify DAG at the moment.',
            ];
        }

        $data = $response['data'];

        return [
            'success' => ($data['responseType'] ?? 0) == 1,
            'patta' => $data['patta_no'] ?? null,
            'message' => $data['msg'] ?? 'Unknown response from land service.',
        ];
    }

    public function mapPattaDetails($dagItems)
    {
        $pattaTypes = collect($dagItems)
            ->pluck('pattatype')
            ->filter()
            ->unique();

        $pattaData = ConfListItems::whereIn('basundhara_patta_id', $pattaTypes)
            ->get(['id', 'basundhara_patta_id', 'item_desc_en'])
            ->keyBy('basundhara_patta_id');

        return collect($dagItems)
            ->map(function ($item) use ($pattaData) {

                $patta = $pattaData->get($item['pattatype']);

                $item['patta_type_id'] = $patta->id ?? null;
                $item['patta_type_desc'] = $patta->item_desc_en ?? null;

                return $item;
            })
            ->toArray();
    }
}

