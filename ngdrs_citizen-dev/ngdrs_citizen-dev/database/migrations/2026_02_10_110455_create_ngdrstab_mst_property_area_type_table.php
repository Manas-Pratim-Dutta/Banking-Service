<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ngdrstab_mst_property_area_type', function (Blueprint $table) {

            $table->bigIncrements('area_type_id');

            $table->string('area_type_desc_en')->nullable();

            $table->char('flag', 1)->default('Y');   

            $table->bigInteger('old_area_type_id')->nullable();

            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ngdrstab_mst_property_area_type');
    }
};
