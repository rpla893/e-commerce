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
        Schema::create('product_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique(); // Tambahkan slug
            $table->string('barcode')->unique(); // Tambahkan barcode
             $table->string('phone');
            $table->string('email');
            $table->string('booking_trx_id')->unique();
            $table->string('city');
            $table->string('post_code');
            $table->text('address');
            $table->string('shoes_size');
            $table->string('quantity', 10, 2);
            $table->decimal('sub_total_amount', 15, 2);
            $table->decimal('grand_total_amount', 15, 2);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->boolean('is_paid')->default(false);
            $table->string('payment_method');
            $table->string('payment_provider')->nullable();
            $table->string('shipping_method');
            $table->foreignId('shoes_id')->constrained()->cascadeOnDelete();
            $table->foreignId('promo_code_id')->nullable()->constrained()->cascadeOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_transactions');
    }
};
