<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $table = 'logs';
    protected $fillable = ['data_new', 'data_old', 'action_by', 'action_at', 'field', 'log_id', 'log_type', 'note', 'staff_id', 'timekeeping_id', 'key'];

    public function logable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'action_by');
    }
}
