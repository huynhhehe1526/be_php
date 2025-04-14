<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMoviesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('movies', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('image');
            $table->integer('genreId');
            $table->string('director');
            $table->string('statusId');
            $table->string('preview');
            $table->string('duration');
            $table->string('actor');
            $table->date('premiere_date');
            $table->string('subtitle');
            $table->string('video');
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
        Schema::dropIfExists('movies');
    }
}
