<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepartmentDayOffLogs extends Model
{
    protected $table = 'department_day_off_logs';
    protected $fillable = ['id','day_off_id','field','old_data','new_data','note','action_by','action_at'];
    public $timestamps = false;

}
