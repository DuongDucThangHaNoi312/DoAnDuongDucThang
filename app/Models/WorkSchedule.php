<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class WorkSchedule extends Model
{
    protected $table = 'work_schedule';
    protected $fillable = [
        'company_id',
        'department_id',
        'from_morning',
        'to_morning',
        'from_afternoon',
        'to_afternoon',
        'from_sa_morning',
        'to_sa_morning',
        'from_sa_afternoon',
        'to_sa_afternoon',
        'created_by',
        'deleted_by',
        'type',
        'ot',
        'update_by',
    ];

    public static function rules()
    {
        return [
            'company_id' => 'required|max:10',
            'department_id' => 'required|max:10',
            'from_morning' => 'required|date_format:"H:i"',
            'to_morning' => 'required|date_format:"H:i"|after:from_morning',
            'from_afternoon' => 'required|date_format:"H:i"',
            'to_afternoon' => 'required|date_format:"H:i"|after:from_morning',
            'ot' => 'required|date_format:"H:i"',
        ];
    }

    public static function rules1()
    {
        return [
            'company_id' => 'required|max:10',
            'department_id' => 'required|max:10',
            'shift1_in' => 'required|date_format:"H:i"',
            'shift1_out' => 'required|date_format:"H:i"|after:shift1_in',
            'shift2_in' => 'required|date_format:"H:i"',
            'shift2_out' => 'required|date_format:"H:i"|after:shift2_in',
            'shift3_in' => 'required|date_format:"H:i"',
            'shift3_out' => 'required|date_format:"H:i"',
        ];
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function updateBy()
    {
        return $this->belongsTo(User::class, 'update_by', 'id');
    }
}
