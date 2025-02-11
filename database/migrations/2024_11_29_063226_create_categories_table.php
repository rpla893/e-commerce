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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama kategori
            $table->string('slug')->unique(); // Slug untuk URL yang ramah SEO
            $table->string('icon')->nullable(); // Ikon kategori
            $table->enum('gender', ['wanita', 'pria', 'anak-anak', 'pria-wanita']); // Jenis kelamin
            $table->string('subcategory'); // Subkategori (kasual, klasik, formal, bayi, dll.)
            $table->string('brand')->nullable(); // Sub-subkategori (contoh: Adidas, Nike, dll.)
            $table->softDeletes(); // Soft delete
            $table->timestamps(); // Timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
