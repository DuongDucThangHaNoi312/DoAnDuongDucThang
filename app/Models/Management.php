<?php

namespace App;

use Illuminate\Support\Facades\Cache;

class Management extends \Eloquent {

    protected $table = 'managements';

    public static function rules( $id = 0 ) {
        return [
            'code'                      => 'required|max:10|regex:/^[a-zA-Z0-9_]+([-.][a-zA-Z0-9_]+)*$/|unique:banks,code' . ($id == 0 ? '' : ',' . $id),
            'name'                      => 'required|max:100',
            'logo'                      => 'max:2048|mimes:jpg,jpeg,png,gif' . ($id == 0 ? '|required': ''),
            'gateway'                   => 'required|integer',
            'type'                      => 'required|integer',
            'status'                    => 'integer',
            'fee_fixed'                 => 'required|integer',
            'fee_percent'               => 'required|between:0,99.99'
        ];
    }

	protected $fillable = [ 'code', 'name', 'logo', 'status', 'fee_fixed', 'fee_percent', 'raw_fee_fixed', 'raw_fee_percent', 'gateway_id', 'type', 'created_by', 'qr_code', 'is_partner' ];

    public static function boot()
    {
        parent::boot();

        static::updated(function($bank)
        {
            self::clearCache();
        });

        static::created(function($bank)
        {
            self::clearCache();
        });

        static::deleted(function($bank)
        {
            self::clearCache();
        });

        static::saved(function($bank)
        {
            self::clearCache();
        });
    }

    public function gateway()
    {
        return $this->belongsTo('\App\Gateway');
    }

    public function orders()
    {
        return $this->hasMany('\App\Order', 'bank_code', 'code');
    }

    public static function getByAll()
    {
        $banks = [];
        if (!Cache::has('banks_all')) {
            $banks = Bank::where('status', 1)->select('code', 'name', 'logo', 'fee_fixed', 'fee_percent', 'gateway_id', 'type')->orderBy('type')->get();
            if (!$banks->count())
                return [];
            $banks = json_encode($banks);
            if ($banks) Cache::forever('banks_all', $banks);
        } else {
            $banks = Cache::get('banks_all');
        }
        return json_decode($banks, 1);
    }

    public static function getByGateway($gateId, $isQRCode = 0)
    {
        $banks = [];
        if (!Cache::tags('bank_by_gateway')->has('gt_' . $gateId . '_' . $isQRCode)) {
            if ($isQRCode) {
                $banks = Bank::where('qr_code', 1)->where('gateway_id', $gateId)->select('code', 'name', 'logo', 'fee_fixed', 'fee_percent', 'gateway_id', 'type')->orderBy('type')->get();
            } else {
                $banks = Bank::where('status', 1)->where('gateway_id', $gateId)->select('code', 'name', 'logo', 'fee_fixed', 'fee_percent', 'gateway_id', 'type')->orderBy('type')->get();
            }
            if (!$banks->count()) return [];
            $banks = json_encode($banks);
            if ($banks) Cache::tags('bank_by_gateway')->forever('gt_' . $gateId . '_' . $isQRCode, $banks);
        } else {
            $banks = Cache::tags('bank_by_gateway')->get('gt_' . $gateId . '_' . $isQRCode);
        }
        return json_decode($banks, 1);
    }

    public static function getByCode($del = 0, $time = 86400) //in 24h = 3600*24=86400)
    {
        $redis      = \App::make('redis');
        if( $del ) $redis->del("banks_by_code");
        $banks = $redis->get("banks_by_code");

        if ( !$banks ) {
            $banks = Bank::where('status', '<>', -1)->get();
            $banks = $banks->keyBy('code');
            // $banks = $banks->getDictionary();
            $redis->set( "banks_by_code", json_encode($banks) );
            $redis->expire( "banks_by_code", $time);
            $banks = json_encode($banks);
        }

        $banks = json_decode( $banks, 1 );
        return $banks;
    }

    public static function updateFeeByGateway($gateway)
    {
        $banks = Bank::where('gateway_id', $gateway->id)->get();
        foreach ($banks as $bank) {
            if ($bank->type == Define\Bank::TYPE_INTERNAL) {
                $bank->raw_fee_fixed    = $gateway->fee_internal_fixed;
                $bank->raw_fee_percent  = $gateway->fee_internal_percent;
                $bank->save();
                BankAmount::where('bank_code', $bank->code)->where('gateway_id', $gateway->id)
                    ->update([ 'raw_fee_fixed' => $gateway->fee_internal_fixed, 'raw_fee_percent' => $gateway->fee_internal_percent ]);
            } elseif ($bank->type == Define\Bank::TYPE_EXTERNAL) {
                $bank->raw_fee_fixed    = $gateway->fee_external_fixed;
                $bank->raw_fee_percent  = $gateway->fee_external_percent;
                $bank->save();
                BankAmount::where('bank_code', $bank->code)->where('gateway_id', $gateway->id)->update([ 'raw_fee_fixed' => $gateway->fee_external_fixed, 'raw_fee_percent' => $gateway->fee_external_percent ]);
            }
        }
    }

    public static function clearCache()
    {
    }
}