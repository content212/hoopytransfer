<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePriceListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('price_lists', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('company_id')->unsigned();
            $table->foreign('company_id')->references('id')->on('price_companies')->onDelete('cascade');
            $table->text('1_weekday');
            $table->text('2_weekday');
            $table->text('3_weekday');
            $table->text('4_weekday');
            $table->text('1_saturday');
            $table->text('2_saturday');
            $table->text('3_saturday');
            $table->text('4_saturday');
            $table->text('1_sunday');
            $table->text('2_sunday');
            $table->text('3_sunday');
            $table->text('4_sunday');
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
        Schema::dropIfExists('price_lists');
    }
}
