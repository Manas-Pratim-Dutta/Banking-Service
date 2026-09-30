<?php

namespace App\Services\Actions\Esign;

use DOMDocument;
use Exception;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use App\Models\EDeedDraft;
use App\Models\EsignSessionState;

use App\Libraries\CustomTcpdf;
use App\Services\Actions\Esign\PdfSignerService;

use RobRichards\XMLSecLibs\XMLSecurityKey;
use RobRichards\XMLSecLibs\XMLSecurityDSig;


class EsignService
{
    /**
     * Start eSign process.
     *
     * Flow:
     *
     * 1. Get token.
     * 2. Find existing signed/current PDF from ngdrstab_trn_e_deed_draft.
     * 3. If no PDF exists, generate the unsigned deed PDF.
     * 4. Save Laravel session against token.
     * 5. Prepare eSign request.
     * 6. Redirect to eSign.
     */
    public function indexNew(
        Request $request
    ) {
        return view(
            'pages.index',
            [
                'signXML' =>
                    $request->eSignRequest,

                'txn' =>
                    $request->aspTxnID,
            ]
        );
    }
    public function start(Request $request)
    {
        try {

            /*
             * ---------------------------------------------------------
             * TOKEN
             * ---------------------------------------------------------
             */
            $token = session('token_no');
            // $token = trim(
            //     $request->input(
            //         'token_no',
            //         session('reg_token', '202500021456')
            //     )
            // );

            if (empty($token)) {
                throw new Exception(
                    'Registration token is missing.'
                );
            }


            /*
             * --------------------------------------------------------
             * SIGNER INFORMATION
             * --------------------------------------------------------
             */

            $partyId = $request->input(
                'party_id'
            );

            $dbId = $request->input(
                'db_id',
                $partyId
            );

            $partyType = strtolower(
                trim(
                    $request->input(
                        'party_type',
                        'party'
                    )
                )
            );

            $partyCategory = $request->input(
                'party_category'
            );

            $partyName = $request->input(
                'party_name'
            );
            $languageType = $request->input('language_type');
            // dd($languageType);
            /*
             * ---------------------------------------------------------
             * DEED HTML
             * ---------------------------------------------------------
             */

            $deedHtml = $request->input(
                'deed_html'
            );


            /*
             * ---------------------------------------------------------
             * FIND CURRENT DRAFT
             * ---------------------------------------------------------
             */

            $draft = EDeedDraft::where(
                'token_no',
                $token
            )->first();


            /*
             * ---------------------------------------------------------
             * DETERMINE PDF TO SIGN
             *
             * IMPORTANT:
             *
             * If a previous party has already signed, use the PDF
             * stored in ngdrstab_trn_e_deed_draft.file_path.
             *
             * Only generate a new PDF when no current PDF exists.
             * ---------------------------------------------------------
             */

            $pdfPath = null;


            if (
                $draft &&
                !empty($draft->file_path) &&
                file_exists($draft->file_path)
            ) {

                $pdfPath = $draft->file_path;

            } elseif (
                $draft &&
                !empty($draft->file_path)
            ) {

                /*
                 * file_path may be stored as a storage-relative path.
                 */

                $possiblePath = storage_path(
                    'app/' . ltrim(
                        $draft->file_path,
                        '/'
                    )
                );

                if (file_exists($possiblePath)) {

                    $pdfPath = $possiblePath;

                } else {

                    $possiblePublicPath = public_path(
                        ltrim(
                            $draft->file_path,
                            '/'
                        )
                    );

                    if (file_exists($possiblePublicPath)) {

                        $pdfPath = $possiblePublicPath;
                    }
                }
            }


            /*
             * ---------------------------------------------------------
             * IF NO EXISTING PDF -> GENERATE NEW PDF
             * ---------------------------------------------------------
             */

            if (empty($pdfPath)) {

                if (empty($deedHtml)) {

                    throw new Exception(
                        'Deed HTML is required because no existing draft PDF was found.'
                    );
                }

                $pdfPath = $this->generateUnsignedPdf(
                    $deedHtml,
                    $token
                );


                /*
                 * -----------------------------------------------------
                 * CREATE / UPDATE DRAFT
                 * -----------------------------------------------------
                 */

                $draft = EDeedDraft::updateOrCreate(
                    [
                        'token_no' => $token
                    ],
                    [
                        'file_path' => $pdfPath,
                        'language_type' => $languageType
                    ]
                );
            }


            /*
             * ---------------------------------------------------------
             * VERIFY PDF
             * ---------------------------------------------------------
             */

            if (
                !file_exists($pdfPath)
            ) {

                throw new Exception(
                    'PDF file does not exist: ' . $pdfPath
                );
            }

            $currentPdfContent = file_get_contents($pdfPath);
            $signatureIndex = substr_count($currentPdfContent, '/Type /Sig');

            $signatureWidth = 180;
            $signatureHeight = 40;
            $signatureX = 15;
            $signatureY = 15 + ($signatureIndex * ($signatureHeight + 10));

            $fieldName = 'Signature_' . $partyType . '_' . $dbId . '_' . time();

            $preparedPdf = storage_path(
                'app/public/edeed/' . $token . '/prepared_' . $fieldName . '.pdf'
            );

            $pdfSigner = app(PdfSignerService::class);
            $prepared = $pdfSigner->prepareIncrementalSignature(
                $pdfPath,
                $preparedPdf,
                $fieldName,
                1,
                $signatureX,
                $signatureY,
                $signatureWidth,
                $signatureHeight,
                (string) $partyName,
                now()->format('Y-m-d H:i:s')
            );


            /*
             * ---------------------------------------------------------
             * STORE CURRENT LARAVEL SESSION
             *
             * THIS IS THE PART THAT WAS MISSING PREVIOUSLY.
             *
             * The eSign flow leaves our application and comes back
             * through the response URL.
             *
             * Therefore we store the complete Laravel session against
             * the registration token.
             * ---------------------------------------------------------
             */

            // $sessionData = serialize(
            //     session()->all()
            // );


            // EsignSessionState::updateOrCreate(
            //     [
            //         'token_no' => $token
            //     ],
            //     [
            //         'user_id' => Auth::id(),
            //         'session_data' => $sessionData
            //     ]
            // );


            // /*
            //  * ---------------------------------------------------------
            //  * SAVE SIGNER INFORMATION IN CACHE
            //  *
            //  * The callback needs to know which database record
            //  * requested this eSign transaction.
            //  * ---------------------------------------------------------
            //  */

            // // return $this->sendEsignRequest(
            // //     $pdfPath,
            // //     $token,
            // //     [
            // //         'party_id' => $partyId,
            // //         'db_id' => $dbId,
            // //         'party_type' => $partyType,
            // //         'party_category' => $partyCategory,
            // //         'party_name' => $partyName,
            // //         'language_type' => $languageType
            // //     ]
            // // );
            // return $this->sendEsignRequest(
            //     $prepared['path'],
            //     $prepared['byte_range'],
            //     $token,
            //     [
            //         'party_id' => $partyId,
            //         'db_id' => $dbId,
            //         'party_type' => $partyType,
            //         'party_category' => $partyCategory,
            //         'party_name' => $partyName,
            //         'language_type' => $languageType
            //     ]
            // );
            $sessionData = serialize(
                session()->all()
            );


            EsignSessionState::updateOrCreate(
                [
                    'token_no' => $token
                ],
                [
                    'user_id' => Auth::id(),
                    'session_data' => $sessionData
                ]
            );


            /*
             * ---------------------------------------------------------
             * PREPARE A NEW INCREMENTAL SIGNATURE PLACEHOLDER FOR THIS
             * SIGNER, WITHOUT TOUCHING ANY PREVIOUS SIGNER'S BYTES.
             * ---------------------------------------------------------
             */

            $currentPdfContent = file_get_contents($pdfPath);
            $signatureIndex = substr_count($currentPdfContent, '/Type /Sig');

            $signatureWidth = 180;
            $signatureHeight = 40;
            $signatureX = 15;
            $signatureY = 15 + ($signatureIndex * ($signatureHeight + 10));

            $fieldName = 'Signature_' . $partyType . '_' . $dbId . '_' . time();

            $preparedPdf = storage_path(
                'app/public/edeed/' . $token . '/prepared_' . $fieldName . '.pdf'
            );

            $pdfSigner = app(PdfSignerService::class);

            $prepared = $pdfSigner->prepareIncrementalSignature(
                $pdfPath,
                $preparedPdf,
                $fieldName,
                1,
                $signatureX,
                $signatureY,
                $signatureWidth,
                $signatureHeight,
                (string) $partyName,
                now()->format('Y-m-d H:i:s')
            );


            /*
             * ---------------------------------------------------------
             * SAVE SIGNER INFORMATION IN CACHE
             *
             * The callback needs to know which database record
             * requested this eSign transaction.
             * ---------------------------------------------------------
             */

            return $this->sendEsignRequest(
                $prepared['path'],
                $prepared['byte_range'],
                $token,
                [
                    'party_id' => $partyId,
                    'db_id' => $dbId,
                    'party_type' => $partyType,
                    'party_category' => $partyCategory,
                    'party_name' => $partyName,
                    'language_type' => $languageType
                ]
            );

        } catch (Exception $e) {

            report($e);

            return response()->json(
                [
                    'status' => 'error',
                    'message' => $e->getMessage()
                ],
                500
            );
        }
    }


