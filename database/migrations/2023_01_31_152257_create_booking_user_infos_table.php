<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingUserInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booking_user_infos', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('booking_id')->unsigned();
            $table->string('name', 255);
            $table->string('surname', 255);
            $table->string('email');
            $table->string('phone');
            $table->integer('country')->unsigned();
            $table->foreign('country')->references('id')->on('countries');
            $table->integer('state')->unsigned();
            $table->foreign('state')->references('id')->on('states');
            $table->string('company_name', 255)->nullable();
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
        Schema::dropIfExists('booking_user_infos');
    }
}
