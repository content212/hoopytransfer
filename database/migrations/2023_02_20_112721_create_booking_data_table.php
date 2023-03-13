<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booking_data', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('booking_id')->unsigned();
            $table->integer('km');
            $table->decimal('opening_fee', 9, 2);
            $table->decimal('km_fee', 9, 2);
            $table->integer('discount_rate');
            $table->enum('payment_type', ['Pre', 'Full']);
            $table->decimal('system_payment', 9, 2);
            $table->decimal('driver_payment', 9, 2);
            $table->decimal('total', 9, 2);
            $table->string('paymentIntentSecret');
            $table->integer('full_discount');
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
        Schema::dropIfExists('booking_data');
    }
}
