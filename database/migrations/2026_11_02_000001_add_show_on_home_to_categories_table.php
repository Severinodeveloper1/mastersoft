<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            // Chosen in the admin panel: only these categories appear in "Compra por categoría" on the home page.
            $table->boolean('show_on_home')->default(false)->after('is_active');
        });

        // Start with the six active categories that have the most products visible in the store
        // (active and in stock), so the home page is not empty right after the deploy.
        $topCategoryIds = DB::table('products')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->where('categories.is_active', true)
            ->where('products.is_active', true)
            ->where('products.stock', '>', 0)
            ->groupBy('products.category_id')
            ->orderByRaw('COUNT(*) DESC')
            ->orderBy('products.category_id')
            ->limit(6)
            ->pluck('products.category_id');

        DB::table('categories')->whereIn('id', $topCategoryIds)->update(['show_on_home' => true]);
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('show_on_home');
        });
    }
};
