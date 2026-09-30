<?php

namespace App\Http\Controllers;

use App\Models\Deed;
use App\Services\AuditService;
use App\Services\NgdrsPayloadBuilder;
use App\Services\NgdrsSsoService;
use Illuminate\Http\Request;

class DeedRegistrationController extends Controller
{
    protected NgdrsSsoService $ssoService;

    public function __construct(NgdrsSsoService $ssoService)
    {
        $this->ssoService = $ssoService;
    }

    /**
     * Instant "+ Initiate registration" without showing any form.
     * Automatically creates sequential deed and directly hands off to NGDRS SSO portal.
     */
    public function quickInitiate(Request $request)
    {
        $bank = $request->attributes->get('bank');
        $officer = $request->attributes->get('officer');
        $isMaster = $request->attributes->get('is_master_admin');

        if ($isMaster || !$officer) {
            abort(403, 'Initiate registration can only be performed by Branch Authorized Officers.');
        }

        $branch = $officer->branch;
        if (!$branch) {
            return back()->with('error', 'Authorized Officer does not have an active branch assignment.');
        }

        // 1. Automatically generate sequential deed number
        $deedNo = Deed::generateDeedNo($bank, $branch);

        // 2. Create the Deed application record
        $deed = Deed::create([
            'deed_no'            => $deedNo,
            'bank_id'            => $bank->id,
            'branch_id'          => $branch->id,
            'created_by_user_id' => $officer->id,
            'borrower_name'      => 'Mortgage Applicant',
            'borrower_mobile'    => $officer->mobile_number,
            'property_details'   => "Mortgage registration parcel for {$branch->branch_name}, {$branch->district}",
            'loan_amount'        => 0.00,
            'stamp_duty'         => 0.00,
            'status'             => 'In progress',
            'bank_trans_id'      => $deedNo,
            'sso_redirected_at'  => now(),
        ]);

        // 3. Build ArrayBuilder compliant payload
        $payload = NgdrsPayloadBuilder::build($deed, $officer, $branch, $bank);

        // 4. Encrypt via AES-256-CBC matching ngdrs_citizen-dev
        $encryptedData = $this->ssoService->encryptPayload($payload);
        $ssoEndpoint = $this->ssoService->getEndpoint();

        // 5. Audit Logging
        AuditService::logSsoAttempt(
            $bank->id,
            $branch->id,
            $officer->id,
            $deed->id,
            $deed->deed_no,
            $payload,
            $request,
            'SUCCESS',
            'SSO_INITIATED'
        );

        // 6. Zero-form instant self-submitting view (No form displayed, immediate redirect to NGDRS)
        return view('sso.redirect', [
            'ssoEndpoint'   => $ssoEndpoint,
            'encryptedData' => $encryptedData,
            'deed'          => $deed,
            'payload'       => $payload,
        ]);
    }

    /**
     * Initiate NGDRS SSO Redirection for an existing Deed
     */
    public function initiateSso(Deed $deed, Request $request)
    {
        $bank = $request->attributes->get('bank');
        if ($deed->bank_id !== $bank->id) {
            abort(403, 'Unauthorized deed access.');
        }

        // 1. Context Extraction
        $branch = $deed->branch;
        $officer = $request->attributes->get('officer') ?? $deed->creator;

        // Ensure status & timestamp are updated
        $deed->update([
            'sso_redirected_at' => now(),
            'status' => $deed->status === 'New' ? 'In progress' : $deed->status,
        ]);

        // 2. Build Payload according to ngdrs_citizen-dev ArrayBuilder specifications
        $payload = NgdrsPayloadBuilder::build($deed, $officer, $branch, $bank);

        // 3. Encrypt via AES-256-CBC matching ngdrs_citizen-dev LoginServices::decryptData
        $encryptedData = $this->ssoService->encryptPayload($payload);
        $ssoEndpoint = $this->ssoService->getEndpoint();

        // 4. Audit Logging
        AuditService::logSsoAttempt(
            $bank->id,
            $branch->id,
            $officer->id,
            $deed->id,
            $deed->deed_no,
            $payload,
            $request,
            'SUCCESS',
            'SSO_INITIATED'
        );

        // 5. Render Zero-Form Self-Submitting View
        return view('sso.redirect', [
            'ssoEndpoint'   => $ssoEndpoint,
            'encryptedData' => $encryptedData,
            'deed'          => $deed,
            'payload'       => $payload,
        ]);
    }
}
