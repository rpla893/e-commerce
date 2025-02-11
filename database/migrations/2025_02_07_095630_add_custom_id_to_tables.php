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
        foreach (['brands', 'categories', 'product_transactions', 'shoes', 'promo_codes'] as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->string('custom_id')->unique()->after('id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach (['brands', 'categories', 'product_transactions', 'shoes', 'promo_codes'] as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropColumn('custom_id');
            });
        }
    }
};
