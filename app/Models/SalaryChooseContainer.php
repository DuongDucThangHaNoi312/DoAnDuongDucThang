<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class SalaryChooseContainer extends Model
{
    protected $table = 'salary_choose_conts';
    protected $fillable = [
        'department_id',
        'company_id',
        'month',
        'year',
        'created_by',
        'tp_approved_by',
        'tp_approved_date',
        'kt_approved_by',
        'kt_approved_date',
        'total_money',
        'total_user',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }
    
    public function deparment()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }
    
    public function salaryChooseContDetail()
    {
        return $this->hasMany(SalaryChooseContDetail::class, 'id_salary_choose_cont', 'id');
    }
    
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function tpApproved()
    {
        return $this->belongsTo(User::class, 'tp_approved_by', 'id');
    }
   
    public function ktApproved()
    {
        return $this->belongsTo(User::class, 'kt_approved_by', 'id');
    }
}
