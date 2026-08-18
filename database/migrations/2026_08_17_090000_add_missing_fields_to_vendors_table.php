<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The Vendor form/table already reference these columns, but they were
     * never added to the database. This migration brings the schema in
     * line with what the UI expects.
     */
    public function up(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            if (! Schema::hasColumn('vendors', 'contact_name')) {
                $table->string('contact_name')->nullable()->after('phone');
            }

            if (! Schema::hasColumn('vendors', 'address')) {
                $table->string('address')->nullable()->after('contact_name');
            }

            if (! Schema::hasColumn('vendors', 'description')) {
                $table->text('description')->nullable()->after('notes');
            }

            if (! Schema::hasColumn('vendors', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('description');
            }
        });
    }

    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            foreach (['contact_name', 'address', 'description', 'is_active'] as $column) {
                if (Schema::hasColumn('vendors', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
