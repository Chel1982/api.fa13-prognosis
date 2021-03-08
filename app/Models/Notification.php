<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    public function userFrom()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function game()
    {
        return $this->belongsTo(Game::class);
    }
}
