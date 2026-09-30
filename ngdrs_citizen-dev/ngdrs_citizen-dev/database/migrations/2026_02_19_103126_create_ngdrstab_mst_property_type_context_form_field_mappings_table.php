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
        Schema::create('ngdrstab_mst_property_type_context_form_field_mappings', function (Blueprint $table) {
            $table->id();
            $table->integer('context_id')->unsigned();
            $table->integer('form_field_id')->unsigned();
            $table->char('display_flag',1)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ngdrstab_mst_property_type_context_form_field_mappings');
    }
};
