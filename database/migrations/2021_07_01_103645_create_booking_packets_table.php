<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingPacketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booking_packets', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('bookingId')->unsigned();
            $table->foreign('bookingId')->references('id')->on('bookings')->onDelete('cascade');
            $table->text('cubic_meters');
            $table->integer('kg')->nullable();
            $table->text('type');
            $table->text('price');
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
        Schema::dropIfExists('booking_packets');

    }
}
