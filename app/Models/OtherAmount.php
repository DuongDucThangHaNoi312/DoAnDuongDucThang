<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtherAmount extends Model
{
    protected $table = 'other_amounts';
    protected $fillable = [
        'payroll_user_id',
        'name',
        'money',
        'type',
        'created_by'
    ];

    public static function getOtherAmounts($id, $type)
    {
        return OtherAmount::where('payroll_user_id', $id)->where('type', $type)->get();
    }
}
