<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('role', 100);
            $table->integer('status');
            $table->boolean('push_enabled');
            $table->boolean('sms_enabled');
            $table->boolean('email_enabled');
            $table->text('push_title')->nullable();
            $table->text('push_body')->nullable();
            $table->text('sms_body')->nullable();
            $table->text('email_subject')->nullable();
            $table->text('email_body')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notifications');
    }
}
