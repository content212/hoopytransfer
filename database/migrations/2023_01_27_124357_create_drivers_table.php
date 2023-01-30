<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDriversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('car_id');
            $table->foreign('car_id')->references('id')->on('cars');
            $table->date('license_date');
            $table->string('license_class', 5);
            $table->string('license_no');
            $table->bigInteger('country');
            $table->foreign('country')->references('id')->on('countries');
            $table->bigInteger('state');
            $table->foreign('state')->references('id')->on('states');
            $table->string('address');
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
        Schema::dropIfExists('drivers');
    }
}
