<?php

namespace App\Http\Controllers\Web\Application;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PartyEntry;
use App\Models\Witness;
use App\Models\Identifier;
use App\Models\PartyType;
use App\Models\PartyCategory;
use App\Models\Articles;
use App\Models\EDeedPartyTransactionDetails;
use App\Models\EDeedDraft;

class EdeedController extends Controller
{
    public function index(Request $request)
    {
        try {

            $token = session('token_no');

            $articleId = \DB::table('ngdrstab_trn_generalinformation')
                ->where('token_no', $token)
                ->value('article_id');
            $article = Articles::where('article_id', $articleId)->first();

            if (!$article) {
                throw new \Exception('Article not found.');
            }

            // ------------------ PARTIES ------------------

            $partytype = PartyType::get_party_typename($articleId);

            $party_category = PartyCategory::get_party_category('en');

            $existingParties = PartyEntry::where(
                'token_no',
                $token
            )
                ->orderBy('id', 'asc')
                ->get();

            foreach ($existingParties as $party) {

                $party->party_type_name =
                    $partytype[$party->party_type_id]
                    ?? $party->party_type_id;

                $party->party_category_name =
                    $party_category[$party->party_catg_id]
                    ?? $party->party_catg_id;
            }


            // ------------------ WITNESSES ------------------

            $existingWitnesses = Witness::where(
                'token_no',
                $token
            )
                ->orderBy('id', 'asc')
                ->get();


            // ------------------ IDENTIFIERS ------------------

            $existingIdentifier = Identifier::where(
                'token_no',
                $token
            )
                ->orderBy('id', 'asc')
                ->get();
            $regenerate_flag = 'N';

            $draft = EDeedDraft::where('token_no', $token)->first();
            if (!empty($draft)) {

                $regenerate_flag = 'Y';

            }
            $existingLanguageType = $draft->language_type ?? null;
            $download_status = $draft->esign_status_presenter ?? null;
            $languageTypeToKey = [
                'E' => 'english',
                'A' => 'assamese',
                'B' => 'bengali',
            ];
            $existingLanguageKey = $languageTypeToKey[$existingLanguageType] ?? null;
            // ------------------ VIEW ------------------

            return view(
                'pages.edeed',
                compact(
                    'existingParties',
                    'existingWitnesses',
                    'existingIdentifier',
                    'article',
                    'existingLanguageType',
                    'existingLanguageKey',
                    'download_status',
                    'token',
                    'regenerate_flag'
                )
            );

        } catch (\Exception $e) {

            return back()->with(
                'error',
                $e->getMessage()
            );
        }
    }

    public function destroy()
    {
        $token = session('token_no');

        if (!$token) {
            return response()->json(['message' => 'No active session token found.'], 400);
        }

        $draft = EDeedDraft::where('token_no', $token)->first();

        if (!$draft) {
            return response()->json(['message' => 'Draft not found.'], 404);
        }

        $draft->delete();

        PartyEntry::where('token_no', $token)->update(['esign_status' => 'N']);
        Witness::where('token_no', $token)->update(['esign_status' => 'N']);
        Identifier::where('token_no', $token)->update(['esign_status' => 'N']);

        return response()->json(['message' => 'Draft deleted successfully.']);
    }
    public function download()
    {
        $token = session('token_no');
        ;

        $draft = EDeedDraft::where('token_no', $token)->firstOrFail();

        if (!$draft->file_path || !file_exists($draft->file_path)) {
            abort(404, 'E-deed file not found.');
        }

        return response()->download(
            $draft->file_path,
            basename($draft->file_path)
        );
    }

    public function articleFields(Request $request)
    {
        try {

            $token = session('token_no');
            ;

            $articleId = $request->input('article_id');
            $language = $request->input('language');

            if (!$articleId || !$language) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Article and language are required.'
                ], 422);
            }

            // ------------------ VIEW ------------------

            $languageSuffix = match ($language) {
                'english' => '',
                'assamese' => '_A',
                'bengali' => '_B',
                default => '',
            };

            $viewName = "elements.edeed.article_{$articleId}{$languageSuffix}";

            // ------------------ EXISTING PARTIES ------------------

            $existingParties = PartyEntry::where('token_no', $token)
                ->where('party_type_id', 9)
                ->orderBy('id', 'asc')
                ->get();

            // ------------------ EXISTING ARTICLE DETAILS ------------------

            $existingArticleDetails =
                EDeedPartyTransactionDetails::where('token_no', $token)
                    ->get();

            return response()->json([
                'status' => 'success',
                'html' => view($viewName, compact(
                    'existingParties',
                    'existingArticleDetails'
                ))->render()
            ]);

        } catch (\Throwable $e) {

            \Log::error('Article fields loading failed', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteArticleDetails(Request $request)
    {
        try {

            $token = session('token_no');
            ;

            $id = $request->input('id');

            if (empty($id)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Transaction ID is required.'
                ], 422);
            }

            /*
            |--------------------------------------------------------------------------
            | Delete only the record belonging to the current token
            |--------------------------------------------------------------------------
            */

            $transaction =
                EDeedPartyTransactionDetails::where(
                    'id',
                    $id
                )
                    ->where(
                        'token_no',
                        $token
                    )
                    ->first();

            if (!$transaction) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Transaction not found.'
                ], 404);
            }

            $transaction->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Transaction deleted successfully.',
                'id' => $id
            ]);

        } catch (\Throwable $e) {

            \Log::error(
                'E-Deed Article Details Delete Error',
                [
                    'message' => $e->getMessage(),
                    'line' => $e->getLine(),
                    'file' => $e->getFile()
                ]
            );

            return response()->json([
                'status' => 'error',
                'message' => 'Unable to delete transaction.'
            ], 500);
        }
    }
    public function saveArticleDetails(Request $request)
    {
        try {

            $token = session('token_no');
            ;

            $transaction = $request->input('transaction');

            if (!is_array($transaction)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid transaction data.'
                ], 422);
            }

            $allowedFields = (new EDeedPartyTransactionDetails())
                ->getFillable();

            $protectedFields = [
                'id',
                'token_no',
                'created_at',
                'updated_at'
            ];

            $allowedFields = array_diff(
                $allowedFields,
                $protectedFields
            );

            $data = collect($transaction)
                ->only($allowedFields)
                ->toArray();

            $data['token_no'] = $token;

            $saved = EDeedPartyTransactionDetails::create($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Article detail saved successfully.',
                'transaction' => $saved->fresh()
            ]);

        } catch (\Throwable $e) {

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function startEsign(Request $request)
    {
        $partyId = $request->party_id;
        $deedHtml = $request->deed_html;

        Session::put('edeed_html', $deedHtml);
        Session::put('party_id', $partyId);

        /*
         * Later:
         * Generate PDF
         * Save PDF
         * Call CakePHP Esign
         */

        return response()->json([
            'success' => true,

            'esign_url' =>
                url('/edeed/esign/' . $partyId)
        ]);
    }

    public function esign($partyId)
    {
        return view(
            'edeed.esign',
            compact('partyId')
        );
    }
}