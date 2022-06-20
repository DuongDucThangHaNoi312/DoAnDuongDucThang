<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class PayrollUser extends Model
{
    protected $table = 'payroll_user';

    public function staff()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }

    public function logs()
    {
        return $this->morphMany('App\Models\Log', 'log');
    }

    public function otherAmounts()
    {
        return $this->hasMany(OtherAmount::class);
    }

    public function timekeepingDetail()
    {
        return $this->belongsTo(TimeKeepingDetail::class, 'timekeeping_id', 'id');
    }
}
