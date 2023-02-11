<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stations', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->string('official_name');
            $table->string('official_phone');
            $table->integer('country')->unsigned();
            $table->foreign('country')->references('id')->on('countries');
            $table->integer('state')->unsigned();
            $table->foreign('state')->references('id')->on('states');
            $table->string('address');
            $table->string('latitude', 255);
            $table->string('longitude', 255);
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
        Schema::dropIfExists('stations');
    }
}
