<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->integer('status');
            $table->bigInteger('user_id')->nullable()->unsigned();
            $table->bigInteger('driver_id')->unsigned()->nullable();
            $table->bigInteger('car_type')->unsigned();
            $table->text('track_code')->nullable();
            $table->string('from');
            $table->integer('km');
            $table->integer('duration');
            $table->text('from_name');
            $table->text('from_address');
            $table->text('from_lat', 255);
            $table->text('from_lng', 255);
            $table->string('to');
            $table->text('to_name');
            $table->text('to_address');
            $table->text('to_lat', 255);
            $table->text('to_lng', 255);
            $table->date('booking_date');
            $table->time('booking_time');
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
        Schema::dropIfExists('bookings');
    }
}
