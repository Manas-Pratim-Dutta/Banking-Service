<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ngdrstab_mst_property_type_contexts', function (Blueprint $table) {
            $table->bigInteger('area_type_id')->unsigned()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ngdrstab_mst_property_type_contexts', function (Blueprint $table) {
            //
        });
    }
};
