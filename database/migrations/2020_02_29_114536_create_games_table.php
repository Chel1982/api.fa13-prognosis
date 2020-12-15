<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedBigInteger('first_team_id')->unsigned();
            $table->unsignedBigInteger('second_team_id')->unsigned();
            $table->unsignedBigInteger('tournament_id')->unsigned();
            $table->tinyInteger('first_team_score')->unsigned()->nullable();
            $table->tinyInteger('second_team_score')->unsigned()->nullable();
            $table->unsignedBigInteger('season_id');
            $table->string('tour');
            $table->unsignedTinyInteger('status')->default(1);
            $table->timestamp('date');
            $table->timestamps();

            $table->foreign('first_team_id')->references('id')->on('teams')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('second_team_id')->references('id')->on('teams')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('tournament_id')->references('id')->on('tournaments')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('season_id')->references('id')->on('seasons')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('games');
    }
}
