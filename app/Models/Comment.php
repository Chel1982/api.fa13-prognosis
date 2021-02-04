<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;

class Comment extends Model
{
    use NodeTrait;

    public function user(){
        return $this->belongsTo(User::class);
    }
}
