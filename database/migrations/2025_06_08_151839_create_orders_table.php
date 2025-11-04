<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            // Buyer info
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone');

            // Shipping address
            $table->enum('shipping_method', ['address', 'econt_office'])->default('address');
            $table->string('city')->nullable();
            $table->string('zip')->nullable();
            $table->string('street')->nullable();
            $table->string('street_num')->nullable();

            // Office
            $table->string('office_code')->nullable();

            // Weight & totals
            $table->float('weight')->nullable();
            $table->decimal('total', 10, 2);

            // Invoice info
            $table->json('invoice')->nullable();

            // Legal
            $table->boolean('terms_accepted')->default(false);
            $table->timestamp('terms_accepted_at')->nullable();

            // Payment
            $table->enum('payment_method', ['card', 'cod']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
