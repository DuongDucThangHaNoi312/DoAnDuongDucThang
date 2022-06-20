<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class SalaryChooseContDetail extends Model
{
    protected $table = 'salary_choose_cont_details';
    protected $fillable = [
        'user_id',
        'department_id',
        'company_id',
        'month',
        'year',
        'money',
        'created_by',
        'tp_approved_by',
        'tp_approved_date',
        'kt_approved_by',
        'kt_approved_date',
        'id_salary_choose_cont',
        'type_cost',
        'customer_id',
        'cont_no',
        'booking_detail'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }
    
    public function deparment()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    
    public function customer()
    {
        return $this->belongsTo(Partner::class, 'customer_id', 'id')->where('is_customer', 1);
    }

    public static function boot() {
        parent::boot();
        
        // static::created(function($model) {
        //         $model->create([
        //             'type_cost' => 'CK16 - CP Chọn Vỏ',
        //         ]);
        // });
    }

}
