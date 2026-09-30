<?php

namespace App\Http\Controllers\Web\Application;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ArticleDetailDescription;
use App\Models\Articles;
use App\Models\ConfDistrict;
use App\Services\Module\GeneralInfo\GeneralInfoService;


class GeneralInfoController extends Controller
{
    protected $service;

    public function __construct(GeneralInfoService $service)
    {
        $this->service = $service;
    }
    public function index()
    {
        // session(['noc_number' => 'NOC/24/66469/2026']);

        $nocData = [];

        if ($nocNumber = session('noc_number')) {
            $nocData = $this->service->fetchNocData($nocNumber);
        }

        // For now, just dump the response to verify
        // dd($nocData);
        $isNocFlow = !empty($nocData);
        // dd($isNocFlow);
        // $article = Articles::getArticleOptions();
        $docLang = session('sess_langauge', 'en');
        $efile = session('efile', 'N');
        $roleType = session('role_type', null);
        $eregLL = session('ereg_ll', 'N');
        $district = ConfDistrict::getAllDistrict();
        // dd($district);

        $article = Articles::getArticleOptions(
            $docLang,
            $efile,
            $roleType,
            $eregLL
        );


        // --- NEW LOGIC START ---

        // 1. Retrieve the Bank Transaction ID from the SSO Payload
        $bankTransId = session('sso_payload.bank_trans_id');

        // 2. Retrieve the User Type (either from login_type or payload)
        $userType = session('sso_payload.user_type');

        // 3. Filter Articles for Bank Users
        // if ($userType === 'B') {
        //     // Assuming $article is an Eloquent Collection, we filter it.
        //     // Replace 'article_id' with the actual primary key name of your Articles model (e.g. 'id')
        //     $article = $article->whereIn('article_id', [51, 40, 92]);
        // }

        // Filter Articles for Bank Users
        if ($userType === 'B') {
            $allowedArticles = [51, 40, 92];

            $article = array_intersect_key(
                $article,
                array_flip($allowedArticles)
            );
        }
        // --- NEW LOGIC END ---

        // dd($article);
        return view('pages.generalinfo', compact("article", "district", "nocData", "isNocFlow"));
    }
    // public function getTitle($articleId)
    // {
    //     if (!is_numeric($articleId)) {
    //         return response()->json([
    //             'title' => null
    //         ], 400);
    //     }

    //     $title = ArticleDetailDescription::where('article_id', $articleId)
    //         ->value('articledescription_en');

    //     return response()->json([
    //         'title' => $title
    //     ]);
    // }  

    // public function store(Request $request): JsonResponse
    // {
    //     try {
    //         $validated = $request->validate(
    //             [
    //                 'article' => 'required',
    //                 'district' => 'required',
    //                 'circle' => 'required',
    //                 'office_name' => 'required',

    //                 'entry_date_in_india' => 'required_if:document_execution_type,2',
    //                 'court_order_date' => 'required_if:document_execution_type,3',
    //                 'court_order_number' => 'required_if:document_execution_type,3',
    //             ],
    //             [
    //                 'article.required' => 'Please select an article.',
    //                 'district.required' => 'Please select a district.',
    //                 'circle.required' => 'Please select a circle.',
    //                 'office_name.required' => 'Please select an office.',
    //             ]
    //         );

    //         $result = $this->service->save($request->all());
    //         session(['token_no' => $result->token_no]);

    //         return response()->json([
    //             'success' => true,
    //             'data' => $result
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function store(Request $request)
    {
        $request->validate(
            [
                'article' => 'required',
                'district' => 'required',
                'circle' => 'required',
                'office_name' => 'required',

                'entry_date_in_india' => 'required_if:document_execution_type,2',
                'court_order_date' => 'required_if:document_execution_type,3',
                'court_order_number' => 'required_if:document_execution_type,3',
            ],
            [
                'article.required' => 'Please select an article.',
                'district.required' => 'Please select a district.',
                'circle.required' => 'Please select a circle.',
                'office_name.required' => 'Please select an office.',
            ]
        );
// dd($request->all());
        try {
            $result = $this->service->save($request->all());

            session(['token_no' => $result->token_no]);

            // Redirect to next page
            return redirect()->route('property');
        } catch (\Exception $e) {

            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }
}
