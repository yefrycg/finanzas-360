<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['general_account', 'cash', 'checking_account', 'credit_card', 'savings_account']);
            $table->decimal('current_balance', 15, 2)->default(0);
            $table->decimal('credit_limit', 15, 2)->nullable();
            $table->string('color');
            $table->string('icon');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
