<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Articles extends Model
{
    // protected $table = 'ngdrstab_mst_article';

    // protected $primaryKey = 'article_id';


    // protected $guarded = [];

    // public static function getArticleOptions()
    // {
    //     return self::where('display_flag', 'Y')
    //         ->orderBy('article_id')
    //         ->pluck('article_desc_en', 'article_id')
    //         ->toArray();
    // }
    protected $table = 'ngdrstab_mst_article';
    protected $primaryKey = 'article_id';
    public $timestamps = false;

    protected $guarded = [];

    public static function getArticleOptions($docLang, $efile = null, $roleType = null, $eregLL = null)
    {

        // CASE 1 (efile or bank role)
        if ($efile == 'Y' || $roleType == 'B') {

            return self::where('article_id', '<', 9000)
                ->where('display_flag', 'Y')
                ->where('e_file_applicable', 'Y')
                ->orderBy('display_order')
                ->pluck("article_desc_$docLang", 'article_id')
                ->toArray();
        }

        // CASE 2 (ereg_ll)
        if ($eregLL == 'Y') {

            return self::where('article_id', 70)
                ->orderBy('display_order')
                ->pluck("article_desc_$docLang", 'article_id')
                ->toArray();
        }

        // CASE 3 (default logic)
        // $articles = self::orderBy('display_order',)->get();
        $articles = self::where('display_flag', 'Y')
            ->orderBy('article_desc_en', 'asc')
            ->get();
        // dd($articles, $articles1);

        $result = [];

        foreach ($articles as $article) {

            if ($docLang == 'll' && $article->article_ll_activation_flag == 'Y') {
                $result[$article->article_id] = $article->article_desc_ll;
            } else {
                $result[$article->article_id] = $article->article_desc_en;
            }
        }


        return $result;
    }
}
