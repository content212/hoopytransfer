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
            $table->bigInteger('car_type')->unsigned();
            $table->foreign('car_type')->references('id')->on('car_types')->onDelete('cascade');
            $table->integer('start_km');
            $table->integer('finish_km');
            $table->decimal('opening_fee', 9, 3);
            $table->decimal('km_fee', 9, 3);
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
