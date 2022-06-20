<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppendixAllowance extends Model
{
    protected $guarded = [];
    protected $dates = ['valid_from', 'valid_to'];

    public function contract()
	{
		return	$this->belongsTo(Contract::class);
	}

	public function setValidFromAttribute($value)
	{
		$this->attributes['valid_from'] = date("Y-m-d 00:00:00", strtotime(str_replace('/', '-', $value)));
	}

	public function setValidToAttribute($value)
	{
		$this->attributes['valid_to'] = !empty($value) ? date("Y-m-d 23:59:59", strtotime(str_replace('/', '-', $value))) : null;
	}

	public function category()
	{
		return $this->belongsTo(AllowanceCategory::class, 'allowance_id');	
	}

    public static function randomCode()
    {
        $latest_trx_no = AppendixAllowance::max('id');
        if(is_null($latest_trx_no)) {
            $latest_trx_no = 1;
        };
        $new_trx_no         = $latest_trx_no + 1;
        $firstTrx_no        = "PKT";
        switch (strlen($new_trx_no)) {
            case 1:
                $trx_no = $firstTrx_no."000000".$new_trx_no;
                break;
            case 2:
                $trx_no = $firstTrx_no."00000".$new_trx_no;
                break;
            case 3:
                $trx_no = $firstTrx_no."0000".$new_trx_no;
                break;
            case 4:
                $trx_no = $firstTrx_no."000".$new_trx_no;
                break;
            case 5:
                $trx_no = $firstTrx_no."00".$new_trx_no;
                break;
            case 6:
                $trx_no = $firstTrx_no."0".$new_trx_no;
                break;
            case 7:
                $trx_no = $firstTrx_no.$new_trx_no;
                break;
        }
        return $trx_no;
    }
	
}
