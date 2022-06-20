<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Newborn extends Model
{
    protected $table = 'newborns';
    protected $fillable = [
        'user_id',
        'start',
        'end',
        'time',
        'note',
        'type',
        'created_by',
        'deleted_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
