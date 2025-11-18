<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('cache', function (Blueprint $table) {
            $table->longText('value')->change();
            $table->unsignedBigInteger('expiration')->change();
        });

        Schema::table('cache_locks', function (Blueprint $table) {
            $table->unsignedBigInteger('expiration')->change();
        });
    }

    public function down()
    {
        Schema::table('cache', function (Blueprint $table) {
            $table->mediumText('value')->change();
            $table->integer('expiration')->change();
        });

        Schema::table('cache_locks', function (Blueprint $table) {
            $table->integer('expiration')->change();
        });
    }
};
