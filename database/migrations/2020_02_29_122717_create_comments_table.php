<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id('id');
            $table->text('comment');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('game_id')->nullable();
            $table->unsignedBigInteger('press_conference_id')->nullable();
            $table->timestamps();

            $table->nestedSet();

            $table->foreign('user_id')->references('id')->on('users')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('game_id')->references('id')->on('games')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('press_conference_id')->references('id')->on('press_conferences')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('comments');
    }
}
