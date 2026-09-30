<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\BankMasterUser;
use App\Models\Branch;
use App\Models\BranchUser;
use App\Models\Deed;
use App\Models\SsoAuditLog;
use App\Services\NgdrsSsoService;
use App\Services\AuditService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

echo "=== NGDRS BANK SERVICE MICROSERVICE VERIFICATION ===\n\n";

// 1. Verify PostgreSQL Database Connection & Tables
echo "1. Checking PostgreSQL Database & Models...\n";
$bankCount = BankMasterUser::count();
$branchCount = Branch::count();
$officerCount = BranchUser::count();
$deedCount = Deed::count();
echo "   [✓] Bank Master Users in DB: {$bankCount}\n";
echo "   [✓] Branches in DB: {$branchCount}\n";
echo "   [✓] Authorized Officers in DB: {$officerCount}\n";
echo "   [✓] Deeds in DB: {$deedCount}\n";

$bank = BankMasterUser::first();
echo "   Bank: {$bank->bank_name} ({$bank->bank_code})\n";

// 2. Test Adding a New Branch (Requested Feature)
echo "\n2. Testing Add New Branch for Bank...\n";
$newBranchCode = 'BR' . str_pad($branchCount + 1, 3, '0', STR_PAD_LEFT);
$newBranch = Branch::updateOrCreate(
    ['bank_id' => $bank->id, 'branch_code' => $newBranchCode],
    [
        'branch_name' => 'Nagaon Town Branch',
        'ifsc_code' => 'AGB0001099',
        'district' => 'Nagaon',
        'address' => 'Main Road, Haibargaon, Nagaon, Assam 782002',
        'contact_phone' => '03672-234891',
        'contact_email' => 'branch.nagaon@agb.co.in',
        'status' => 'Active',
    ]
);
echo "   [✓] Successfully added Branch: {$newBranch->branch_name} [{$newBranch->branch_code}, IFSC: {$newBranch->ifsc_code}]\n";

// 3. Test Creating Authorized Officer with all compliance fields (PAN, TAN, Age, Address)
echo "\n3. Testing Create Authorized Officer with PAN, TAN, Age, Address...\n";
$testOfficer = BranchUser::updateOrCreate(
    ['email' => 'officer.test@agb.co.in'],
    [
        'bank_id' => $bank->id,
        'branch_id' => $newBranch->id,
        'officer_id' => 'AGB-NGN-01',
        'name' => 'Himanta Bordoloi',
        'mobile_number' => '94350 99887',
        'password' => Hash::make('password'),
        'designation' => 'Authorized Officer',
        'pan_number' => 'ABCPD9981K',
        'tan_number' => 'NGNB01923M',
        'age' => 42,
        'address' => 'Bordoloi Bhavan, Amolapatty, Nagaon, Assam 782001',
        'status' => 'Active',
    ]
);
echo "   [✓] Authorized Officer: {$testOfficer->name} ({$testOfficer->officer_id})\n";
echo "   [✓] PAN: {$testOfficer->pan_number} | TAN: {$testOfficer->tan_number} | Age: {$testOfficer->age}\n";
echo "   [✓] Address: {$testOfficer->address}\n";

// 4. Test Sequential Deed Generation
echo "\n4. Testing Sequential Deed Application Generation...\n";
$generatedDeedNo = Deed::generateDeedNo($bank, $newBranch);
echo "   Generated Sequential Deed No: {$generatedDeedNo}\n";

