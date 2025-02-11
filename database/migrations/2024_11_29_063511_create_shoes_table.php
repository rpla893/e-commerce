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
        Schema::create('shoes', function (Blueprint $table) {
            $table->id();
            $table->string('thumbnail');
            $table->string('name');
            $table->decimal('price', 10, 2);
            $table->decimal('diskon', 5, 2)->nullable();
            $table->unsignedInteger('stock');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->boolean('is_popular')->default(false);
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('slug')->unique();
            $table->foreignId('brand_id')->constrained()->cascadeOnDelete();
            $table->text('about')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shoes');
    }
};
