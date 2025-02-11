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
            $table->string('phone');
            $table->string('email');
            $table->string('booking_trx_id');
            $table->string('city');
            $table->string('post_code');
            $table->string('proof');
            $table->unsignedInteger('shoes_size');
            $table->text('address');
            $table->integer('quantity');
            $table->integer('sub_total_amount');
            $table->integer('grand_total_amount');
            $table->integer('discount_amount');
            $table->boolean('is_paid');
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