    /**
     * Generate initial unsigned PDF.
     */
    // private function generateUnsignedPdf(
    //     string $deedHtml,
    //     string $token
    // ): string {

    //     /*
    //      * ---------------------------------------------------------
    //      * DIRECTORY
    //      * ---------------------------------------------------------
    //      */

    //     $directory = storage_path(
    //         'app/public/edeed/' . $token
    //     );


    //     if (
    //         !is_dir($directory)
    //     ) {

    //         mkdir(
    //             $directory,
    //             0775,
    //             true
    //         );
    //     }


    //     /*
    //      * ---------------------------------------------------------
    //      * FILE
    //      * ---------------------------------------------------------
    //      */

    //     $pdfPath = $directory . '/unsigned.pdf';


    //     /*
    //      * ---------------------------------------------------------
    //      * TCPDF
    //      * ---------------------------------------------------------
    //      */

    //     $pdf = new CustomTcpdf(
    //         PDF_PAGE_ORIENTATION,
    //         PDF_UNIT,
    //         PDF_PAGE_FORMAT,
    //         true,
    //         'UTF-8',
    //         false
    //     );


    //     $pdf->SetCreator(
    //         'NGDRS'
    //     );

    //     $pdf->SetAuthor(
    //         'NGDRS'
    //     );

