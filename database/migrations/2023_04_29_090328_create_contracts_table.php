<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->boolean('active');
            $table->boolean('required');
            $table->boolean('selected');
            $table->enum('position', ['register', 'orderform', 'payment']);
            $table->string('name', 500)->nullable();
            $table->string('prefix', 500)->nullable();
            $table->string('suffix', 500)->nullable();
            $table->text('contract')->nullable();
            $table->integer('display_order');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contracts');
    }
}
