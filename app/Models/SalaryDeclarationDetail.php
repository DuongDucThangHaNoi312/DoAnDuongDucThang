<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryDeclarationDetail extends Model
{
    protected $table = "salary_declaration_details";
    protected $fillable = [
        'user_id',
        'month',
        'year',
        'department_group_id',
        'company_id',
        'money',
        'month_year',
        'created_by',
        'ratio',
        'note',
    ];
}
