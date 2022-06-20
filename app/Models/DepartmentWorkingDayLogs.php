<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepartmentWorkingDayLogs extends Model
{
    protected $table = 'department_working_day_logs';
    protected $fillable = ['id','work_day_id','field','old_data','new_data','note','action_by','action_at'];
    public $timestamps = false;

}
