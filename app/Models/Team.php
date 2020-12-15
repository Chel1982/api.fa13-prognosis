<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    public function teamTournaments()
    {
        return $this->hasMany(TeamTournament::class);
    }
}
