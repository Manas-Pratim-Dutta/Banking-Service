<?php

namespace App\Services\Actions\Party;

use Firebase\JWT\JWT;

class NocLandhubService
{
    protected string $jwtKey = '3bf93d2a0c2628f8020e8ecc9389db5314d7c0b43980a2d6a772f2f9a990153111b513fbc252152e8e5253e89f1841d49a6917dc7f6db4025f2386a9cd143de0';

    public function generateJwtToken(): string
    {
        $payload = ['iat' => time()];

        return JWT::encode($payload, $this->jwtKey, 'HS512');
    }
        /**
     * Equivalent of the property_type_id == 16 branch in
     * viewnoc_partydetails_landhub (industry/govt lease NOC).
     */
    public function fetchGovtLeaseData(string $appno, int $tokenNo): array
    {
        $jwt = $this->generateJwtToken();

        $distCode = \DB::table('ngdrstab_trn_generalinformation as g')
            ->join('ngdrstab_conf_admblock3_district as d', 'd.district_id', '=', 'g.district_id')
            ->where('g.token_no', $tokenNo)
            ->orderBy('g.general_info_id')
            ->value('d.basundhara_dharitri_code');

        $response = \Http::asForm()
            ->withHeaders(['Authorization' => 'Bearer ' . $jwt])
            ->post('https://landhub.assam.gov.in/nocApi/index.php/Industry/fetch/get-all-data', [
                'user_name' => 'ngdrs',
                'dist_code' => $distCode,
                'appno' => $appno,
            ]);

        $decoded = $response->json() ?? [];

        $sellerSeq = 1;
        $buyerSeq = 1;
        $landSeq = 1;

        return [
            'responseType' => $decoded['status'] ?? null,
            'data' => [
                'nocdata' => [
                    'appno' => $decoded['data']['noc_data']['appno'] ?? null,
                    'combkey' => '',
                    'considerationamount' => $decoded['data']['noc_data']['rent_amount'] ?? null,
                    'lease_years' => $decoded['data']['noc_data']['lease_period'] ?? null,
                ],
                'seller' => collect($decoded['data']['lessor_data'] ?? [])->map(function ($item) use (&$sellerSeq) {
                    return [
                        'slno' => $sellerSeq++,
                        'sname' => $item['name'] ?? '',
                        'sfname' => $item['represented_by'] ?? '',
                        'pan' => $item['pan'] ?? '',
                        'phone' => $item['phone'] ?? '',
                        'prhouse' => '',
                        'prlocality' => $item['address'] ?? '',
                        'prpin' => '',
                        'prpoff' => '',
                        'prdist' => '',
                        'pattadar_id' => '',
                        'pattano' => '',
                        'dagno' => '',
                    ];
                })->values()->all(),
                'buyer' => collect($decoded['data']['lessee_data'] ?? [])->map(function ($item) use (&$buyerSeq) {
                    return [
                        'slno' => $buyerSeq++,
                        'bname' => $item['name'] ?? '',
                        'bfname' => $item['represented_by'] ?? '',
                        'pan' => $item['pan'] ?? '',
                        'phone' => $item['phone'] ?? '',
                        'prhouse' => '',
                        'prlocality' => $item['address'] ?? '',
                        'prpin' => '',
                        'prpoff' => '',
                        'prdist' => '',
                        'pattadar_id' => '',
                        'pattano' => '',
                        'dagno' => '',
                    ];
                })->values()->all(),
                'power_of_attorney' => [],
                'landschedule' => collect($decoded['data']['landschedule_data'] ?? [])->map(function ($item) use (&$landSeq) {
                    return [
                        'appslno' => $landSeq++,
                        'lncombkey' => "{$item['distcode']}{$item['subcode']}{$item['circode']}{$item['mouzacode']}{$item['lotno']}{$item['villcode']}",
                        'bigha' => $item['bigha'] ?? null,
                        'katha' => $item['katha'] ?? null,
                        'lessa' => $item['lessa'] ?? null,
                        'are' => 'N/A',
                        'acre' => 'N/A',
                        'sqfeet' => 'N/A',
                        'pattano' => $item['pattano'] ?? null,
                        'dagno' => $item['dagno'] ?? null,
                        'ganda' => $item['ganda'] ?? null,
                        'pattatype' => $item['pattatype'] ?? null,
                        'district' => $item['distcode'] ?? null,
                        'northside' => $item['northside'] ?? null,
                        'southside' => $item['southside'] ?? null,
                        'eastside' => $item['eastside'] ?? null,
                        'westside' => $item['westside'] ?? null,
                        'unique_village_code' => $item['villcode'] ?? null,
                    ];
                })->values()->all(),
            ],
        ];
    }

    /**
     * Equivalent of the non-govt-lease branch (NicApi/seller call).
     * That endpoint already returns the { data: { nocdata, seller, buyer,
     * power_of_attorney, landschedule } } shape directly, so no
     * transform is needed here — just normalize into an array.
     */
    public function fetchStandardNocData(string $appno): array
    {
        $response = \Http::asForm()
            ->timeout(5)
            ->connectTimeout(5)
            ->post('https://landhub.assam.gov.in/api/index.php/NicApi/seller', [
                'apikey' => 'abcd123haryanasinglesigonapplicationDFFEFSDAFETUVX',
                'appno' => $appno,
            ]);

        return $response->json() ?? [];
    }
}