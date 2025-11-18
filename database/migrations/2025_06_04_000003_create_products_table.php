<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // Source / vendor reference
            $table->string('source')->index();
            $table->string('vendor_id')->index();

            // Basic info
            $table->string('title');
            $table->string('slug')->nullable();
            $table->string('brand_name')->nullable()->index();
            $table->string('brand_slug')->nullable()->index();
            $table->string('category')->nullable()->index();
            $table->string('category_slug')->nullable()->index();

            // Pricing
            $table->decimal('price', 10, 2)->nullable();
            $table->decimal('old_price', 10, 2)->nullable();
            $table->integer('stock')->nullable();

            // Weight
            $table->decimal('weight', 8, 3)->nullable(); // grams / kg

            // Media
            $table->string('image')->nullable();
            $table->json('images')->nullable();
            $table->string('label')->nullable();

            // Content
            $table->text('short_description')->nullable();
            $table->longText('description_html')->nullable();
            $table->longText('supplement_facts_html')->nullable();

            // Identifiers
            $table->string('sku')->nullable();
            $table->string('upc')->nullable();
            $table->string('ean')->nullable();

            // RAW DATA
            $table->longText('raw')->nullable();

            $table->timestamps();

            $table->unique(['source', 'vendor_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
