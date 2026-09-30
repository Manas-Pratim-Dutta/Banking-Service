<?php

namespace App\Services\Actions\EDeed;

use Illuminate\Support\Facades\DB;

/**
 * Laravel port of the CakePHP controller's dataQuery($tokenNo) method.
 * Produces the exact $data shape EDeedTemplate.php expects (CakePHP raw
 * query()-style rows: array of [0 => [...fields]]).
 */
class EDeedDataQueryService
{
    protected const WILL_POA_ARTICLE_IDS = [217, 220];

    protected float $lastTotalValuationAmount = 0;

    public function build(string $tokenNo, int $articleId): array
    {
        $data = [];

        $data['property']             = $this->buildProperty($tokenNo, $articleId);
        $data['totalValuationAmount'] = $this->lastTotalValuationAmount;
        $data['party']                = $this->buildParty($tokenNo);
        $data['e_deed_party_transaction_details'] = $this->buildTransactionDetails($tokenNo);
        $data['witness']              = $this->buildWitness($tokenNo);
        $data['identifier']           = $this->buildIdentifier($tokenNo);
        $data['cons_amt']             = $this->buildConsAmt($tokenNo);
        $data['stamp_duty']           = $this->buildStampDuty($tokenNo);
        $data['office_name']          = $this->buildOfficeName($tokenNo);
        $data['noc']                  = $this->buildNoc($tokenNo);
        $data['fee_item_value']       = $this->buildFeeItemValue($tokenNo, 98);
        $data['loan_amount']          = $this->buildFeeItemValue($tokenNo, 10);
        $data['additional_data']      = $this->buildAdditionalData($tokenNo);
        $data['additional_data1']     = $this->buildAdditionalData1($tokenNo);

        return $data;
    }

    private function wrapCakeStyle(array $rows): array
    {
        return array_map(fn ($row) => [0 => (array) $row], $rows);
    }

    // ---------------------------------------------------------------
    // PROPERTY
    // ---------------------------------------------------------------

    private function buildProperty(string $tokenNo, int $articleId): array
    {
        if (in_array($articleId, self::WILL_POA_ARTICLE_IDS, true)) {
            return $this->buildPropertyWillPoa($tokenNo);
        }

        return $this->buildPropertyStandard($tokenNo, $articleId);
    }

