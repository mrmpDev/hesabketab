<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * ExpenseCategoryForm already has a description textarea, but the
     * column was never added to the database.
     */
    public function up(): void
    {
        Schema::table('expense_categories', function (Blueprint $table) {
            if (! Schema::hasColumn('expense_categories', 'description')) {
                $table->text('description')->nullable()->after('color');
            }
        });
    }

    public function down(): void
    {
        Schema::table('expense_categories', function (Blueprint $table) {
            if (Schema::hasColumn('expense_categories', 'description')) {
                $table->dropColumn('description');
            }
        });
    }
};
