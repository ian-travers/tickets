<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConcertsTable extends Migration
{
    public function up()
    {
        Schema::create('concerts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->text('additional_info')->nullable();
            $table->timestamp('date');
            $table->string('venue');
            $table->string('venue_address');
            $table->string('city');
            $table->string('state');
            $table->string('zip');
            $table->integer('ticket_price');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('concerts');
    }
}