    /**
     * NOTE (needs verification): property_details_entry_will_poa
     * ->get_property_detail_list1() wasn't provided. Approximated here
     * as "one iteration per property_details_entry_will_poa row for this
     * token" — the loop body itself is a faithful 1:1 port of the raw
     * SQL inline in dataQuery(), including its quirk of re-running the
     * same (token-wide, not per-property) location/office lookup on
     * every iteration.
     */
    private function buildPropertyWillPoa(string $tokenNo): array
    {
        $propertyCount = (int) (DB::selectOne(
            "SELECT COUNT(*) AS cnt FROM ngdrstab_trn_property_details_entry_will_poa WHERE token_no = ?",
            [$tokenNo]
        )->cnt ?? 0);

        $this->lastTotalValuationAmount = 0;

        if ($propertyCount === 0) {
            return [];
        }

        $location = DB::select("
            SELECT prop.bigha, prop.katha, prop.lessa, prop.area_in_acres,
                   subdiv.subdivision_name_en, dist.district_name_en,
                   tal.taluka_name_en, cir.circle_name_en, vill.village_name_en,
                   dag.level_1_desc_en, prop.patta_no AS paramter_value
            FROM ngdrstab_trn_property_details_entry_will_poa AS prop
            LEFT JOIN ngdrstab_conf_admblock4_subdivision subdiv ON subdiv.subdivision_id = prop.subdivision_id
            LEFT JOIN ngdrstab_conf_admblock3_district dist ON dist.district_id = prop.district_id
            LEFT JOIN ngdrstab_conf_admblock5_taluka tal ON tal.taluka_id = prop.taluka_id
            LEFT JOIN ngdrstab_conf_admblock6_circle cir ON cir.circle_id = prop.circle_id
            LEFT JOIN ngdrstab_conf_admblock7_village_mapping vill ON vill.village_id = prop.village_id
            LEFT JOIN ngdrstab_mst_location_levels_1_property dag
                   ON dag.level_1_id = prop.level1_id AND dag.village_id = prop.village_id
            WHERE prop.token_no = ?
        ", [$tokenNo]);

        $office = $this->buildOfficeName($tokenNo);

        $properties = [];

        for ($i = 0; $i < $propertyCount; $i++) {
            $properties[] = [
                'area'     => [],
                'location' => $location ? (array) $location[0] : [],
                'office'   => $office,
            ];
        }

        return $properties;
    }

    /**
     * NOTE (needs verification): two model methods were not provided:
     *
     *  - property_details_entry->get_property_detail_list($lang, $tokenNo, $citizenUserId)
     *    approximated below as a direct SELECT reading only the fields
     *    the original code actually consumes off each row.
     *
     *  - valuation_details->get_valuation_details_cake($lang, $valId)
     *    approximated in buildPropertyAreaLines() below. Table names
     *    (ngdrstab_mst_valuation_item / _list, ngdrstab_mst_unit) are
     *    inferred from field usage and MUST be confirmed.
     *
     * Everything else here (location query, office query, boundaries,
     * zonal class, valuation total, and the `$i` / article-46 location
     * indexing quirk) is a 1:1 port of the original.
     */
    private function buildPropertyStandard(string $tokenNo, int $articleId): array
    {
        $rptPropDtl = DB::select("
            SELECT
                property_details_entry.property_id,
                property_details_entry.property_group,
                property_details_entry.fee_calc_id,
                property_details_entry.val_id,
                property_details_entry.boundries_east_en,
                property_details_entry.boundries_west_en,
                property_details_entry.boundries_south_en,
                property_details_entry.boundries_north_en,
                property_details_entry.police_station,
                property_details_entry.post_office,
                property_details_entry.pincode,
                property_details_entry.property_description,
                village.village_name_en,
                district.district_name_en,
                taluka.taluka_name_en,
                sdc.cons_amt
            FROM ngdrstab_trn_property_details_entry AS property_details_entry
            JOIN ngdrstab_conf_admblock7_village_mapping AS village
                ON village.village_id = property_details_entry.village_id
            LEFT JOIN ngdrstab_conf_admblock3_district AS district
                ON district.district_id = property_details_entry.district_id
            LEFT JOIN ngdrstab_conf_admblock5_taluka AS taluka
                ON taluka.taluka_id = property_details_entry.taluka_id
            LEFT JOIN ngdrstab_trn_fee_calculation AS sdc
                ON sdc.token_no = property_details_entry.token_no
               AND sdc.article_id != 9998
               AND sdc.delete_flag = 'N'
               AND sdc.property_id = property_details_entry.property_id
               AND sdc.cons_amt IS NOT NULL
            WHERE property_details_entry.token_no = ?
            ORDER BY property_details_entry.property_id
        ", [$tokenNo]);

        // --- everything below is unchanged from the previous version ---

        $location = DB::select("
            SELECT DISTINCT
                prop.property_type_id, prop.noc_number, prop.noc_date,
                subdiv.subdivision_name_en, dist.district_name_en,
                tal.taluka_name_en, cir.circle_name_en, vill.village_name_en,
                dag.level_1_desc_en, patta.paramter_value
            FROM ngdrstab_trn_property_details_entry AS prop
            LEFT JOIN ngdrstab_conf_admblock4_subdivision AS subdiv ON subdiv.subdivision_id = prop.subdivision_id
            LEFT JOIN ngdrstab_conf_admblock3_district AS dist ON dist.district_id = prop.district_id
            LEFT JOIN ngdrstab_conf_admblock5_taluka AS tal ON tal.taluka_id = prop.taluka_id
            LEFT JOIN ngdrstab_conf_admblock6_circle AS cir ON cir.circle_id = prop.circle_id
            LEFT JOIN ngdrstab_conf_admblock7_village_mapping AS vill ON vill.village_id = prop.village_id
            LEFT JOIN ngdrstab_mst_location_levels_1_property AS dag
                   ON dag.level_1_id = prop.level1_id AND dag.village_id = prop.village_id
            LEFT JOIN ngdrstab_trn_parameter AS patta ON patta.property_id = prop.property_id
            WHERE prop.token_no = ?
        ", [$tokenNo]);

        $office = $this->buildOfficeName($tokenNo);

        $totalValAmount = 0;
        $properties = [];
        $i = 0;

        foreach ($rptPropDtl as $prpt) {
            $valId = $prpt->val_id;

            $valuationRow = $valId
                ? DB::selectOne("SELECT rounded_val_amt FROM ngdrstab_trn_valuation WHERE val_id = ?", [$valId])
                : null;

            $totalValAmount += $valuationRow->rounded_val_amt ?? 0;

            $zonalClassRow = $valId
                ? DB::selectOne("
                    SELECT item_value_string
                    FROM ngdrstab_trn_valuation_details
                    WHERE val_id = ? AND item_id = 10
                ", [$valId])
                : null;

            $tmpPropArea = $valId ? $this->buildPropertyAreaLines($valId) : [];
// dd($tmpPropArea);
            if (count($location) === 1) {
                $locationRow = (array) $location[0];
            } else {
                if ($articleId === 46) {
                    $i = 0;
                }
                $locationRow = isset($location[$i]) ? (array) $location[$i] : [];
                $i++;
            }

            $properties[] = [
                'area'                 => $tmpPropArea,
                'location'             => $locationRow,
                'office'               => $office,
                'East'                 => $prpt->boundries_east_en,
                'West'                 => $prpt->boundries_west_en,
                'North'                => $prpt->boundries_north_en,
                'South'                => $prpt->boundries_south_en,
                'police_station'       => $prpt->police_station,
                'post_office'          => $prpt->post_office,
                'pincode'              => $prpt->pincode,
                'property_description' => $prpt->property_description,
                'zonalClass'           => $zonalClassRow->item_value_string ?? null,
                'property_group'       => $prpt->property_group,
            ];
        }

        $this->lastTotalValuationAmount = $totalValAmount;

        return $properties;
    }

    /** NOTE (needs verification) — see docblock above. */
    private function buildPropertyAreaLines(int $valId): array
    {
       $rows = DB::select("
        SELECT DISTINCT vd.item_id, vd.item_value, vd.area_unit, vd.area_type, vd.rule_id,
               item.usage_param_desc_en, item.is_list_field_flag,
               unit.unit_desc_en,
               list.item_desc_en AS list_item_desc_en
        FROM ngdrstab_trn_valuation_details vd
        LEFT JOIN ngdrstab_mst_usage_items_list item ON item.usage_param_id = vd.item_id
        LEFT JOIN ngdrstab_mst_unit unit ON unit.unit_id = vd.area_unit
        LEFT JOIN ngdrstab_conf_list_items list
               ON list.item_id = vd.item_id AND list.item_desc_id = CAST(vd.item_value AS numeric)
        WHERE vd.val_id = ? AND vd.item_type_id = 1
    ", [$valId]);

    // dd($rows);

        $lines = [];

        foreach ($rows as $row) {
            $itemValue = $row->is_list_field_flag === 'Y'
                ? $row->list_item_desc_en
                : $row->item_value;

            if ($itemValue > 0 && $row->area_unit != 0) {
                $lines[] = $row->usage_param_desc_en . ' : ' . $itemValue . ' ' . $row->unit_desc_en;
            }
        }

        return $lines;
    }

    // ---------------------------------------------------------------
    // EVERYTHING BELOW IS A DIRECT PORT — EXACT SQL WAS GIVEN
    // ---------------------------------------------------------------

    private function buildParty(string $tokenNo): array
    {
        $rows = DB::select("
            SELECT faith.faith_name_en, dist.district_name_en, taluka.taluka_name_en,
                   village.village_name_en, occ.occupation_name_en, party_type_id,
                   party_catg_id, gender_id, party_full_name_en, property_group,
                   father_full_name_en, age, address_en, pan_no, police_station_name,
                   post_office_name, p.pin_code, mobile_no
            FROM ngdrstab_trn_party_entry_new AS p
            LEFT JOIN ngdrstab_mst_occupation occ ON occ.occupation_id = p.occupation_id
            LEFT JOIN ngdrstab_conf_admblock5_taluka taluka ON taluka.taluka_id = p.taluka_id
            LEFT JOIN ngdrstab_conf_admblock3_district dist ON dist.district_id = p.district_id
            LEFT JOIN ngdrstab_conf_admblock7_village_mapping village ON village.village_id = p.village_id
            LEFT JOIN ngdrstab_mst_faith faith ON faith.faith_id = p.faith_id
            WHERE p.token_no = ?
        ", [$tokenNo]);

        return $this->wrapCakeStyle($rows);
    }

    private function buildTransactionDetails(string $tokenNo): array|string
    {
        $rows = DB::select(
            "SELECT * FROM ngdrstab_trn_e_deed_party_transaction_details WHERE token_no = ?",
            [$tokenNo]
        );

        return count($rows) > 0 ? $this->wrapCakeStyle($rows) : 'NA';
    }

    private function buildWitness(string $tokenNo): array
    {
        return $this->wrapCakeStyle(
            DB::select("SELECT * FROM ngdrstab_trn_witness WHERE token_no = ?", [$tokenNo])
        );
    }

    private function buildIdentifier(string $tokenNo): array
    {
        return $this->wrapCakeStyle(
            DB::select("SELECT identification_full_name_en FROM ngdrstab_trn_identification WHERE token_no = ?", [$tokenNo])
        );
    }

    private function buildConsAmt(string $tokenNo): array
    {
        return $this->wrapCakeStyle(
            DB::select("SELECT cons_amt FROM ngdrstab_trn_fee_calculation WHERE token_no = ?", [$tokenNo])
        );
    }

    private function buildStampDuty(string $tokenNo): array
    {
        return $this->wrapCakeStyle(DB::select("
            SELECT cal.final_value
            FROM ngdrstab_trn_fee_calculation AS fee
            JOIN ngdrstab_trn_fee_calculation_detail cal ON cal.fee_calc_id = fee.fee_calc_id
            WHERE fee.token_no = ? AND cal.fee_item_id = 2
        ", [$tokenNo]));
    }

    /** Original unwraps [0][0] at assignment time — this stays flat, not wrapped. */
    private function buildOfficeName(string $tokenNo): array
    {
        $rows = DB::select("
            SELECT off.office_name_en
            FROM ngdrstab_trn_generalinformation AS gen
            JOIN ngdrstab_mst_office off ON off.office_id = gen.office_id
            WHERE gen.token_no = ?
        ", [$tokenNo]);

        return $rows ? (array) $rows[0] : [];
    }

    private function buildNoc(string $tokenNo): string
    {
        $rows = DB::select("SELECT * FROM ngdrstab_trn_noc_landdetails WHERE token_no = ?", [$tokenNo]);

        return count($rows) > 0 ? ($rows[0]->appno ?? 'NA') : 'NA';
    }

    private function buildFeeItemValue(string $tokenNo, int $feeItemId): array
    {
        return $this->wrapCakeStyle(DB::select("
            SELECT fee_detail.fee_item_value
            FROM ngdrstab_trn_fee_calculation AS fee
            JOIN ngdrstab_trn_fee_calculation_detail AS fee_detail ON fee_detail.fee_calc_id = fee.fee_calc_id
            WHERE fee.token_no = ? AND fee_detail.fee_item_id = ?
        ", [$tokenNo, $feeItemId]));
    }

    /**
     * Kept for parity only. In the original, every EDeedTemplate method
     * that reads `additional_data` immediately overwrites it with
     * `additional_data1` before ever using the value — it appears dead.
     */
    private function buildAdditionalData(string $tokenNo): array
    {
        $row = DB::table('ngdrstab_trn_e_deed_party_transaction_details')
            ->where('token_no', $tokenNo)
            ->first();

        return $row ? [0 => (array) $row] : [];
    }

    private function buildAdditionalData1(string $tokenNo): array
    {
        return $this->wrapCakeStyle(
            DB::select("SELECT * FROM ngdrstab_trn_deed_texts WHERE token_no = ?", [$tokenNo])
        );
    }
}