    //     $pdf->SetTitle(
    //         'e-Deed'
    //     );

    //     $pdf->SetSubject(
    //         'e-Deed'
    //     );


    //     $pdf->SetMargins(
    //         15,
    //         15,
    //         15
    //     );


    //     $pdf->SetAutoPageBreak(
    //         true,
    //         15
    //     );


    //     /*
    //      * ---------------------------------------------------------
    //      * SIGNATURE PLACEHOLDER
    //      * ---------------------------------------------------------
    //      */

    //     $pdf->my_set_sign(
    //         '',
    //         '',
    //         '',
    //         '',
    //         2,
    //         [],
    //         ''
    //     );


    //     /*
    //      * ---------------------------------------------------------
    //      * PAGE
    //      * ---------------------------------------------------------
    //      */

    //     $pdf->AddPage();


    //     /*
    //      * ---------------------------------------------------------
    //      * WRITE DEED HTML
    //      * ---------------------------------------------------------
    //      */

    //     $pdf->writeHTML(
    //         $deedHtml,
    //         true,
    //         false,
    //         true,
    //         false,
    //         ''
    //     );


    //     /*
    //      * ---------------------------------------------------------
    //      * PREPARE FOR EXTERNAL SIGNING
    //      * ---------------------------------------------------------
    //      */

    //     $pdf->prepareForExternalSigning(
    //         $pdfPath
    //     );


    //     /*
    //      * ---------------------------------------------------------
    //      * VERIFY PDF
    //      * ---------------------------------------------------------
    //      */

    //     if (
    //         !file_exists($pdfPath)
    //     ) {

    //         throw new Exception(
    //             'Failed to create unsigned PDF.'
    //         );
    //     }


    //     $pdfContent = file_get_contents(
    //         $pdfPath
    //     );


    //     if (
    //         strpos(
    //             $pdfContent,
    //             '/Contents<'
    //         ) === false &&
    //         strpos(
    //             $pdfContent,
    //             '/Contents <'
    //         ) === false
    //     ) {

    //         throw new Exception(
    //             'PDF signature placeholder was not generated.'
    //         );
    //     }


    //     return $pdfPath;
    // }

