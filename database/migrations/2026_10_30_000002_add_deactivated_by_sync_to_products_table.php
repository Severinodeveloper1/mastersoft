<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // True only when the ERP sync switched the product off because the ERP stopped sending it,
            // so the sync may switch it back on; a product hidden by an admin stays false and hidden.
            $table->boolean('deactivated_by_sync')->default(false)->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('deactivated_by_sync');
        });
    }
};
