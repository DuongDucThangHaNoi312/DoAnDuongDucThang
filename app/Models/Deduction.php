<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class Deduction extends Model
{
    protected $table = 'deductions';
    protected $fillable = [
        'month',
        'year',
        'created_by',
        'user_id',
        'type',
        'department_id'
    ];

    public function detailDeduction()
    {
        return $this->hasMany(DetailDeduction::class);
    }

    public function totalTax()
    {
        return $this->hasMany(DetailDeduction::class)->where('type', 'CHIU_THUE');
    }

    public function totalNonTax()
    {
        return $this->hasMany(DetailDeduction::class)->where('type', 'MIEN_THUE');
    }


    public static function category()
    {
        $category = Adjustment::where('type', 2)->where('action', 1)->get();
		return Arr::pluck($category->toArray(), 'title', 'id');
    }
}
