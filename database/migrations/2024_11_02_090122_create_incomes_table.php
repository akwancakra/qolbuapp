<?php

use App\Models\Ambassador;
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
        Schema::create('incomes', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Ambassador::class)->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->date('transfer_date');
            $table->decimal('amount', 19, 2);
            $table->string('donor');
            $table->string('team')->nullable();
            $table->string('payment_method')->default('Transfer Bank');
            $table->string('type')->default('Donasi');
            $table->string('on_behalf_of')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incomes');
    }
};
