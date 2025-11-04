<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                ->constrained()
                ->onDelete('cascade');
            $table->unsignedBigInteger('product_id'); // external API ID

            $table->string('product_source')->nullable(); // fitness1, revita, etc
            $table->string('product_slug')->nullable();
            $table->string('product_image')->nullable();

            $table->string('name');
            $table->decimal('price', 10, 2);
            $table->integer('quantity');
            $table->string('currency')->default('лв.');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
