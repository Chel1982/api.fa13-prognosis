<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePressConferencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('press_conferences', function (Blueprint $table) {
            $table->id();
            $table->text('press_conference');
            $table->unsignedBigInteger('first_team_id')->nullable();
            $table->unsignedBigInteger('second_team_id')->nullable();
            $table->unsignedBigInteger('game_id');
            $table->timestamp('date');
            $table->timestamps();


            $table->foreign('first_team_id')->references('id')->on('teams')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('second_team_id')->references('id')->on('teams')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::drop('press_conferences');
    }
}
