<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Formlabel extends Model
{
    // protected $connection = 'ngprs';
    protected $table = 'ngdrstab_mst_formlabels';
    protected $primaryKey = 'labelname';
    public $incrementing = false;
    public $timestamps = false;

    /**
     * Equivalent of CakePHP updatepo()
     */
    public static function updatePo()
    {
        // Fetch all records (same as $this->find('all'))
        $query = self::all();

        $englbl = '';
        $mrlbl = '';
        $gujratilbl = '';

        foreach ($query as $qry) {

            $lbls = explode('_ll', $qry->labelname);

            $ll_activation_flag  = $qry->ll_activation_flag;
            $ll1_activation_flag = $qry->ll1_activation_flag;
            $ll2_activation_flag = $qry->ll2_activation_flag; // kept for parity

            // ---------- English ----------
            $englbl .=
                'msgid "' . $qry->labelname . "\" \n" .
                'msgstr "' . $qry->label_desc_en . "\" \n\n";

            // ---------- LL (Marathi / Local Language) ----------
            if ($ll_activation_flag === 'Y') {
                if ($qry->label_desc_ll === '' || $qry->label_desc_ll === null) {
                    $mrlbl .=
                        'msgid "' . $qry->labelname . "\" \n" .
                        'msgstr "' . $qry->label_desc_en . "\" \n\n";
                } else {
                    $mrlbl .=
                        'msgid "' . $qry->labelname . "\" \n" .
                        'msgstr "' . $qry->label_desc_ll . "\" \n\n";
                }
            }

            if ($ll_activation_flag === 'N') {
                $mrlbl .=
                    'msgid "' . $qry->labelname . "\" \n" .
                    'msgstr "' . $qry->label_desc_en . "\" \n\n";
            }

            // ---------- LL1 (Gujarati / Second Local Language) ----------
            if ($ll1_activation_flag === 'Y') {
                if ($qry->label_desc_ll1 === '' || $qry->label_desc_ll1 === null) {
                    $gujratilbl .=
                        'msgid "' . $lbls[0] . "\" \n" .
                        'msgstr "' . $qry->label_desc_en . "\" \n\n";
                } else {
                    $gujratilbl .=
                        'msgid "' . $lbls[0] . "\" \n" .
                        'msgstr "' . $qry->label_desc_ll1 . "\" \n\n";
                }
            }
        }

        // ---------- File Paths (Laravel equivalent of APP . 'Locale') ----------
        $basePath = resource_path('lang');

        $engPath = $basePath . '/eng/LC_MESSAGES';
        $llPath  = $basePath . '/ll/LC_MESSAGES';
        $ll1Path = $basePath . '/ll1/LC_MESSAGES';

        // Ensure directories exist
        if (!is_dir($engPath)) mkdir($engPath, 0777, true);
        if (!is_dir($llPath))  mkdir($llPath, 0777, true);
        if (!is_dir($ll1Path)) mkdir($ll1Path, 0777, true);

        // ---------- Write PO files ----------
        file_put_contents($engPath . '/default.po', $englbl);
        file_put_contents($llPath . '/default.po', $mrlbl);
        file_put_contents($ll1Path . '/default.po', $gujratilbl);

        return true;
    }
}
