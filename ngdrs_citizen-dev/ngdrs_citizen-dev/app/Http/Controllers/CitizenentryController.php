<?php

namespace App\Http\Controllers;

use App\Models\CitizenUser;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class CitizenentryController extends Controller
{
    /**
     * POST /api/asmsso
     * Accepts a POST param "data" (encrypted string), decrypts, stores/updates users,
     * inserts a single-window record and performs login.
     */
    public function delete_session()
    {
        // Clear session values (Cake: Session->write(key, null))
  
        Session::put('Selectedtoken', null);
        Session::put('doc_lang', null);
        Session::put('article_id', null);
        // echo 'hihii' ;exit;
        // Read application type
        $application_type = Session::get('rtps_process');

        // Read csrf token (Cake: $this->Session->read('csrftoken'))
        $csrftoken = Session::get('csrftoken');
        // dd($application_type);exit;
        // Redirect logic (EXACT match)
        if ($application_type === 'I') {
            return redirect()->action(
                [self::class, 'genernal_info'],
                ['csrftoken' => $csrftoken]
            );
        } else {
            return redirect()->action(
                [self::class, 'genernalinfoentry'],
                ['csrftoken' => $csrftoken]
            );
        }
    }

    public function genernalinfoentry(Request $request, $csrftoken = null, $flag = '')
    {
        try {

            /* ================= Cake: Session init ================= */
            Session::put('partyCount', 0);

            $referer = url()->previous();
            if ($referer && $referer !== url('/')) {
                if (!str_contains($referer, url('/'))) {
                    return response()->file(public_path('error.html'));
                }
            }

            if (Session::get('session_usertype') === 'C') {
                Session::put('manual_flag', 'N');
            }
 
            $sessionUserId = Session::get('session_user_id');
 
            $actualUserId = substr($sessionUserId, 4, -4);
         
            // fetch logged-in citizen (EXACTLY ONE row)
            $user = CitizenUser::where('user_id', $actualUserId)->first();
            
           
            if (!$user) {
                abort(401, 'Citizen user not found');
            }
            Auth::login($user);
           
            Session::put('user_role_id', Auth::user()->role_id);
            Session::put('citizen_user_id', Auth::user()->user_id);
           
            Session::put('sroidetifier', 'N');
            Session::put('sroparty', 'N');
            Session::put('ereg_flag', 'N');

            /* ================= Reschedule ================= */
            if (Session::get('reschedule_flag') === 'Y') {
                return redirect()->action(
                    [self::class, 'appointment'],
                    Session::get('csrftoken')
                );
            }

            /* ================= Load common fields ================= */
            $fields = $this->set_common_fields();

            /* ================= Regconfig ================= */
            if (Session::get('deed_writer') === 'Y') {
                $doc_count = $this->Regconfig->where('reginfo_id', 50)->first();
            } elseif (Session::get('is_advocate') === 'Y') {
                $doc_count = $this->Regconfig->where('reginfo_id', 62)->first();
            } else {
                $doc_count = $this->Regconfig->where('reginfo_id', 47)->first();
            }

            $village_flag = $this->Regconfig->where('reginfo_id', 81)->first();
            $mortgage_days_cfg = $this->Regconfig->where('reginfo_id', 206)->first();
            $mortgage_days = $mortgage_days_cfg
                ? '-' . $mortgage_days_cfg->info_value . 'd'
                : '+0d';

            /* ================= Token count check ================= */
            if (!is_numeric(Session::get('Selectedtoken')) && $doc_count) {
                $check_count = $this->Genernalinfoentry
                    ->where('user_id', $fields['user_id'])
                    ->where('last_status_id', '!=', 4)
                    ->count();

                if ($check_count >= $doc_count->info_value) {
                    Session::flash(
                        'message',
                        'Document count greater than ' . $doc_count->info_value
                    );
                    return redirect()->action([self::class, 'genernal_info']);
                }
            }

            /* ================= POST / PUT ================= */
            if ($request->isMethod('post') || $request->isMethod('put')) {

                $data = $request->input('genernalinfoentry');

                if (!empty($data['courtorderdate'])) {
                    $data['courtorderdate'] =
                        Carbon::parse($data['courtorderdate'])->format('Y-m-d');
                }

                if (!Session::get('role_type')) {
                    $data['is_uid_consent'] = 'Y';
                }

                /* ================= Validation ================= */
                $fieldlist = $this->ConfArticleFeeruleItems
                    ->fieldlist(Session::get('sess_langauge'), null, $data['article_id']);

                $errarr = $this->validatedata($data, $fieldlist);

                foreach ($errarr as $err) {
                    if ($err !== '') {
                        Session::put('errarr', $errarr);
                        File::put(
                            public_path('files/jsonfile_dfields_' . auth()->user()->user_id . '.json'),
                            json_encode($errarr)
                        );
                        return redirect()->back()->withInput();
                    }
                }

                /* ================= Token generation ================= */
                if (empty($data['general_info_id'])) {
                    $token = DB::selectOne('select * from generate_token_number()');
                    $data['token_no'] = $token->generate_token_number;
                }

                $data['user_type'] = Session::get('session_usertype');
                $data['rtps_trans_id'] = Session::get('sess_rtps_trans_id');

                $data = $this->istrim($data);

                /* ================= Save ================= */
                $this->Genernalinfoentry->updateOrCreate(
                    ['general_info_id' => $data['general_info_id'] ?? null],
                    $data
                );

                /* ================= Session updates ================= */
                Session::put('Selectedtoken', $data['token_no']);
                Session::put('article_id', $data['article_id']);
                Session::put('major_minor', $data['major_minor_flag']);
                Session::put('reg_type_id', $data['registration_type_id']);

                Session::flash('message', 'Saved Successfully');
                $this->set_csrf_token();

                return redirect()->action(
                    [self::class, 'property_details'],
                    Session::get('csrftoken')
                );
            }

            /* ================= GET ================= */
            $token = Session::get('Selectedtoken');

            if ($token) {
                $gen_info = $this->Genernalinfoentry
                    ->where('token_no', $token)
                    ->first();

                if ($gen_info) {
                    $request->merge(['genernalinfoentry' => $gen_info->toArray()]);
                }
            }

            return view('citizen.genernalinfoentry');
        } catch (\Throwable $ex) {
            report($ex);
            Session::flash(
                'message',
                'Record Cannot be displayed. Error: ' . $ex->getMessage()
            );
            // return redirect()->action([ErrorController::class, 'exception_occurred']);
        }
    }
}
