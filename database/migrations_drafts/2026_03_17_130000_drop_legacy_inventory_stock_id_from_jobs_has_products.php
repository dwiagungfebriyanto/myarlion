<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Draft cleanup migration.
 *
 * NOTE:
 * - This file is intentionally placed in `database/migrations_drafts` so it is NOT executed by default.
 * - Move this file into `database/migrations` only after readiness checks pass.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('jobs_has_products', function (Blueprint $table) {
            $table->dropColumn('inventory_stock_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jobs_has_products', function (Blueprint $table) {
            $table->unsignedBigInteger('inventory_stock_id')
                ->nullable()
                ->comment('LEGACY BACKUP ONLY - SAFE TO DROP AFTER JOB PRODUCT MIGRATION VALIDATED');
        });
    }
};
