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
            $table->enum('gender', ['male', 'female']);
            $table->string('neighborhood_unit')->nullable();
            $table->string('photo')->nullable();
            $table->string('father')->nullable();
            $table->string('father_photo')->nullable();
            $table->string('mother')->nullable();
            $table->string('mother_photo')->nullable();
            $table->string('last_education')->nullable();
            $table->string('school_grade')->nullable();
            $table->string('shirt_size')->nullable();
            $table->integer('shoe_size')->nullable();
            $table->enum('status', ['Yatim', 'Piatu', 'Yatim Piatu', 'Dhuafa'])->nullable();
            $table->string('phone_number', 15)->nullable();
            $table->string('father_death_certificate_number')->nullable();
            $table->string('mother_death_certificate_number')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        DB::statement("ALTER TABLE `beneficiaries` AUTO_INCREMENT = 1000000000000000;");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beneficiaries');
    }
};
