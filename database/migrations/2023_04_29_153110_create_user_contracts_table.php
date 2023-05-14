<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_contracts', function (Blueprint $table) {
            $table->id();
            
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');

            $table->bigInteger('contract_id')->unsigned()->nullable();
            $table->foreign('contract_id')->references('id')->on('contracts')->onDelete('set null');

            $table->bigInteger('booking_id')->unsigned()->nullable();
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('set null');

            $table->text('contract')->nullable();

            $table->string('name',500)->nullable();

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
        Schema::dropIfExists('user_contracts');
    }
}
