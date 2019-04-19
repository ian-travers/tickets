<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttendeeMessagesTable extends Migration
{
    public function up()
    {
        Schema::create('attendee_messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('concert_id');
            $table->string('subject');
            $table->string('message');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('attendee_messages');
    }
}
