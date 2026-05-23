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
        Schema::create('debts', function (Blueprint $table) {
            $table->id();
            $table->string('lender');
            $table->decimal('total_amount', 12, 2);
            $table->decimal('paid_amount', 12, 2)->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['paid', 'no_paid'])->default('no_paid');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debts');
    }
};
