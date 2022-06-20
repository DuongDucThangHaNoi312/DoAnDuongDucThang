<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class SalaryDeclaration extends Model
{
    protected $table = 'salary_declarations';
    protected $fillable = [
        'department_group_id',
        'company_id',
        'month',
        'year',
        'created_by',
        'tp_approved_by',
        'tp_approved_date',
        'kt_approved_by',
        'kt_approved_date',
        'type_declaration',
        'declaration_main',
        'declaration_branch',
        'declaration_self',
        'month_year',
        'user_mo_tk',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }
    
    public function departmentGroup()
    {
        return $this->belongsTo(DepartmentGroup::class, 'department_group_id', 'id');
    }
    
    public function user()
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
  
    public function point()
    {
        return $this->belongsTo(TypeDeclaration::class, 'type_declaration', 'name');
    }
}
