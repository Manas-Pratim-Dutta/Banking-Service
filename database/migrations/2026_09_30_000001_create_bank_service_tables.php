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
        // 1. Bank Master Users (Top-level bank entity / Master Admin)
        Schema::create('bank_master_users', function (Blueprint $table) {
            $table->id();
            $table->string('bank_code', 50)->unique();
            $table->string('bank_name', 255);
            $table->string('email', 255)->unique();
            $table->string('password');
            $table->string('phone', 50)->nullable();
            $table->text('head_office_address')->nullable();
            $table->string('notification_email', 255)->nullable();
            $table->string('status', 20)->default('Active');
            $table->rememberToken();
            $table->timestamps();
        });

        // 2. Branches under the Bank
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_id')->constrained('bank_master_users')->onDelete('cascade');
            $table->string('branch_code', 50);
            $table->string('branch_name', 255);
            $table->string('ifsc_code', 50);
            $table->string('district', 100);
            $table->text('address')->nullable();
            $table->string('contact_phone', 50)->nullable();
            $table->string('contact_email', 255)->nullable();
            $table->string('status', 20)->default('Active');
            $table->timestamps();

            $table->unique(['bank_id', 'branch_code']);
        });

        // 3. Branch Users (Authorized Officers)
        Schema::create('branch_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_id')->constrained('bank_master_users')->onDelete('cascade');
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->string('officer_id', 50)->unique();
            $table->string('name', 255);
            $table->string('email', 255)->unique();
            $table->string('mobile_number', 50);
            $table->string('password');
            $table->string('designation', 100)->default('Authorized Officer');
            $table->string('pan_number', 20);
            $table->string('tan_number', 20)->nullable();
            $table->integer('age')->nullable();
            $table->text('address')->nullable();
            $table->string('status', 20)->default('Active');
            $table->rememberToken();
            $table->timestamps();
        });

        // 4. Deeds (Mortgage Application Tracking)
        Schema::create('deeds', function (Blueprint $table) {
            $table->id();
            $table->string('deed_no', 100)->unique();
            $table->foreignId('bank_id')->constrained('bank_master_users')->onDelete('cascade');
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->foreignId('created_by_user_id')->constrained('branch_users')->onDelete('cascade');
            $table->string('borrower_name', 255);
            $table->string('borrower_mobile', 50)->nullable();
            $table->string('borrower_pan', 20)->nullable();
            $table->text('property_details')->nullable();
            $table->decimal('loan_amount', 15, 2)->default(0);
            $table->decimal('stamp_duty', 15, 2)->default(0);
            $table->string('status', 50)->default('New'); // New, In progress, SRO review, Completed, Rejected
            $table->string('bank_trans_id', 100)->unique();
            $table->timestamp('sso_redirected_at')->nullable();
            $table->timestamps();
        });

        // 5. SSO Audit Logs
        Schema::create('sso_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_id')->nullable()->constrained('bank_master_users')->nullOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('branch_users')->nullOnDelete();
            $table->foreignId('deed_id')->nullable()->constrained('deeds')->nullOnDelete();
            $table->string('bank_trans_id', 100)->index();
            $table->string('action', 50)->default('SSO_INITIATED');
            $table->text('payload_preview')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('status', 50)->default('SUCCESS');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sso_audit_logs');
        Schema::dropIfExists('deeds');
        Schema::dropIfExists('branch_users');
        Schema::dropIfExists('branches');
        Schema::dropIfExists('bank_master_users');
    }
};
