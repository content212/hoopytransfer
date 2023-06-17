<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateBookingDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_data', function (Blueprint $table) {
            $table->decimal('discount_price', 9, 2);
            $table->decimal('full_discount_price', 9, 2);
            #$table->enum('payment_type', ['Pre', 'Full'])->nullable()->change();
            DB::statement("ALTER TABLE booking_data MODIFY payment_type ENUM('Pre', 'Full', 'No') NULL");
            $table->dropColumn('paymentIntentSecret');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('booking_data', function (Blueprint $table) {
            $table->decimal('discount_price', 9, 2);
            $table->decimal('full_discount_price', 9, 2);
        });
    }
}
