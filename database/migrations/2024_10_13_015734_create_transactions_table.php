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
        Schema::create('transactions', function (Blueprint $table) {
            $table->string('id', 15)->primary();
            $table->timestamps(); // created_at dan updated_at
            $table->date('transfer_date');
            $table->string('donor_name', 100);
            $table->string('method', 50);
            $table->string('type', 50);
            $table->integer('amount')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
