<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class SalaryDrive extends Model
{
    protected $table = 'salary_drives';
    protected $fillable = [
        'company_id',
        'month',
        'year',
        'created_by',
        'title',
        'type',
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

    public function salaryDriveDetail()
    {
        return $this->hasMany(SalaryDriveDetail::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by', 'id');
    }

    public static function handleDataExcel($params)
    {
        $m = $params['month'];
        $y = $params['year'];
        $companyId = $params['company_id'];

        $salaryDriverIds = SalaryDrive::where('month', $m)
            ->where('year', $y)
            ->pluck('id')
            ->toArray();
        if (count($salaryDriverIds) == 0) return [];
        $deptData = $params['dept'];
        if (!$deptData || count($deptData) == 0) return [];
        $listDeptIds = array_keys($deptData);
        $salaryDetails = SalaryDriveDetail::whereIn('salary_drive_id', $salaryDriverIds)
            ->whereIn('department_id', $listDeptIds)
            ->get()
            ->groupBy('department_id');
        return ['detail' => $salaryDetails, 'dept' => $deptData];
    }
}
