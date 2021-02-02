<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    public function pressConferences()
    {
        return $this->hasMany(PressConference::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }

    public function firstTeam()
    {
        return $this->belongsTo(Team::class, 'first_team_id');
    }

    public function secondTeam()
    {
        return $this->belongsTo(Team::class, 'second_team_id');
    }

    public function videoSource()
    {
        return $this->hasOne(VideoSource::class);
    }

    public function textSource()
    {
        return $this->hasOne(TextSource::class);
    }
}
