<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCouponCodeGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupon_code_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('prefix');
            $table->integer('character_length');
            $table->integer('quantity');
            $table->bigInteger('coupon_code_id')->unsigned()->nullable();
            $table->foreign('coupon_code_id')->references('id')->on('coupon_codes')->onDelete('set null');
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
        Schema::dropIfExists('coupon_code_groups');
    }
}
