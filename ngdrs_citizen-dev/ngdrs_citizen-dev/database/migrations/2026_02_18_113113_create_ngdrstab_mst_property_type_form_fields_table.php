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
        Schema::create('ngdrstab_mst_property_type_form_fields', function (Blueprint $table) {
            $table->id();
            $table->string('field_key', 100)->unique();
            $table->string('label', 150);
            $table->string('type', 50);
            $table->boolean('is_required')->default(false);
            $table->boolean('is_readonly')->default(false);
            $table->boolean('is_visible')->default(true);
            $table->json('validation_rules')->nullable();
            $table->string('placeholder')->nullable();
            $table->string('help_text')->nullable();
            $table->json('options')->nullable();
            $table->integer('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ngdrstab_mst_property_type_form_fields');
    }
};
