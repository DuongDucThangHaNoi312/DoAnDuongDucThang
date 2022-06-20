<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContractFile extends Model
{
    protected $fillable = ['user_id', 'contract_id', 'name', 'path', 'status'];

	public static function rules()
	{
		return [
//			'file' => 'required',
			'file.*' => 'mimes:doc,pdf,docx,txt,zip'
		];
    }
}