    private function generateUnsignedPdf(
        string $deedHtml,
        string $token
    ): string {

        $directory = storage_path('app/public/edeed/' . $token);

        if (!is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        $pdfPath = $directory . '/base.pdf';

        $pdf = new CustomTcpdf(
            PDF_PAGE_ORIENTATION,
            PDF_UNIT,
            PDF_PAGE_FORMAT,
            true,
            'UTF-8',
            false
        );

        $pdf->SetCreator('NGDRS');
        $pdf->SetAuthor('NGDRS');
        $pdf->SetTitle('e-Deed');
        $pdf->SetSubject('e-Deed');
        $pdf->SetMargins(15, 15, 15);
        $pdf->SetAutoPageBreak(true, 15);

        $pdf->AddPage();
        $pdf->writeHTML($deedHtml, true, false, true, false, '');

        // Plain save. No /ByteRange or /Contents placeholder here —
        // each signer gets their own placeholder via
        // PdfSignerService::prepareIncrementalSignature().
        $pdf->Output($pdfPath, 'F');

        if (!file_exists($pdfPath)) {
            throw new Exception('Failed to create base PDF.');
        }

        return $pdfPath;
    }


    /**
     * Prepare and send eSign request.
     */
    // private function sendEsignRequest(
    //     string $pdfPath,
    //     string $token,
    //     array $signer
    // ) {

    //     /*
    //      * ---------------------------------------------------------
    //      * READ PDF
    //      * ---------------------------------------------------------
    //      */

    //     $pdfContent = file_get_contents(
    //         $pdfPath
    //     );


    //     if (
    //         $pdfContent === false
    //     ) {

    //         throw new Exception(
    //             'Unable to read PDF.'
    //         );
    //     }


    //     /*
    //      * ---------------------------------------------------------
    //      * GET PDF BYTE RANGE
    //      * ---------------------------------------------------------
    //      */

    //     $pdfSigner = app(
    //         PdfSignerService::class
    //     );

    //     $pdfContent = file_get_contents($pdfPath);

    //     $byteRange = $pdfSigner->getByteRange(
    //         $pdfContent
    //     );

    //     // if (
    //     //     !is_array($byteRange) ||
    //     //     count($byteRange) !== 4
    //     // ) {

    //     //     throw new Exception(
    //     //         'Invalid PDF ByteRange.'
    //     //     );
    //     // }


    //     /*
    //      * ---------------------------------------------------------
    //      * CREATE SIGNED DATA
    //      *
    //      * ByteRange format:
    //      *
    //      * [0, length1, offset2, length2]
    //      * ---------------------------------------------------------
    //      */

    //     $signedData = $pdfSigner->getSigningData(
    //         $pdfContent,
    //         $byteRange
    //     );


    //     /*
    //      * ---------------------------------------------------------
    //      * HASH
    //      * ---------------------------------------------------------
    //      */

    //     $fileHash = hash(
    //         'sha256',
    //         $signedData
    //     );


    //     /*
    //      * ---------------------------------------------------------
    //      * TRANSACTION
    //      * ---------------------------------------------------------
    //      */

    //     $txn = (string) random_int(
    //         111111111111,
    //         999999999999
    //     );


    //     $ts = date(
    //         'Y-m-d\TH:i:s'
    //     );


    //     /*
    //      * ---------------------------------------------------------
    //      * CREATE XML
    //      * ---------------------------------------------------------
    //      */

    //     $xml = $this->createSignedXml(
    //         $fileHash,
    //         $txn,
    //         $ts,
    //         $token
    //     );


    //     /*
    //      * ---------------------------------------------------------
    //      * CACHE TRANSACTION INFORMATION
    //      * ---------------------------------------------------------
    //      *
    //      * Store everything required by response().
    //      * ---------------------------------------------------------
    //      */

    //     cache()->put(
    //         'esign_txn_' . $txn,
    //         [
    //             'pdf_path' => $pdfPath,
    //             'token' => $token,

    //             'party_id' => $signer['party_id'] ?? null,
    //             'db_id' => $signer['db_id'] ?? null,
    //             'party_type' => $signer['party_type'] ?? null,
    //             'party_category' => $signer['party_category'] ?? null,
    //             'party_name' => $signer['party_name'] ?? null,
    //             'language_type' => $signer['language_type'] ?? null,
    //             'byte_range' => $byteRange
    //         ],
    //         now()->addHours(2)
    //     );


    //     /*
    //      * ---------------------------------------------------------
    //      * STORE TRANSACTION IN SESSION
    //      * ---------------------------------------------------------
    //      */

    //     session([
    //         'esign_xml' => $xml,
    //         'esign_txn' => $txn,
    //         'unsigned_pdf' => $pdfPath,
    //         'esign_token' => $token
    //     ]);


    //     /*
    //      * ---------------------------------------------------------
    //      * RETURN SUBMIT VIEW
    //      * ---------------------------------------------------------
    //      */

    //     return view(
    //         'pages.submit',
    //         [
    //             'signXML' => $xml,
    //             'txn' => $txn
    //         ]
    //     );
    // }

    // private function sendEsignRequest(
    //     string $preparedPdf,
    //     array $byteRange,
    //     string $token,
    //     array $signer
    // ) {
    //     $pdfContent = file_get_contents($preparedPdf);

    //     if ($pdfContent === false) {
    //         throw new Exception('Unable to read prepared PDF.');
    //     }

    //     $pdfSigner = app(PdfSignerService::class);

    //     $signedData = $pdfSigner->getSigningData($pdfContent, $byteRange);

    //     $fileHash = hash('sha256', $signedData);

    //     $txn = (string) random_int(111111111111, 999999999999);
    //     $ts = date('Y-m-d\TH:i:s');

    //     $xml = $this->createSignedXml($fileHash, $txn, $ts, $token);

    //     cache()->put(
    //         'esign_txn_' . $txn,
    //         [
    //             'pdf_path' => $preparedPdf,   // the INCREMENTAL-prepared file
    //             'token' => $token,
    //             'party_id' => $signer['party_id'] ?? null,
    //             'db_id' => $signer['db_id'] ?? null,
    //             'party_type' => $signer['party_type'] ?? null,
    //             'party_category' => $signer['party_category'] ?? null,
    //             'party_name' => $signer['party_name'] ?? null,
    //             'language_type' => $signer['language_type'] ?? null,
    //             'byte_range' => $byteRange
    //         ],
    //         now()->addHours(2)
    //     );

    //     session([
    //         'esign_xml' => $xml,
    //         'esign_txn' => $txn,
    //         'unsigned_pdf' => $preparedPdf,
    //         'esign_token' => $token
    //     ]);

    //     return view('pages.submit', [
    //         'signXML' => $xml,
    //         'txn' => $txn
    //     ]);
    // }
    private function sendEsignRequest(
        string $preparedPdf,
        array $byteRange,
        string $token,
        array $signer
    ) {

        $pdfContent = file_get_contents($preparedPdf);

        if ($pdfContent === false) {
            throw new Exception('Unable to read prepared PDF.');
        }

        $pdfSigner = app(PdfSignerService::class);

        $signedData = $pdfSigner->getSigningData($pdfContent, $byteRange);

        $fileHash = hash('sha256', $signedData);

        $txn = (string) random_int(111111111111, 999999999999);

        $ts = date('Y-m-d\TH:i:s');

        $xml = $this->createSignedXml($fileHash, $txn, $ts, $token);

        cache()->put(
            'esign_txn_' . $txn,
            [
                'pdf_path' => $preparedPdf,
                'token' => $token,
                'party_id' => $signer['party_id'] ?? null,
                'db_id' => $signer['db_id'] ?? null,
                'party_type' => $signer['party_type'] ?? null,
                'party_category' => $signer['party_category'] ?? null,
                'party_name' => $signer['party_name'] ?? null,
                'language_type' => $signer['language_type'] ?? null,
                'byte_range' => $byteRange
            ],
            now()->addHours(2)
        );

        session([
            'esign_xml' => $xml,
            'esign_txn' => $txn,
            'unsigned_pdf' => $preparedPdf,
            'esign_token' => $token
        ]);

        return view(
            'pages.submit',
            [
                'signXML' => $xml,
                'txn' => $txn
            ]
        );
    }


    /**
     * Create eSign XML.
     */
    private function createSignedXml(
        string $hash,
        string $txn,
        string $ts,
        string $token
    ): string {

        $doc = new DOMDocument();

        /*
         * IMPORTANT:
         *
         * responseUrl contains the token.
         *
         * Example:
         *
         * https://domain.com/esign/response?param=202500021456
         */

        $responseUrl =
            config(
                'esign.response_url'
            )
            .
            '?param='
            .
            urlencode($token);


        $xml = '<Esign AuthMode="1"
            aspId="' . htmlspecialchars(
            config('esign.asp_id'),
            ENT_QUOTES,
            'UTF-8'
        ) . '"
            ekycId=""
            ekycIdType="A"
            responseSigType="pkcs7"
            responseUrl="' . htmlspecialchars(
            $responseUrl,
            ENT_QUOTES,
            'UTF-8'
        ) . '"
            sc="y"
            ts="' . htmlspecialchars(
            $ts,
            ENT_QUOTES,
            'UTF-8'
        ) . '"
            txn="' . htmlspecialchars(
            $txn,
            ENT_QUOTES,
            'UTF-8'
        ) . '"
            ver="2.1">
            <Docs>
                <InputHash
                    docInfo="' . htmlspecialchars(
            $txn,
            ENT_QUOTES,
            'UTF-8'
        ) . '"
                    hashAlgorithm="SHA256"
                    id="1">' .
            htmlspecialchars(
                $hash,
                ENT_XML1,
                'UTF-8'
            ) .
            '</InputHash>
            </Docs>
        </Esign>';


        $doc->loadXML(
            $xml
        );


        return $this->signXml(
            $doc
        );
    }


    /**
     * Sign eSign request XML using ASP private key.
     */
    private function signXml(
        DOMDocument $doc
    ): string {

        $key = new XMLSecurityKey(
            XMLSecurityKey::RSA_SHA1,
            [
                'type' => 'private'
            ]
        );


        $key->loadKey(
            config(
                'esign.private_key'
            ),
            true
        );


        $dsig = new XMLSecurityDSig();


        $dsig->setCanonicalMethod(
            XMLSecurityDSig::C14N
        );


        $dsig->addReference(
            $doc,
            XMLSecurityDSig::SHA1,
            [
                'http://www.w3.org/2000/09/xmldsig#enveloped-signature'
            ],
            [
                'force_uri' => true
            ]
        );


        $dsig->sign(
            $key
        );


        $dsig->appendSignature(
            $doc->documentElement
        );


        return str_replace(
            '<?xml version="1.0"?>',
            '',
            $doc->saveXML()
        );
    }


    /**
     * eSign callback.
     *
     * This method:
     *
     * 1. Gets token from ?param=
     * 2. Gets transaction from response XML.
     * 3. Retrieves cached transaction data.
     * 4. Restores Laravel session.
     * 5. Applies PKCS#7 signature.
     * 6. Saves signed PDF to EDeedDraft.
     * 7. Updates individual signer status.
     * 8. Checks whether all signers have signed.
     * 9. Updates presenter status.
     * 10. Deletes temporary session state.
     * 11. Redirects to /edeed.
     */
    public function response(Request $request)
    {
        try {

            /*
             * ---------------------------------------------------------
             * TOKEN FROM responseUrl
             * ---------------------------------------------------------
             */

            $token = trim(
                $request->query(
                    'param'
                )
            );


            if (
                empty($token)
            ) {

                throw new Exception(
                    'eSign callback token is missing.'
                );
            }


            /*
             * ---------------------------------------------------------
             * XML
             * ---------------------------------------------------------
             */

            // $xmlString =
            //     $request->input(
            //         'EsignResponse'
            //     )
            //     ??
            //     $request->input(
            //         'esignResponse'
            //     )
            //     ??
            //     $request->getContent();
            $xmlString =
                $request->input('EsignResponse')
                ??
                $request->input('eSignResponse')
                ??
                $request->input('esignResponse')
                ??
                $request->getContent();


            if (
                empty($xmlString)
            ) {

                throw new Exception(
                    'eSign response XML is empty.'
                );
            }


            /*
             * ---------------------------------------------------------
             * PARSE XML
             * ---------------------------------------------------------
             */

            // dd([
            //     'content_type' => $request->header('Content-Type'),
            //     'all' => $request->all(),
            //     'raw' => $request->getContent(),
            //     'EsignResponse' => $request->input('EsignResponse'),
            //     'esignResponse' => $request->input('esignResponse'),
            // ]);

            $xml = simplexml_load_string(
                $xmlString
            );


            if (
                $xml === false
            ) {

                throw new Exception(
                    'Invalid eSign response XML.'
                );
            }

//                         dd([
//     'xmlString' => $xmlString,
//     'xml' => $xml->asXML(),
//     'children' => $xml->children(),
// ]);
            /*
             * ---------------------------------------------------------
             * STATUS
             * ---------------------------------------------------------
             */

            $status = (string) (
                $xml['status']
                ??
                $xml['Status']
            );


            if (
                strtolower($status) !== 'success' &&
                strtolower($status) !== 'y' &&
                strtolower($status) !== '1'
            ) {

                throw new Exception(
                    'eSign failed. Status: ' . $status
                );
            }


            /*
             * ---------------------------------------------------------
             * TRANSACTION
             * ---------------------------------------------------------
             */

            $txn = (string) (
                $xml['txn']
                ??
                $xml['Txn']
            );


            if (
                empty($txn)
            ) {

                /*
                 * Some providers return transaction information
                 * inside a child node.
                 */

                $txn = (string) (
                    $xml->txn
                    ??
                    $xml->Txn
                );
            }


            if (
                empty($txn)
            ) {

                throw new Exception(
                    'Transaction number is missing in eSign response.'
                );
            }


            /*
             * ---------------------------------------------------------
             * GET TRANSACTION CACHE
             * ---------------------------------------------------------
             */

            $cacheKey =
                'esign_txn_' . $txn;


            $meta = cache()->get(
                $cacheKey
            );


            if (
                empty($meta)
            ) {

                throw new Exception(
                    'eSign transaction information has expired or was not found.'
                );
            }
            $languageType = $meta['language_type'] ?? null;

            /*
             * ---------------------------------------------------------
             * VERIFY TOKEN
             * ---------------------------------------------------------
             */

            if (
                (string) ($meta['token'] ?? '')
                !==
                (string) $token
            ) {

                throw new Exception(
                    'eSign token mismatch.'
                );
            }


            /*
             * ---------------------------------------------------------
             * RESTORE SESSION
             *
             * THIS IS THE OTHER IMPORTANT SESSION PART.
             * ---------------------------------------------------------
             */

            $this->restoreSession(
                $token
            );


            /*
             * ---------------------------------------------------------
             * GET PKCS#7 SIGNATURE
             * ---------------------------------------------------------
             */

            // $pkcs7 = '';


            // if (
            //     isset($xml->DocSignature)
            // ) {

            //     $pkcs7 =
            //         trim(
            //             (string) $xml->DocSignature
            //         );
            // }


            // if (
            //     empty($pkcs7)
            // ) {

            //     /*
            //      * Try alternative response structure.
            //      */

            //     $pkcs7 =
            //         trim(
            //             (string) (
            //                 $xml->EsignResponse->DocSignature
            //                 ??
            //                 ''
            //             )
            //         );
            // }


            // if (
            //     empty($pkcs7)
            // ) {

            //     throw new Exception(
            //         'PKCS#7 signature is missing from eSign response.'
            //     );
            // }
            $pkcs7 = '';

            if (isset($xml->Signatures->DocSignature)) {
                $pkcs7 = trim(
                    (string) $xml->Signatures->DocSignature
                );
            }

            if (empty($pkcs7)) {
                throw new Exception(
                    'PKCS#7 signature is missing from eSign response.'
                );
            }

            /*
             * ---------------------------------------------------------
             * GET CERTIFICATE
             * ---------------------------------------------------------
             */

            $certificate = '';


            if (
                isset($xml->UserX509Certificate)
            ) {

                $certificate =
                    trim(
                        (string) $xml->UserX509Certificate
                    );
            }


            if (
                empty($certificate)
            ) {

                $certificate =
                    trim(
                        (string) (
                            $xml->Certificate
                            ??
                            ''
                        )
                    );
            }


            /*
             * ---------------------------------------------------------
             * PDF PATH
             * ---------------------------------------------------------
             */

            $preparedPdf =
                $meta['pdf_path'];


            if (
                empty($preparedPdf) ||
                !file_exists($preparedPdf)
            ) {

                throw new Exception(
                    'Prepared PDF not found.'
                );
            }


            /*
             * ---------------------------------------------------------
             * OUTPUT SIGNED PDF
             * ---------------------------------------------------------
             */

            $tokenDirectory =
                storage_path(
                    'app/public/edeed/' . $token
                );


            if (
                !is_dir($tokenDirectory)
            ) {

                mkdir(
                    $tokenDirectory,
                    0775,
                    true
                );
            }


            $signedPdf =
                $tokenDirectory
                .
                '/signed_' . $txn . '.pdf';


            /*
             * ---------------------------------------------------------
             * APPLY EXTERNAL SIGNATURE
             *
             * IMPORTANT:
             *
             * PdfSignerService currently expects its own ByteRange
             * argument. The byte range retrieved above is an array.
             *
             * We pass the first-length value only if that is what the
             * current PdfSignerService implementation expects.
             *
             * If your PdfSignerService expects the complete array,
             * change that method there accordingly.
             * ---------------------------------------------------------
             */

            $pdfSigner =
                app(
                    PdfSignerService::class
                );


            // $pdfSigner->applyExternalSignature(
            //     $preparedPdf,
            //     $signedPdf,
            //     $certificate,
            //     $pkcs7,
            //     $meta['byte_range']
            // );
            $pdfSigner->applyIncrementalSignature(
                $preparedPdf,   // = $meta['pdf_path'], the incremental-prepared file
                $signedPdf,
                $pkcs7
            );


            /*
             * ---------------------------------------------------------
             * VERIFY SIGNED PDF
             * ---------------------------------------------------------
             */

            if (
                !file_exists($signedPdf)
            ) {

                throw new Exception(
                    'Signed PDF was not created.'
                );
            }


            /*
             * ---------------------------------------------------------
             * UPDATE DRAFT
             *
             * FINAL PDF POINTER IS ONLY STORED HERE.
             * ---------------------------------------------------------
             */

            $draft =
                EDeedDraft::where(
                    'token_no',
                    $token
                )->first();


            // if (
            //     !$draft
            // ) {

            //     $draft =
            //         EDeedDraft::create(
            //             [
            //                 'token_no' => $token,
            //                 'file_path' => $signedPdf,
            //                 'language_type' => $languageType,
            //             ]
            //         );

            // } else {

            //     $draft->file_path =
            //         $signedPdf;

            //     $draft->save();
            // }
            if (
                !$draft
            ) {

                $draft =
                    EDeedDraft::create(
                        [
                            'token_no' => $token,
                            'file_path' => $signedPdf,
                            'language_type' => $languageType,
                        ]
                    );

            } else {

                $draft->file_path =
                    $signedPdf;

                if (!empty($languageType)) {
                    $draft->language_type = $languageType;
                }

                $draft->save();
            }


            /*
             * ---------------------------------------------------------
             * UPDATE INDIVIDUAL SIGNER STATUS
             * ---------------------------------------------------------
             */

            $this->updateSignerStatus(
                $token,
                $meta
            );


            /*
             * ---------------------------------------------------------
             * CHECK ALL SIGNERS
             * ---------------------------------------------------------
             */

            if (
                $this->allSignersSigned(
                    $token
                )
            ) {

                $draft->esign_status_presenter =
                    'Y';

                $draft->save();
            }


            /*
             * ---------------------------------------------------------
             * REMOVE TRANSACTION CACHE
             * ---------------------------------------------------------
             */

            cache()->forget(
                $cacheKey
            );


            /*
             * ---------------------------------------------------------
             * DELETE SAVED SESSION STATE
             *
             * Session has already been restored above.
             * ---------------------------------------------------------
             */

            EsignSessionState::where(
                'token_no',
                $token
            )->delete();


            /*
             * ---------------------------------------------------------
             * SAVE CURRENT PDF IN SESSION
             * ---------------------------------------------------------
             */

            session([
                'reg_token' => $token,
                'current_edeed_pdf' => $signedPdf,
                'esign_last_signed_txn' => $txn
            ]);


            /*
             * ---------------------------------------------------------
             * REDIRECT
             * ---------------------------------------------------------
             */

            return redirect(
                '/edeed'
            );

        } catch (Exception $e) {

            report($e);

            return response()->json(
                [
                    'status' => 'error',
                    'message' => $e->getMessage()
                ],
                500
            );
        }
    }


    /**
     * Restore Laravel session saved before eSign.
     */
    private function restoreSession(
        string $token
    ): void {

        $state =
            EsignSessionState::where(
                'token_no',
                $token
            )->first();


        if (
            !$state
        ) {

            return;
        }


        /*
         * ---------------------------------------------------------
         * RESTORE SESSION DATA
         * ---------------------------------------------------------
         */

        if (
            !empty($state->session_data)
        ) {

            $sessionData =
                unserialize(
                    $state->session_data
                );


            if (
                is_array($sessionData)
            ) {

                foreach (
                    $sessionData as $key => $value
                ) {

                    session([
                        $key => $value
                    ]);
                }
            }
        }


        /*
         * ---------------------------------------------------------
         * RESTORE AUTHENTICATED USER
         * ---------------------------------------------------------
         */

        if (
            !empty($state->user_id)
        ) {

            Auth::loginUsingId(
                $state->user_id
            );
        }


        /*
         * ---------------------------------------------------------
         * RESTORE TOKEN
         * ---------------------------------------------------------
         */

        session([
            'reg_token' => $token
        ]);
    }


    /**
     * Update individual signer eSign status.
     */
    private function updateSignerStatus(
        string $token,
        array $meta
    ): void {

        $dbId =
            $meta['db_id']
            ??
            $meta['party_id']
            ??
            null;


        if (
            empty($dbId)
        ) {

            throw new Exception(
                'Signer database ID is missing.'
            );
        }


        $partyType =
            strtolower(
                trim(
                    $meta['party_type']
                    ??
                    'party'
                )
            );


        /*
         * ---------------------------------------------------------
         * PARTY
         * ---------------------------------------------------------
         */

        if (
            in_array(
                $partyType,
                [
                    'party',
                    'parties',
                    'party_entry',
                    'party_entry_new'
                ],
                true
            )
        ) {

            $updated =
                DB::table(
                    'ngdrstab_trn_party_entry_new'
                )
                    ->where(
                        'id',
                        $dbId
                    )
                    ->where(
                        'token_no',
                        $token
                    )
                    ->update(
                        [
                            'esign_status' => 'Y'
                        ]
                    );


            if (
                $updated === 0
            ) {

                throw new Exception(
                    'Party signer record was not found.'
                );
            }


            return;
        }


        /*
         * ---------------------------------------------------------
         * WITNESS
         * ---------------------------------------------------------
         */

        if (
            in_array(
                $partyType,
                [
                    'witness',
                    'witnesses'
                ],
                true
            )
        ) {

            $updated =
                DB::table(
                    'ngdrstab_trn_witness'
                )
                    ->where(
                        'id',
                        $dbId
                    )
                    ->where(
                        'token_no',
                        $token
                    )
                    ->update(
                        [
                            'esign_status' => 'Y'
                        ]
                    );


            if (
                $updated === 0
            ) {

                throw new Exception(
                    'Witness signer record was not found.'
                );
            }


            return;
        }


        /*
         * ---------------------------------------------------------
         * IDENTIFIER
         * ---------------------------------------------------------
         */

        if (
            in_array(
                $partyType,
                [
                    'identifier',
                    'identifiers'
                ],
                true
            )
        ) {

            $updated =
                DB::table(
                    'ngdrstab_trn_identification'
                )
                    ->where(
                        'id',
                        $dbId
                    )
                    ->where(
                        'token_no',
                        $token
                    )
                    ->update(
                        [
                            'esign_status' => 'Y'
                        ]
                    );


            if (
                $updated === 0
            ) {

                throw new Exception(
                    'Identifier signer record was not found.'
                );
            }


            return;
        }


        throw new Exception(
            'Unknown signer type: ' . $partyType
        );
    }


    /**
     * Check whether every existing party, witness and identifier
     * has completed eSign.
     */
    private function allSignersSigned(
        string $token
    ): bool {

        $tables = [
            'ngdrstab_trn_party_entry_new',
            'ngdrstab_trn_witness',
            'ngdrstab_trn_identification'
        ];


        $hasSigners = false;


        foreach (
            $tables as $table
        ) {

            /*
             * -----------------------------------------------------
             * TOTAL RECORDS
             * -----------------------------------------------------
             */

            $count =
                DB::table(
                    $table
                )
                    ->where(
                        'token_no',
                        $token
                    )
                    ->count();


            if (
                $count > 0
            ) {

                $hasSigners = true;
            }


            /*
             * -----------------------------------------------------
             * ANY UNSIGNED RECORD?
             * -----------------------------------------------------
             */

            $unsignedExists =
                DB::table(
                    $table
                )
                    ->where(
                        'token_no',
                        $token
                    )
                    ->where(
                        function ($query) {

                            $query
                                ->whereNull(
                                    'esign_status'
                                )
                                ->orWhere(
                                    'esign_status',
                                    '!=',
                                    'Y'
                                );
                        }
                    )
                    ->exists();


            if (
                $unsignedExists
            ) {

                return false;
            }
        }


        /*
         * If there are no signer records at all,
         * do not mark the deed as completely signed.
         */

        return $hasSigners;
    }
}