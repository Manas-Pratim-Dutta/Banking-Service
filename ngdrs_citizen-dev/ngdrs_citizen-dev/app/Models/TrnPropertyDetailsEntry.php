<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrnPropertyDetailsEntry extends Model
{
    protected $table = 'ngdrstab_trn_property_details_entry';

    protected $guarded = [];

    const CREATED_AT = 'created';
    const UPDATED_AT = 'updated';

    /**
     * Retrieve property details and valuation information for a given token number.
     *
     * Fetches the valuation details associated with the properties under the
     * specified token number and maps each usage item's field_key to its
     * corresponding value. Properties are grouped by val_id, with DAG and Patta
     * numbers included in each property record.
     *
     * For land valuation, rounded_val_amt is used as the value. For string-based
     * usage items, item_value_string is used; otherwise, item_value is used.
     *
     * @param  string $tokenNo The token number associated with the properties.
     * @return \Illuminate\Support\Collection Collection of property details grouped by property.
     */
    public static function getPropertyDetails($tokenNo)
    {
        $details = self::from('ngdrstab_trn_property_details_entry as prop')
            ->join(
                'ngdrstab_trn_valuation_details as val',
                'val.val_id',
                '=',
                'prop.val_id'
            )
            ->join(
                'ngdrstab_mst_usage_items_list as item',
                'item.usage_param_id',
                '=',
                'val.item_id'
            )
            ->where('prop.token_no', $tokenNo)
            ->select(
                'prop.val_id',
                'prop.dag_no',
                'prop.patta_no',
                'item.field_key',
                'val.item_value',
                'val.item_value_string',
                'val.rounded_val_amt'
            )
            ->get();

        return $details
            ->groupBy('val_id')
            ->values()
            ->map(function ($property) {

                $first = $property->first();

                $result = [
                    'dag_no' => $first->dag_no,
                    'patta_no' => $first->patta_no,
                ];

                foreach ($property as $detail) {

                    if ($detail->field_key === 'land_valuation') {
                        $value = $detail->rounded_val_amt;
                    } elseif ($detail->item_value_string !== null) {
                        $value = $detail->item_value_string;
                    } else {
                        $value = $detail->item_value;
                    }

                    $result[$detail->field_key] = $value;
                }

                return $result; 
            });
    }
}
