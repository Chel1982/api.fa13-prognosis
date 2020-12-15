<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTeamTournamentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('team_tournaments', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedBigInteger('team_id');
            $table->unsignedBigInteger('tournament_id');
            $table->unsignedBigInteger('season_id');
            $table->timestamps();

            $table->foreign('team_id')->references('id')->on('teams')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('tournament_id')->references('id')->on('tournaments')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('season_id')->references('id')->on('seasons')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('team_tournaments');
    }
}
