<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class AllowanceCategory extends Model
{
	protected $fillable = ['name', 'name_es', 'status', 'type', 'desc', 'is_social_security', 'is_exemp', 'ot', 'company_id', 'department'];
	const categoryTypeKpi = 1;

	public static function rules($id = 0)
	{
		return [
			'name' => 'required|unique:allowance_categories,name' . ($id == 0 ? '' : ',' . $id),
		];
	}

    public function allowances()
    {
        return $this->hasMany(Allowance::class, 'category_id');
    }
	public function appendix_allowances()
	{
		return $this->hasMany(AppendixAllowance::class, 'allowance_id');
	}

	public static function allowanceCategoryHasKpi()
	{
		return AllowanceCategory::where('type', self::categoryTypeKpi)->pluck('id');
	}

	public function company()
	{
		return $this->belongsTo(Company::class);
	}

	public static function cateAllowance()
    {
        $cate_allowances = AllowanceCategory::whereNotIn('id', [1])->get();
		return Arr::pluck($cate_allowances->toArray(), 'name', 'id');
    }

    public static function keyAllowanceById()
    {
        return [
            2 => 'di_lai',
            3 => 'trach_nhiem',
            4 => 'cong_hien',
            5 => 'nang_suat',
            6 => 'dien_thoai',
            7 => 'cong_viec',
            8 => 'dac_thu',
            9 => 'khac',
            10 => 'chuyen_can'
        ];
    }
}
