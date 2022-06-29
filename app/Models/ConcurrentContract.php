<?php

namespace App\Models;

use App\Position;
use App\Qualification;
use App\User;
use Illuminate\Database\Eloquent\Model;

class ConcurrentContract extends Model
{
	protected $guarded = [];
	protected $dates = ['valid_from', 'valid_to'];

	public static function rules($contract_id, $id = null)
	{
		return [
			// 'company_id' => 'required|unique:concurrent_contracts,company_id,' . $id . ',id,contract_id' .($contract_id == null ? '' : ',' . $contract_id),
			'company_id' => 'required',
			'department_id' => 'required',
			'position_id' => 'required',
			'qualification_id' => 'required',
			'salary' => 'min:0',
			'valid_from' => 'required',
			'valid_to' => 'required',
		];
	}

	public function contract()
	{
		return $this->belongsTo(Contract::class);
	}

	public function setValidFromAttribute($value)
	{
		$this->attributes['valid_from'] = date("Y-m-d", strtotime(str_replace('/', '-', $value)));
	}

	public function setValidToAttribute($value)
	{
		$this->attributes['valid_to'] = !empty($value) ? date("Y-m-d", strtotime(str_replace('/', '-', $value))) : null;
	}

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function qualification()
    {
        return $this->belongsTo(Qualification::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
