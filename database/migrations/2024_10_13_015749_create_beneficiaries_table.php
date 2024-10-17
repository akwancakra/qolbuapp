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
        Schema::create('beneficiaries', function (Blueprint $table) {
            $table->string('id', 15)->primary();
            $table->string('name', 100);
            $table->date('date_of_birth');
            $table->integer('age');
            $table->string('address', 500);
            $table->string('father_name', 100);
            $table->string('mother_name', 100);
            $table->string('school_level', 15);
            $table->string('school_name', 100);
            $table->string('shirt_size', 10);
            $table->string('shoe_size', 10);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beneficiaries');
    }
};
