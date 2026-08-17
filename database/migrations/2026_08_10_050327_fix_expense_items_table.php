<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expense_items', function (Blueprint $table) {
            if (! Schema::hasColumn('expense_items', 'amount')) {
                $table->unsignedBigInteger('amount')->nullable()->after('unit');
            }

            if (! Schema::hasColumn('expense_items', 'created_at')) {
                $table->timestamps();
            }
        });
    }

    public function down(): void
    {
        Schema::table('expense_items', function (Blueprint $table) {
            if (Schema::hasColumn('expense_items', 'amount')) {
                $table->dropColumn('amount');
            }

            if (
                Schema::hasColumn('expense_items', 'created_at') &&
                Schema::hasColumn('expense_items', 'updated_at')
            ) {
                $table->dropTimestamps();
            }
        });
    }
};
