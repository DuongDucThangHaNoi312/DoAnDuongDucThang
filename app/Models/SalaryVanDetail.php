<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class SalaryVanDetail extends Model
{
    protected $table = 'salary_van_details';
    protected $fillable = [
        'salary_van_id',
        'job_id',
        'contractual_wages_1',
        'contractual_wages_2',
        'contractual_wages_3',
        'wharf_1',
        'wharf_2',
        'wharf_3',
        'monthly_ticket',
        'parking_fee',
        'meal_allowance',
        'total_contractual_wages',
        'total_wharf',
        'total',
        'user_id',
        'license_plates',
        'department_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
