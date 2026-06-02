<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');

            // ── From Excel: Personal Info ──────────────────────────────────
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('email_address')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('blood_type', 10)->nullable();
            $table->decimal('height_cm', 6, 2)->nullable();
            $table->decimal('weight_kg', 6, 2)->nullable();
            $table->text('home_address')->nullable();         // Home Address (St., Brgy, Mun./City, Prov.)
            $table->string('phone_number', 20)->nullable();   // Phone number (09xxxxxxxxx)
            $table->string('emergency_contact_no', 20)->nullable(); // Emergency Contact No. (09xxxxxxxxx)

            // ── From Excel: Government IDs ─────────────────────────────────
            $table->string('id_number')->nullable()->unique(); // ID NUMBER (PDS-xxxxxxxxx)
            $table->string('tin')->nullable();                 // TIN (xxx-xxx-xxx)
            $table->string('pagibig_no')->nullable();          // Pag-IBIG No.
            $table->string('philhealth')->nullable();          // PhilHealth (15-000000000-6)
            $table->string('gsis_no')->nullable();             // GSIS No. (10 digit No.)

            // ── From Excel: HMO ────────────────────────────────────────────
            $table->string('hmo_organization')->nullable();    // HMO ORGANIZATION ( ex. 1 Health Coop - Ficco )
            $table->string('hmo_number')->nullable();          // HMO #

            // ── From Excel: Professional ───────────────────────────────────
            $table->text('eligibility')->nullable();           // ELIGIBILITY (CSC, TESDA NC II, PRC, OTHERS)
            $table->string('position_title')->nullable();      // Position Title
            $table->string('licence_number')->nullable();      // LICENCE NUMBER

            // ── Extra fields (not in Excel, kept for system use) ───────────
            $table->string('department')->nullable();
            $table->string('office')->nullable();
            $table->string('signature_path')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};