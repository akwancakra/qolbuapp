<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('beneficiaries', function (Blueprint $table) {
            $table->bigIncrements('nik')->primary();
            $table->string('name');
            $table->string('place_of_birth');
            $table->date('date_of_birth');
            $table->char('gender', 1);
            $table->string('neighborhood_unit')->nullable();
            $table->string('father')->nullable();
            $table->string('mother')->nullable();
            $table->string('education_level')->nullable();
            $table->string('school_grade')->nullable();
            $table->string('shirt_size')->nullable();
            $table->integer('shoe_size')->nullable();
            $table->string('description')->nullable();
            $table->string('phone_number', 15)->nullable();
            $table->string('death_certificate_number')->nullable();
            $table->timestamps();
        });

        DB::statement("ALTER TABLE `users` AUTO_INCREMENT = 1000000000000000;");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beneficiaries');
    }
};
