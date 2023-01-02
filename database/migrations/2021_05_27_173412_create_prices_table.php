<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prices', function (Blueprint $table) {
            $table->id();
            $table->string('area');
            $table->string('zip_code')->unique();
            $table->string('bp_km_price');
            $table->string('bp_small_6');
            $table->string('bp_small_3');
            $table->string('bp_small_2');
            $table->string('bp_small_express');
            $table->string('bp_small_timed');
            $table->string('bp_medium_6');
            $table->string('bp_medium_3');
            $table->string('bp_medium_2');
            $table->string('bp_medium_express');
            $table->string('bp_medium_timed');
            $table->string('bp_large_6');
            $table->string('bp_large_3');
            $table->string('bp_large_2');
            $table->string('bp_large_express');
            $table->string('bp_large_timed');
            $table->string('lp_km_price');
            $table->string('lp_small_6');
            $table->string('lp_small_3');
            $table->string('lp_small_2');
            $table->string('lp_small_express');
            $table->string('lp_small_timed');
            $table->string('lp_medium_6');
            $table->string('lp_medium_3');
            $table->string('lp_medium_2');
            $table->string('lp_medium_express');
            $table->string('lp_medium_timed');
            $table->string('lp_large_6');
            $table->string('lp_large_3');
            $table->string('lp_large_2');
            $table->string('lp_large_express');
            $table->string('lp_large_timed');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prices');
    }
}
