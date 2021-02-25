<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    public function teamTournaments()
    {
        return $this->hasMany(TeamTournament::class);
    }

    public function userFa13email()
    {
        return $this->belongsTo(UserFa13Email::class, 'user_fa13_email_id');
    }
}
