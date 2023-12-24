<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserCreditActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_credit_activities', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->bigInteger('user_coupon_code_id')->unsigned()->nullable();
            $table->foreign('user_coupon_code_id')->references('id')->on('user_coupon_codes')->onDelete('set null');
            $table->bigInteger('booking_id')->unsigned()->nullable();
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('set null');
            $table->decimal('credit');
            $table->string('note', 250)->nullable();
            $table->string('note2', 250)->nullable();
            $table->string('payment_intent')->nullable();
            $table->string('payment_status')->nullable();
            $table->string('refund')->nullable();
            $table->enum('activity_type', ['charge', 'spend']);
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
        Schema::dropIfExists('user_credit_activities');
    }
}