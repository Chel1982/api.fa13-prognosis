<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function teams()
    {
        return $this->belongsTo(Team::class);
    }
}
