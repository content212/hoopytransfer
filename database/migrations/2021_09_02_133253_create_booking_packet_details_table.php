<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingPacketDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booking_packet_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('packet_id')->unsigned();
            $table->foreign('packet_id')->references('id')->on('booking_packets')->onDelete('cascade');
            $table->text('type');
            $table->text('size');
            $table->text('height');
            $table->text('weight');
            $table->text('cubic_meters');
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
        Schema::dropIfExists('booking_packet_details');
    }
}
