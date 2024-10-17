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
        Schema::create('dutas', function (Blueprint $table) {
            $table->string('id', 15)->primary();
            $table->string('name', 100);
            $table->string('code', 8);
            $table->string('phone_number', 14);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dutas');
    }
};
