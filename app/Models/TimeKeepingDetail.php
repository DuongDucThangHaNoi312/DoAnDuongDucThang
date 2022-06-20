<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Staff;
use App\User;

class TimeKeepingDetail extends Model
{
    protected $table = 'timekeeping_detail';
    protected $fillable = [
        'code',
        'detail',
        'timekeeping_id',
        'created_by',
        'staff_id',
        'total'
    ];

    public function timekeeping()
    {
        return $this->belongsTo(TimeKeeping::class);
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id', 'id');
    }

    public function logs()
    {
        return $this->morphMany('App\Models\Log', 'log');
    }

    public static function teamTimeKeeping($team)
    {
        return TimeKeepingDetail::whereIn('staff_id', $team)->with('timekeeping')->get()->groupBy('timekeeping.id');
    }
}