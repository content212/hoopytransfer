<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateUserCouponCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_coupon_codes', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->bigInteger('coupon_code_id')->unsigned()->nullable();
            $table->foreign('coupon_code_id')->references('id')->on('coupon_codes')->onDelete('set null');
            $table->bigInteger('coupon_code_group_id')->unsigned()->nullable();
            $table->foreign('coupon_code_group_id')->references('id')->on('coupon_code_groups')->onDelete('set null');
            $table->timestamp('date_of_use')->nullable();
            $table->decimal('credit');
            $table->decimal('price');
            $table->string('code', 250);
            $table->unique('code');
            $table->uuid('guid')->default(DB::raw('(UUID())'));
            $table->boolean('is_gift')->default(0);
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
        Schema::dropIfExists('user_coupon_codes');
    }
}
