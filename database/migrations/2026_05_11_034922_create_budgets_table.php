<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('period', ['daily', 'weekly', 'monthly', 'annually']);
            $table->decimal('limit_amount', 12, 2);
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->index(['user_id', 'period']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};
