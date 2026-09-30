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
        Schema::create('ngdrstab_mst_property_type_contexts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('property_type_id')->unsigned();
            $table->bigInteger('article_id')->unsigned();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ngdrstab_mst_property_type_contexts');
    }
};
