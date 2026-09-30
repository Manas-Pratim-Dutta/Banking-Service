<?php

namespace App\Services\Auth;

use App\Utils\Auth\ArrayBuilder;
use Carbon\Carbon;
use App\Models\CitizenUserReg;
use App\Models\CitizenUser;
use App\Models\TrnSingleWindow;
class LoginServices
{
    public static function decryptData(string $payload): ?array
    {
        $key = env('APP_CRYPTO_KEY');
        $cipher = "AES-256-CBC";
        $iv = "1234567890123456";

        $decrypted = openssl_decrypt(
            $payload,
            $cipher,
            $key,
            0,
            $iv
        );

        if ($decrypted === false) {
            return null;
        }

        $json = json_decode(trim($decrypted), true);

        return is_array($json) ? $json : null;
    }

    public function saveUserData(array $data, string $ip): bool
    {
        $now = Carbon::now()->toDateString();
        $saveToCitizenUserReg = $this->saveCitizenUserReg($data, $ip, $now);
        $citizenUserId = $this->saveCitizenUser($data, $ip, $now);
        if (!empty($citizenUserId) && $saveToCitizenUserReg) {
            $saveToTrnSingleWindow = $this->saveTrnSingleWindow($data, $citizenUserId, $now);
            return $saveToTrnSingleWindow;
        }

        return false;
    }

    public function saveCitizenUserReg(array $data, string $ip, $now): bool
    {
        $userName = $data['contact_fname'] . $data['mobile_no'];
        $userExists = CitizenUserReg::userExists($userName);
        $saveData = $updateData = false;
        if (!$userExists) {
            $toSave = ArrayBuilder::arrCitizenUserRegSave($data, $ip, $now);
            $saveData = CitizenUserReg::create($toSave) ? true : false;
        } else {
            $toUpdate = ArrayBuilder::arrCitizenUserRegUpdate($data, $ip, $now);
            $updateData = CitizenUserReg::updateUser($toUpdate, $userName);
        }

        return ($saveData || $updateData);
    }
    public function saveCitizenUser(array $data, string $ip, $now): ?int
    {
        $userName = $data['contact_fname'] . $data['mobile_no'];
        $userExists = CitizenUser::userExists($userName);
        $saveData = $updateData = false;
        if (!$userExists) {
            $toSave = ArrayBuilder::arrCitizenUserSave($data, $ip, $now);
            $saveData = CitizenUser::create($toSave) ? true : false;
        } else {
            $toUpdate = ArrayBuilder::arrCitizenUserUpdate($data, $ip, $now);
            $updateData = CitizenUser::updateUser($toUpdate, $userName);
        }

        if ($saveData || $updateData) {
            return CitizenUser::getUserId($userName);
        }

        return null;
    }

    public function saveTrnSingleWindow(array $data, string $userId, $now): bool
    {
        $toSave = ArrayBuilder::trnSingleWindowData($data, $userId, $now);
        $save = TrnSingleWindow::create($toSave) ? true : false;
        return $save;
    }

    public static function initSession(string $userName, int $userId, int $moduleId, string $process,array $decodedData = []): bool
    {
        $loginType = $decodedData['user_type'] ?? 'C';
        session([
            'doc_lang' => 'en',
            'login_type' => $loginType,
            'username' => $userName,
            'userId' => $userId,
            'module_id' => $moduleId,
            'session_redirect' => 'all-applications',
            'rtps_process' => $process,
            'sso_payload' => $decodedData
        ]);

        session()->forget(['selectedToken', 'articleId']);

        return session()->has('userId');
    }
}