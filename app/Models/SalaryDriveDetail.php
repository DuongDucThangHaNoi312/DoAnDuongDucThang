<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class SalaryDriveDetail extends Model
{
    protected $table = 'salary_drives_detail';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function salaryDrive()
    {
        return $this->belongsTo(SalaryDrive::class);
    }
}
