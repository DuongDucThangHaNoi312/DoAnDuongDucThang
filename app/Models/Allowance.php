<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class Allowance extends Model
{
    protected $guarded = [];

	public static function rules($id = 0)
	{

		return [
			'category_id' => 'required|unique:allowances,category_id,NULL,id,contract_id,' . $id,
		];
    }
 
    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function allowanceCategory()
    {
        return $this->belongsTo(AllowanceCategory::class, 'category_id');
    }
}
