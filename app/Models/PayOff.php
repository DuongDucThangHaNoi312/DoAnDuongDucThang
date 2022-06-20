<?php

namespace App\Models;

use App\User;
use Illuminate\Support\Arr;
use App\Models\Adjustment;
use Illuminate\Database\Eloquent\Model;

class PayOff extends Model
{
    protected $table = 'payoffs';
    protected $fillable = [
        'content',
        'amount_money_non_tax',
        'amount_money_tax',
        'user_id',
        'created_by',
        'month',
        'year',
        'category',
        'note',
        'department_id',
        'type',
        'check'
    ];

    public function createdByPayOff()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function adjustment()
    {
        return $this->belongsTo(Adjustment::class, 'category', 'id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
