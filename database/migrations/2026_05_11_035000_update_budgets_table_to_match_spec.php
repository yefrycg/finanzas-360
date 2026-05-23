<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('budgets')) {
            return;
        }

        if (Schema::hasColumn('budgets', 'period')) {
            DB::table('budgets')->where('period', 'yearly')->update(['period' => 'annually']);

            DB::statement("ALTER TABLE `budgets` MODIFY `period` ENUM('daily','weekly','monthly','annually') NOT NULL");
        }

        if (Schema::hasColumn('budgets', 'limit_amount')) {
            DB::statement('ALTER TABLE `budgets` MODIFY `limit_amount` DECIMAL(12,2) NOT NULL');
        }

        Schema::table('budgets', function (Blueprint $table) {
            if (Schema::hasColumn('budgets', 'spent_amount')) {
                $table->dropColumn('spent_amount');
            }

            if (Schema::hasColumn('budgets', 'start_date')) {
                $table->dropColumn('start_date');
            }

            if (Schema::hasColumn('budgets', 'end_date')) {
                $table->dropColumn('end_date');
            }

            if (Schema::hasColumn('budgets', 'status')) {
                $table->dropColumn('status');
            }

            if (Schema::hasColumn('budgets', 'category_id')) {
                $table->dropConstrainedForeignId('category_id');
            }
        });

        Schema::table('budgets', function (Blueprint $table) {
            if (! Schema::hasColumn('budgets', 'user_id')) {
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            }

            if (! Schema::hasColumn('budgets', 'name')) {
                $table->string('name');
            }

            if (! Schema::hasColumn('budgets', 'period')) {
                $table->enum('period', ['daily', 'weekly', 'monthly', 'annually']);
            }

            if (! Schema::hasColumn('budgets', 'limit_amount')) {
                $table->decimal('limit_amount', 12, 2);
            }

            if (! Schema::hasColumn('budgets', 'created_at') && ! Schema::hasColumn('budgets', 'updated_at')) {
                $table->timestamps();
            }
        });
    }

    public function down(): void
    {
        // Irreversible: this migration removes legacy columns.
    }
};
