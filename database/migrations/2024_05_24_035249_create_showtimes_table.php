<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShowtimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('showtimes', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('movie_id')->unsigned()->index();
            $table->foreign('movie_id')->references('id')->on('movies')->onDelete('cascade');
            $table->bigInteger('location_id')->unsigned()->index();
            $table->foreign('location_id')->references('id')->on('locations')->onDelete('cascade');
            $table->string('time');
            $table->date('date');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('showtimes');
    }
}
