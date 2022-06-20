<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserTeam extends Model
{
    protected $table = 'user_team';
    protected $fillable = [
        'team_id',
        'user_id',
    ];
}
