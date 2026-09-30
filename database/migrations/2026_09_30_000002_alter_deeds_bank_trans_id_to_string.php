<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Alter bank_trans_id from uuid to string (varchar 100) to support format BANK/YEAR/BRANCH/SEQ
        DB::statement('ALTER TABLE deeds ALTER COLUMN bank_trans_id TYPE VARCHAR(100);');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE deeds ALTER COLUMN bank_trans_id TYPE UUID USING bank_trans_id::uuid;');
    }
};
