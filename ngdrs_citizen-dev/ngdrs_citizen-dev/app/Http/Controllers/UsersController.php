<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Formlabel;
use Exception;

class UsersController extends Controller
{
    /**
     * POST /api/asmsso
     * Accepts a POST param "data" (encrypted string), decrypts, stores/updates users,
     * inserts a single-window record and performs login.
     */
    public function welcome(Request $request, $master_role_id = null)
{
    try {

        // 1️⃣ Generate session_role_id
        if (!is_null($master_role_id)) {
            $roleid_random1 = rand(1000, 9999);
            $roleid_random2 = rand(1000, 9999);

            $session_roleid = $roleid_random1 . $master_role_id . $roleid_random2;

            Session::put('session_role_id', $session_roleid);
        }

        // 2️⃣ Set default language (same logic as CakePHP)
        if (Session::get('doc_lang') !== '' && Session::get('sess_langauge') !== '') {
            Session::put('doc_lang', 'en');
            Session::put('sess_langauge', 'en');
        }

        // 3️⃣ Update PO file labels
        Formlabel::updatePo();

        // 4️⃣ Session redirect flag
        Session::put('session_redirect', 'welcome');

        return response()->json([
            'status' => 'success'
        ]);

    } catch (Exception $e) {

        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
}

    
}