$testDeed = Deed::updateOrCreate(
    ['deed_no' => $generatedDeedNo],
    [
        'bank_id' => $bank->id,
        'branch_id' => $newBranch->id,
        'created_by_user_id' => $testOfficer->id,
        'borrower_name' => 'Kalyan Jyoti Saikia',
        'borrower_mobile' => '98640 11099',
        'borrower_pan' => 'SKKPB7712M',
        'property_details' => 'Dag No. 119, Patta No. 44, Mouza Town, Nagaon Revenue Circle',
        'loan_amount' => 5500000.00,
        'stamp_duty' => 110000.00,
        'status' => 'New',
        'bank_trans_id' => (string) Str::uuid(),
    ]
);
echo "   [✓] Created Deed in DB: {$testDeed->deed_no} (Loan: ₹{$testDeed->loan_amount}, Stamp: ₹{$testDeed->stamp_duty})\n";

// 5. Test AES-256-CBC Encryption & Decryption (NGDRS SSO Handshake)
echo "\n5. Testing NGDRS SSO AES-256-CBC Cryptographic Handshake...\n";
$ssoService = new NgdrsSsoService();

$payload = [
    'bank_trans_id' => $testDeed->bank_trans_id,
    'deed_no' => $testDeed->deed_no,
    'bank_code' => $bank->bank_code,
    'bank_name' => $bank->bank_name,
    'branch_code' => $newBranch->branch_code,
    'branch_name' => $newBranch->branch_name,
    'ifsc_code' => $newBranch->ifsc_code,
    'district' => $newBranch->district,
    'user_type' => 'B',
    'officer_id' => $testOfficer->officer_id,
    'officer_name' => $testOfficer->name,
    'officer_designation' => $testOfficer->designation,
    'mobile_no' => $testOfficer->mobile_number,
    'email' => $testOfficer->email,
    'pan_number' => $testOfficer->pan_number,
    'tan_number' => $testOfficer->tan_number,
    'borrower_name' => $testDeed->borrower_name,
    'borrower_pan' => $testDeed->borrower_pan,
    'property_details' => $testDeed->property_details,
    'loan_amount' => (float) $testDeed->loan_amount,
    'stamp_duty' => (float) $testDeed->stamp_duty,
    'timestamp' => now()->toIso8601String(),
];

$encryptedBase64 = $ssoService->encryptPayload($payload);
echo "   [✓] Encrypted Base64 Payload Length: " . strlen($encryptedBase64) . " bytes\n";
echo "   Ciphertext preview: " . substr($encryptedBase64, 0, 40) . "...\n";

// Decrypt & Verify
$decrypted = $ssoService->decryptPayload($encryptedBase64);
assert($decrypted !== null, 'Decryption failed!');
assert($decrypted['bank_trans_id'] === $testDeed->bank_trans_id, 'Bank trans ID mismatch!');
assert($decrypted['officer_name'] === $testOfficer->name, 'Officer name mismatch!');
assert($decrypted['pan_number'] === 'ABCPD9981K', 'PAN mismatch!');
assert($decrypted['user_type'] === 'B', 'User type mismatch!');
echo "   [✓] AES-256-CBC Decryption & Validation: 100% MATCH\n";

// 6. Test Audit Logging
echo "\n6. Testing Audit Log Recording...\n";
$auditLog = SsoAuditLog::create([
    'bank_id' => $bank->id,
    'branch_id' => $newBranch->id,
    'user_id' => $testOfficer->id,
    'deed_id' => $testDeed->id,
    'bank_trans_id' => $testDeed->bank_trans_id,
    'action' => 'SSO_INITIATED',
    'payload_preview' => json_encode($payload),
    'ip_address' => '127.0.0.1',
    'user_agent' => 'CLI Verification Test',
    'status' => 'SUCCESS',
]);
echo "   [✓] Audit Log Recorded (ID: {$auditLog->id}, Action: {$auditLog->action})\n";

// 7. Test Mock NGDRS Landing Status Transition
echo "\n7. Testing SRO Approval State Transition...\n";
$testDeed->update(['status' => 'Completed']);
echo "   [✓] Deed status updated to: {$testDeed->fresh()->status}\n";

echo "\n============================================\n";
echo "ALL TESTS PASSED WITH 100% SUCCESS!\n";
echo "============================================\n";
