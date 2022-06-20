<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appendix extends Model
{
	protected $fillable = ['name', 'expense', 'description', 'status'];

	public static function rules($id = 0)
	{
		return [
			'name' => 'required|unique:appendixes,name' . ($id == 0 ? '' : ',' . $id),
			'expense' => 'required|integer',
			'status' => 'required'
		];
    }

	public static function getAppendixesForOption()
	{
		return Appendix::pluck('name', 'id')->toArray();
	}
}
