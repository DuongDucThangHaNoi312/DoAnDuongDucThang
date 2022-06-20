<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class SalaryVan extends Model
{
    protected $table = 'salary_vans';
    protected $fillable = [
        'company_id',
        'month',
        'year',
        'time_1',
        'time_2',
        'time_3',
        'cp_1',
        'cp_2',
        'cp_3',
        'created_by',
        'title',
        'approved',
        'approved_by',
        'approved_date',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function user_by()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function salaryDetail()
    {
        return $this->hasMany(SalaryVanDetail::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by', 'id');
    }
}
