<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PartyType extends Model
{
    protected $table = 'ngdrstab_mst_party_type';

    protected $primaryKey = 'party_type_id';

    public $timestamps = false;

    public static function get_party_typename($article_id)
    {
        // Common party types
        $partytype_name = DB::table('ngdrstab_mst_party_type')
            ->where('display_flag', 'C')
            ->pluck('party_type_desc_en', 'party_type_id')
            ->toArray();

        // Article specific party types
        $party = DB::table('ngdrstab_mst_party_type as partytype')
            ->join(
                'ngdrstab_mst_article_partytype_mapping as m',
                'partytype.party_type_id',
                '=',
                'm.party_type_id'
            )
            ->where('m.article_id', trim($article_id))
            ->pluck('partytype.party_type_desc_en', 'partytype.party_type_id')
            ->toArray();

        return $partytype_name + $party;
    }
}