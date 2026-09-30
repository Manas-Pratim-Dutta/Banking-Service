<?php

namespace App\Http\Controllers;

use App\Models\Deed;
use App\Services\NgdrsSsoService;
use Illuminate\Http\Request;

class MockNgdrsController extends Controller
{
    protected NgdrsSsoService $ssoService;

    public function __construct(NgdrsSsoService $ssoService)
    {
        $this->ssoService = $ssoService;
    }

    /**
     * Receiver for official NGDRS Citizen SSO endpoint (POST /asmsso)
     * Mirrors ngdrs_citizen-dev LoginController@asmsso & LoginServices::decryptData
     */
    public function asmsso(Request $request)
    {
        $request->validate([
            'data' => 'required|string',
        ]);

        $encryptedData = $request->input('data');
        $decodedData = $this->ssoService->decryptPayload($encryptedData);

        if (empty($decodedData) || empty($decodedData['contact_fname']) || empty($decodedData['mobile_no'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to decrypt data or mandatory fields missing (contact_fname, mobile_no).'
            ], 422);
        }

        // Store session matching ngdrs_citizen-dev LoginServices::initSession
        session([
            'sso_payload' => $decodedData,
            'user_type' => $decodedData['user_type'] ?? 'B',
            'bank_trans_id' => $decodedData['bank_trans_id'] ?? null,
            'bank_user_id' => $decodedData['bank_user_id'] ?? null,
            'login_type' => 'B',
        ]);

        return view('ngdrs.portal', [
            'data' => $decodedData,
            'decryptionSuccess' => true,
            'errorMessage' => null,
            'encryptedPayload' => $encryptedData,
            'transId' => $decodedData['bank_trans_id'] ?? $decodedData['rtps_trans_id'] ?? null,
        ]);
    }

    /**
     * Backward-compatible portal landing
     */
    public function portal(Request $request)
    {
        if ($request->has('data')) {
            return $this->asmsso($request);
        }

        $encryptedPayload = $request->input('payload');
        $transId = $request->input('bank_trans_id');

        $data = null;
        $decryptionSuccess = false;
        $errorMessage = null;

        if ($encryptedPayload) {
            $data = $this->ssoService->decryptPayload($encryptedPayload);
            if ($data && isset($data['contact_fname'])) {
                $decryptionSuccess = true;
            } else {
                $errorMessage = 'Invalid AES-256-CBC payload or key mismatch.';
            }
        } else {
            $errorMessage = 'No encrypted payload received.';
        }

        return view('ngdrs.portal', compact(
            'data',
            'decryptionSuccess',
            'errorMessage',
            'encryptedPayload',
            'transId'
        ));
    }

    /**
     * Mock action to simulate SRO review / completion back into Bank Service
     */
    public function updateStatus(Request $request)
    {
        $request->validate([
            'deed_no' => 'required',
            'status' => 'required|in:In progress,SRO review,Completed',
        ]);

        $deed = Deed::where('deed_no', $request->deed_no)->first();
        if ($deed) {
            $deed->update(['status' => $request->status]);
            return redirect()->route('deeds.show', $deed->id)
                ->with('success', "NGDRS deed status updated to '{$request->status}' successfully.");
        }

        return redirect()->route('deeds.index')->with('info', 'Deed status updated.');
    }
}
