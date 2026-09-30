<?php

namespace App\Utils\Auth;

class ArrayBuilder
{
    // public static function arrCitizenUserRegSave(array $data, string $ip, string $now): array
    // {
    //     return [
    //         'contact_fname' => $data['contact_fname'] ?? null,
    //         'contact_lname' => $data['contact_lname'] ?? null,
    //         'building' => $data['building'] ?? null,
    //         'pincode' => $data['pincode'] ?? null,
    //         'state_id' => 18,
    //         'email_id' => $data['email_id'] ?? null,
    //         'mobile_no' => $data['mobile_no'] ?? null,
    //         'user_name' => $data['contact_fname'] . $data['mobile_no'],
    //         'user_pass' => '6b8f56da5bc5fafff0b5c60f52f094d998ecb94adf0c79d461a797100c0bd7ee',
    //         'req_ip' => $ip,
    //         'user_creation_date' => $now,
    //         'created' => $now,
    //         'user_type' => 'C',
    //         'role_id' => 1,
    //         'module_id' => 3,
    //         'full_name' => trim(($data['contact_fname'] ?? '') . ' ' . ($data['contact_lname'] ?? '')),
    //         'activeflag' => 'Y',
    //         'authentication_type' => 1,
    //         'user_active_date' => $now,
    //         'rtps_user_id' => $data['user_id'] ?? null
    //     ];
    // }

    public static function arrCitizenUserRegSave(array $data, string $ip, string $now): array
    {
        // dd($data);
        $userType = $data['user_type'] ?? 'C';

        $result = [
            'contact_fname' => $data['contact_fname'] ?? null,
            'contact_lname' => $data['contact_lname'] ?? null,
            'building' => $data['building'] ?? null,
            'pincode' => $data['pincode'] ?? null,
            'state_id' => 18,
            'email_id' => $data['email_id'] ?? null,
            'mobile_no' => $data['mobile_no'] ?? null,

            'user_name' => ($data['contact_fname'] ?? '')
                . ($data['mobile_no'] ?? ''),

            'user_pass' => '6b8f56da5bc5fafff0b5c60f52f094d998ecb94adf0c79d461a797100c0bd7ee',

            'req_ip' => $ip,
            'user_creation_date' => $now,
            'created' => $now,

            'user_type' => $userType,
            'role_id' => 1,
            'module_id' => 3,

            'full_name' => trim(
                ($data['contact_fname'] ?? '') . ' ' .
                    ($data['contact_lname'] ?? '')
            ),

            'activeflag' => 'Y',
            'authentication_type' => 1,
            'user_active_date' => $now,
        ];

        if ($userType == 'B') {
            // Bank user
            $result['bank_user_id'] = $data['bank_user_id'] ?? null;
        } else {
            // Citizen user
            $result['rtps_user_id'] = $data['user_id'] ?? null;
        }

        return $result;
    }

    public static function arrCitizenUserRegUpdate(array $data, string $ip, string $now): array
    {
        return [
            'contact_fname' => $data['contact_fname'],
            'contact_lname' => $data['contact_lname'],
            'building' => $data['building'],
            'pincode' => $data['pincode'],
            'email_id' => $data['email_id'],
            'mobile_no' => $data['mobile_no'],
            'user_pass' => '6b8f56da5bc5fafff0b5c60f52f094d998ecb94adf0c79d461a797100c0bd7ee',
            'req_ip' => $ip,
            'user_creation_date' => $now,
            'created' => $now
        ];
    }

    // public static function arrCitizenUserSave(array $data, string $ip, string $now): array
    // {
    //     return [
    //         'username' => $data['contact_fname'] . $data['mobile_no'],
    //         'password' => '6b8f56da5bc5fafff0b5c60f52f094d998ecb94adf0c79d461a797100c0bd7ee',
    //         'role_id' => 1,
    //         'module_id' => 3,
    //         'full_name' => trim(($data['contact_fname'] ?? '') . ' ' . ($data['contact_lname'] ?? '')),
    //         'mobile_no' => $data['mobile_no'] ?? null,
    //         'email_id' => $data['email_id'] ?? null,
    //         'created' => $now,
    //         'req_ip' => $ip,
    //         'activeflag' => 'Y',
    //         'state_id' => 18,
    //         'authetication_type' => 1,
    //         'user_active_date' => $now,
    //         'lang_both' => 1,
    //         'rtps_user_id' => $data['user_id'] ?? null
    //     ];
    // }

    public static function arrCitizenUserSave(array $data, string $ip, string $now): array
    {
        $userType = $data['user_type'] ?? 'C';
        $result = [
            'username' => $data['contact_fname'] . $data['mobile_no'],
            'password' => '6b8f56da5bc5fafff0b5c60f52f094d998ecb94adf0c79d461a797100c0bd7ee',
            'role_id' => 1,
            'authetication_type' => 1,
            'user_active_date' => $now,
            'lang_both' => 1,
            'rtps_user_id' => $data['user_id'] ?? null
        ];
        if ($userType === 'B') {
            $result['bank_user_id'] = $data['bank_user_id'] ?? null;
        } else {
            $result['rtps_user_id'] = $data['user_id'] ?? null;
        }
        return $result;
    }

    public static function arrCitizenUserUpdate(array $data, string $ip, string $now): array
    {
        return [
            'password' => '6b8f56da5bc5fafff0b5c60f52f094d998ecb94adf0c79d461a797100c0bd7ee',
            'role_id' => 1,
            'module_id' => 3,
            'full_name' => trim(($data['contact_fname'] ?? '') . ' ' . ($data['contact_lname'] ?? '')),
            'mobile_no' => $data['mobile_no'] ?? null,
            'email_id' => $data['email_id'] ?? null,
            'created' => $now,
            'req_ip' => $ip,
            'activeflag' => 'Y',
            'state_id' => 18,
            'authetication_type' => 1,
            'user_active_date' => $now,
            'lang_both' => 1,
            'rtps_user_id' => $data['user_id'] ?? null
        ];
    }

    public static function trnSingleWindowData(array $data, string $userId, string $now): array
    {
        return [
            'rtps_trans_id' => $data['rtps_trans_id'] ?? null,
            'user_id' => $userId,
            'mobile' => $data['mobile_no'] ?? null,
            'service_id' => $data['service_id'] ?? null,
            'portal_no' => $data['portal_no'] ?? null,
            'process' => $data['process'] ?? null,
            'response_url' => $data['response_url'] ?? null,
            'contact_fname' => $data['contact_fname'] ?? null,
            'contact_mname' => $data['contact_mname'] ?? null,
            'contact_lname' => $data['contact_lname'] ?? null,
            'building' => $data['building'] ?? null,
            'street' => $data['street'] ?? null,
            'city' => $data['city'] ?? null,
            'pincode' => $data['pincode'] ?? null,
            'state_id' => $data['state_id'] ?? null,
            'district_id' => $data['district_id'] ?? null,
            'taluka_id' => $data['taluka_id'] ?? null,
            'email_id' => $data['email_id'] ?? null,
            'mobile_no' => $data['mobile_no'] ?? null,
            'pan_no' => $data['pan_no'] ?? null,
            'uid' => $data['uid'] ?? null,
            'created' => $now
        ];
    }


}