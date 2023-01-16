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
            $table->text('track_code');
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
            $table->string('delivery_type');
            $table->date('delivery_date');
            $table->time('delivery_time');
            $table->string('sender_name');
            $table->string('sender_phone');
            $table->string('sender_mail');
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('customer_mail');
            $table->string('company_name')->nullable();
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
