<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExtraTimeScoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('extra_time_scores', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('first_team_score')->nullable();
            $table->unsignedTinyInteger('second_team_score')->nullable();
            $table->unsignedBigInteger('game_id');
            $table->timestamps();

            $table->foreign('game_id')->references('id')->on('games')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('extra_time_scores');
    }
}
