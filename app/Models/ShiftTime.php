<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class ShiftTime extends Model
{
    protected $table = 'shift_times';
    protected $fillable = [
        'company_id',
        'department_id',
        'category_shift_id',
        'time_in',
        'time_out',
        'off_mid_shift',
        'start_mid_shift',
        'limit_time_in',
        'limit_time_out',
        'created_by',
        'update_by'
    ];

    public static function rules()
    {
        return [
            'company_id' => 'required|max:10',
            'department_id' => 'required|max:10',
            'category_shift_id' => 'required|max:10',
            'time_in' => 'required|date_format:"H:i"',
            'time_out' => 'required|date_format:"H:i"',
            'off_mid_shift' => 'required|date_format:"H:i"',
            'start_mid_shift' => 'required|date_format:"H:i"',
            'limit_time_in' => 'required|date_format:"H:i"',
            'limit_time_out' => 'required|date_format:"H:i"',
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

    public function category()
    {
        return $this->belongsTo(CategoryShift::class, 'category_shift_id', 'id');
    }

    public function updateBy()
    {
        return $this->belongsTo(User::class, 'update_by', 'id');
    }
}
