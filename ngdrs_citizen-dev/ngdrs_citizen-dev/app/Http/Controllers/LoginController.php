<?php

namespace App\Http\Controllers;
use App\Utils\ApiResponse;
use App\Services\Auth\LoginServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\CitizenUser;
use App\Models\MstLoginUsersCitizen;

class LoginController extends Controller
{
    /**
     * POST /asmsso
     * Accepts a POST param "data" (encrypted string), decrypts, stores/updates users,
     * inserts a single-window record and performs login.
     */

    public function encryptAsmSsoPayloadApi(Request $request)
    {
        return response()->json([
            'encrypted' => $this->encryptAsmSsoPayload($request->all())
        ]);
    }

    public function encryptAsmSsoPayload(array $data)
    {
        $key = "1234567890123456";
        $iv = "1234567890123456";

        $json = json_encode($data, JSON_UNESCAPED_SLASHES);

        if ($json === false) {
            throw new Exception('JSON encoding failed');
        }

        $encrypted = openssl_encrypt(
            $json,
            'AES-256-CBC',
            $key,
            0,
            $iv
        );

        if ($encrypted === false) {
            throw new Exception('Encryption failed');
        }

        return $encrypted; // Base64 encoded string
    }

    public function autoLogin(Request $request)
    {
        $request->merge([
            'data' => 'AMNRaPa38i5fz39donI93h7YDpM3o2KUShEPA8U9upRI83aIwjLYnfJ9SRm3bc0WjJBWMWcxO72m5JTUzHO9GaTTdG///Vid28ZF1fmmIJH0nbW8J+QkvTZqlAoomEO7aK8hDipwV46IwVm0U0FNY6Y6mFt9MQ84QS6FxYbJSwAyoHlTVOv26YrczNSBh7bbSwpq75UHEWbi4johs0BgBZVHgIfY/ssy4HKOzrmadv+CTMYl6kHW6Wix9eP7AMkjM3Sv45XuVW2tFnb7Y5tKpEargcrp4TI7A4b4n843WX8pIKm5oaKhjwae/RoUq1rwbGBLbhOCNw2GZNcBP9b9haEnZC4X1ulBMlWy8AwIzhuiFsCe+qRUYdR7nXoKIpg/9GdVvwoSAslm4gS7jOMz+oJNcRpJC/R/gKbFzXzKnuYjADcCmqSi2eBVdxj4YXtaeYcJJwTamtymIrgcehqIS++Bf9mfOIv//nCc0dcdYdKTz0dKtwuFJquPDWR16KKrG8q7attCNEtlo3ZEiusILvuAQI7A/T21GOYiwewvdGw='
        ]);

        return $this->asmsso($request);
    }

    public function asmsso(Request $request)
    {
        $request->validate([
            'data' => 'required|string'
        ]);
        $enctyptedData = $request->input('data');

        try {
            $decodedData = LoginServices::decryptData($enctyptedData);
            if (empty($decodedData) || empty($decodedData['contact_fname']) || empty($decodedData['mobile_no'])) {
                return response()->json(['success' => false, 'message' => 'Unable to decrypt data or mandatory fields missing.']);
            }
            $ip = $request->ip();
            $services = new LoginServices();
            $saveUser = $services->saveUserData($decodedData, $ip);
            $rtpsProcess = $decodedData['process'];
            $userName = $decodedData['contact_fname'] . $decodedData['mobile_no'];
            if ($saveUser) {
                return $this->singleWindowLogin($userName, $rtpsProcess,$decodedData);
            }
            return response()->json(['success' => false, 'message' => 'Failed to save user data.']);
        } catch (\Exception $e) {
            Log::error('asmsso error: ' . $e->getMessage());
            return response()->json(['error' => 'Server error', 'details' => $e->getMessage()], 500);
        }
    }

    public function singleWindowLogin(string $userName, string $process,$decodedData= [])
    {
        if (empty($userName)) {
            return ApiResponse::error('Missing param username', 404);
        }

        $user = CitizenUser::getByUsername($userName);
        if (empty($user)) {
            return ApiResponse::error('User not found', 404);
        }
        if ($user->activeflag == 'N') {
            return ApiResponse::error('User not active', 200);
        }
        Auth::loginUsingId($user->id);
        if (!Auth::check()) {
            return ApiResponse::error('Login failed', 401);
        }

        $saveSession = LoginServices::initSession($userName, $user->id, $user->module_id, $process,$decodedData);
        // dd((session()->getId()));
        if (!$saveSession) {
            return ApiResponse::error('Session failed', 404);
        }

        MstLoginUsersCitizen::insertLoginDetails($user->id);

        if ($process == 'I') {
            return redirect()->route('general-info');

        }

        return redirect()->route(route: 'general-info');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

}
