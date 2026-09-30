<?php

namespace Database\Seeders;

use App\Models\BankMasterUser;
use App\Models\Branch;
use App\Models\BranchUser;
use App\Models\Deed;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Bank Master
        $bank = BankMasterUser::updateOrCreate(
            ['bank_code' => 'AGB'],
            [
                'bank_name' => 'Assam Gramin Bank',
                'email' => 'admin@agb.co.in',
                'password' => Hash::make('password'),
                'phone' => '98540 11221',
                'head_office_address' => 'Head Office, Mahapurush Srimanta Sankardev Path, Bhangagarh, Guwahati, Assam 781005',
                'notification_email' => 'head.office@agb.co.in',
                'status' => 'Active',
            ]
        );

        // 2. Branches
        $branchesData = [
            [
                'branch_code' => 'BR001',
                'branch_name' => 'Guwahati Kamrup Branch',
                'ifsc_code' => 'AGB0001102',
                'district' => 'Kamrup',
                'address' => 'GS Road, Bhangagarh, Guwahati, Assam 781005',
                'contact_phone' => '0361-2456789',
                'contact_email' => 'guwahati.branch@agb.co.in',
                'status' => 'Active',
            ],
            [
                'branch_code' => 'BR002',
                'branch_name' => 'Silchar Cachar Branch',
                'ifsc_code' => 'AGB0001087',
                'district' => 'Cachar',
                'address' => 'Club Road, Silchar, Assam 788001',
                'contact_phone' => '03842-234567',
                'contact_email' => 'silchar.branch@agb.co.in',
                'status' => 'Active',
            ],
            [
                'branch_code' => 'BR003',
                'branch_name' => 'Dibrugarh Branch',
                'ifsc_code' => 'AGB0001045',
                'district' => 'Dibrugarh',
                'address' => 'RKB Path, Dibrugarh, Assam 786001',
                'contact_phone' => '0373-2321456',
                'contact_email' => 'dibrugarh.branch@agb.co.in',
                'status' => 'Active',
            ],
            [
                'branch_code' => 'BR004',
                'branch_name' => 'Jorhat Branch',
                'ifsc_code' => 'AGB0001064',
                'district' => 'Jorhat',
                'address' => 'AT Road, Jorhat, Assam 785001',
                'contact_phone' => '0376-2301298',
                'contact_email' => 'jorhat.branch@agb.co.in',
                'status' => 'Active',
            ],
        ];

        $branches = [];
        foreach ($branchesData as $bData) {
            $branches[$bData['branch_code']] = Branch::updateOrCreate(
                ['bank_id' => $bank->id, 'branch_code' => $bData['branch_code']],
                $bData
            );
        }

        // 3. Authorized Officers (Branch Users with PAN, TAN, Age, Address)
        $officersData = [
            [
                'branch_code' => 'BR001',
                'officer_id' => 'AGB-GHY-01',
                'name' => 'Bikash Sarma',
                'email' => 'branch.guwahati@agb.co.in',
                'mobile_number' => '94350 22110',
                'password' => Hash::make('password'),
                'designation' => 'Authorized Officer',
                'pan_number' => 'BPZPS2914K',
                'tan_number' => 'SHLB02914A',
                'age' => 38,
                'address' => 'Flat 302, Green View Apts, Zoo Road Tiniali, Guwahati, Assam',
                'status' => 'Active',
            ],
            [
                'branch_code' => 'BR002',
                'officer_id' => 'AGB-SCH-02',
                'name' => 'Priya Nath',
                'email' => 'branch.silchar@agb.co.in',
                'mobile_number' => '94311 38112',
                'password' => Hash::make('password'),
                'designation' => 'Authorized Officer',
                'pan_number' => 'CKLPN8832L',
                'tan_number' => 'SLCR01944B',
                'age' => 34,
                'address' => 'Holding 14, Tarapur Station Road, Silchar, Assam',
                'status' => 'Active',
            ],
            [
                'branch_code' => 'BR003',
                'officer_id' => 'AGB-DBR-03',
                'name' => 'Anupam Gogoi',
                'email' => 'branch.dibrugarh@agb.co.in',
                'mobile_number' => '98640 32123',
                'password' => Hash::make('password'),
                'designation' => 'Authorized Officer',
                'pan_number' => 'AGYPG4102N',
                'tan_number' => 'DBRG00392C',
                'age' => 41,
                'address' => 'Ward 4, Graham Bazar, Dibrugarh, Assam',
                'status' => 'Active',
            ],
        ];

        $officers = [];
        foreach ($officersData as $oData) {
            $branch = $branches[$oData['branch_code']];
            unset($oData['branch_code']);
            $officers[] = BranchUser::updateOrCreate(
                ['email' => $oData['email']],
                array_merge($oData, [
                    'bank_id' => $bank->id,
                    'branch_id' => $branch->id,
                ])
            );
        }

        // 4. Sample Deeds / Applications
        $primaryOfficer = $officers[0]; // Bikash Sarma
        $primaryBranch = $branches['BR001'];

        $sampleDeeds = [
            [
                'borrower_name' => 'Ramesh Chandra Barua',
                'borrower_mobile' => '98540 88219',
                'borrower_pan' => 'BRUPB4401M',
                'property_details' => 'Dag No. 412, Patta No. 89, Mouza Beltola, Guwahati, Kamrup (Metro)',
                'loan_amount' => 4500000.00,
                'stamp_duty' => 90000.00,
                'status' => 'In progress',
            ],
            [
                'borrower_name' => 'Deepak Kalita & Sunita Kalita',
                'borrower_mobile' => '94351 12903',
                'borrower_pan' => 'KLTPD1198R',
                'property_details' => 'Dag No. 102, Patta No. 34, Dispur Mouza, Guwahati',
                'loan_amount' => 6200000.00,
                'stamp_duty' => 124000.00,
                'status' => 'SRO review',
            ],
            [
                'borrower_name' => 'Manas Jyoti Hazarika',
                'borrower_mobile' => '97060 45112',
                'borrower_pan' => 'HZRMJ9823T',
                'property_details' => 'Flat 4B, Brahmaputra Heights, Jalukbari, Guwahati',
                'loan_amount' => 3800000.00,
                'stamp_duty' => 76000.00,
                'status' => 'New',
            ],
            [
                'borrower_name' => 'Pranjal Dutta',
                'borrower_mobile' => '98642 99014',
                'borrower_pan' => 'DTTBP5531K',
                'property_details' => 'Dag No. 84, Patta No. 12, Panbazar, Guwahati',
                'loan_amount' => 2500000.00,
                'stamp_duty' => 50000.00,
                'status' => 'Completed',
            ],
        ];

        foreach ($sampleDeeds as $index => $deedInfo) {
            $deedNo = sprintf("AGB/2026/BR001/%05d", $index + 1);
            Deed::updateOrCreate(
                ['deed_no' => $deedNo],
                array_merge($deedInfo, [
                    'bank_id' => $bank->id,
                    'branch_id' => $primaryBranch->id,
                    'created_by_user_id' => $primaryOfficer->id,
                    'bank_trans_id' => (string) Str::uuid(),
                ])
            );
        }
    }
}
