<?php

namespace App\Models;

use App\Define\Timekeeping as DefineTimekeeping;
use Illuminate\Database\Eloquent\Model;
use App\User;
use Illuminate\Support\Facades\DB;
use Mpdf\Tag\Time;

class TimeKeeping extends Model
{
    const NGAY_THUONG = 'NGAY_THUONG';
    const NGAY_NGHI = 'NGAY_NGHI';
    const NGAY_LE = 'NGAY_LE';
    const NGHI_LAM = 0;

    protected $table = 'timekeepings';
    protected $fillable = [
        'month',
        'year',
        'company_id',
        'department_id',
        'created_by',
        'ids',
        'status',
        'user_approved',
        'date_approved',
        'user',
        'version'
    ];

    public static function rules()
    {
        return [
            'company_id' => 'required|max:10',
            'department_id' => 'required|max:10',
            'month' => 'required|max:10',
            'year' => 'required|max:10',
        ];
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function user_by()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function timeKeepingDetail()
    {
        return $this->hasMany(TimeKeepingDetail::class, 'timekeeping_id', 'id');
    }

    public static function getTimekeeping($id)
    {
        return TimeKeeping::find(intval($id));
    }

    public function userApproved()
    {
        return $this->belongsTo(User::class, 'user_approved', 'id');
    }

    public static function tongHop($timekeepingDetailId, $typeDepartment = null, $total_day_request, $otherData = [])
    {
        $return = [];
        if (0&&isset($otherData['timekeeping_detail']) && $otherData['timekeeping_detail'])
            $timekeepingDetail = $otherData['timekeeping_detail'];
        else
            $timekeepingDetail = TimeKeepingDetail::find(intval($timekeepingDetailId));
        $detail = json_decode($timekeepingDetail->detail, true);
        $congTv = $congMain = [];
        if ($otherData['start_date'] && $otherData['end_date'] && $otherData['is_main'] != 1) {
            $startTz = strtotime($otherData['start_date'] .' 00:00:00');
            $startMainTz = strtotime($otherData['start_date_main'] .' 00:00:00');
            $endTz = strtotime($otherData['end_date'] .' 00:00:00');
            foreach ($detail as $tz => $v) {
                if ($tz < $startTz || $tz >= $endTz) unset($detail[$tz]);
                else {
                    if ($otherData['start_date_main']) {
                        if ($tz < $startMainTz)
                            $congTv[$tz] = $v;
                        else
                            $congMain[$tz] = $v;
                    }
                }
            }
            $congTv = collect($congTv);
            $congMain = collect($congMain);
        } else {
            if ($otherData['is_main'] == 1)
                $congTv = $congMain = collect($detail);
            else {
                $congMain = collect($congMain);
                $congTv = collect($congTv);
            }
        }
        //if ($otherData['contract_id'] == 7208) dd($congTv, $startMainTz, $otherData['start_date_main']);
        $cong = collect($detail);

        $code_day_offs = DefineTimekeeping::codeDayOff();
        if ($otherData['start_date_main']) {
            foreach ($code_day_offs as $name => $code) {
                $t = $t_nua = $t_ = 0;
                $t = $congMain->where('day_off', $code)->count();
                $t_nua = $congMain->filter(function ($i) use ($code) {
                    return false !== stristr($i['day_off'], ($code.'/2'));
                })->count();
                $t_ = $congMain->where('day_off', 'T/2 T/2')->count();
                if ($name == 'nghi_cong_tac' && $t_ > 0) {
                    $return[$name] = $t + ($t_nua * 0.5) + ($t_ * 0.5);
                } else {
                    $return[$name] = $t + ($t_nua * 0.5);
                }
            }
            $h = $congTv->where('day_off', 'H')->count();
            $h_nua = $congTv->filter(function ($i) {
                return false !== stristr($i['day_off'], ('H/2'));
            })->count();
            $return['nghi_le_tv'] = $h + $h_nua*0.5;
            $return['nghi_le_hd'] = ($otherData['is_main'] == 1) ? 0 : $return['nghi_le'];
            $return['nghi_le'] = $return['nghi_le_tv'] + $return['nghi_le_hd'];
        } else {
            foreach ($code_day_offs as $name => $code) {
                $t = $t_nua = $t_ = 0;
                $t = $cong->where('day_off', $code)->count();
                $t_nua = $cong->filter(function ($i) use ($code) {
                    return false !== stristr($i['day_off'], ($code.'/2'));
                })->count();
                $t_ = $cong->where('day_off', 'T/2 T/2')->count();
                if ($name == 'nghi_cong_tac' && $t_ > 0) {
                    $return[$name] = $t + ($t_nua * 0.5) + ($t_ * 0.5);
                } else {
                    $return[$name] = $t + ($t_nua * 0.5);
                }
            }
        }

        $return['total'] = array_sum(array_column($detail, 'total'));
        $return['total_hd'] = $cong->where('contract_type', 'HOP_DONG')->sum('total_work');
        //if ($otherData['contract_id'] == 534) dd($cong->toArray(), $congTv->toArray(), $congMain->toArray(), $return, $otherData);

        $return['total_tv'] = $congTv->where('contract_type', 'THU_VIEC')->sum('total_work');

        $return['total_hd_tv'] = $congTv->where('contract_type', 'THU_VIEC')->sum('total');


        if ($typeDepartment == \App\Define\Department::DECLARATION_OFFICE) {
            $return['shift_day'] = $cong->where('shift_type', 'NGAY')->sum('total_work');
            $return['shift_hc'] = $cong->where('shift_type', 'HANH_CHINH')->sum('total_work');
            $return['shift_night'] = $cong->where('shift_type', 'DEM')->sum('total_work');

            $return['dem_tv'] = $cong->where('shift_type', 'DEM')->where('contract_type', 'THU_VIEC')->sum('total_work');
            $return['dem_hd'] = $cong->where('shift_type', 'DEM')->where('contract_type', 'HOP_DONG')->sum('total_work');

            $return['ngay_tv'] = $cong->whereIn('shift_type', ['NGAY', 'HANH_CHINH'])->where('contract_type', 'THU_VIEC')->sum('total_work');
            $return['ngay_hd'] = $cong->whereIn('shift_type', ['NGAY', 'HANH_CHINH'])->where('contract_type', 'HOP_DONG')->sum('total_work');
            //if ($otherData['contract_id'] == 534) dd($cong->toArray(),$return, $otherData);
            $an_chinh_ngay_di_lam = $cong->where('total', 1)->count();
            $an_chinh_ngay_thuong = $cong->where('type_ot', self::NGAY_THUONG)->where('total_ot', '>=', 6)->count();
            $an_chinh_nua_cong = $cong->whereIn('type_ot', [self::NGAY_NGHI, self::NGAY_LE])->where('total_work', 0.5)->where('total_ot', '>', 0)->count();
            $an_chinh_ngay_nghi = $cong->whereIn('type_ot', [self::NGAY_NGHI, self::NGAY_LE])->where('status', self::NGHI_LAM)->where('total_ot', '>', 4)->count();

            $an_phu_ngay_thuong = $cong->where('type_ot', self::NGAY_THUONG)->where('total_ot', '>=', 3)->where('total_ot', '<', 6)->count();
            $an_phu_nua_cong = $cong->whereIn('type_ot', [self::NGAY_NGHI, self::NGAY_LE])->where('total_work', 0.5)->where('total_ot', '>=', 7)->count();
            $an_phu_ngay_nghi = $cong->whereIn('type_ot', [self::NGAY_NGHI, self::NGAY_LE])->where('status', self::NGHI_LAM)->where('total_ot', '>=', 11)->count();

            $return['an_chinh'] = $an_chinh_ngay_di_lam + $an_chinh_ngay_thuong + $an_chinh_nua_cong + $an_chinh_ngay_nghi;
            $return['an_phu'] = $an_phu_ngay_thuong + $an_phu_nua_cong + $an_phu_ngay_nghi;

        } else {
            $return['ngay_tv'] = $return['total_tv'];
            $return['ngay_hd'] = $return['total_hd'];
            $return['dem_tv'] = 0;
            $return['dem_hd'] = 0;
            $return['an_chinh'] = 0;
            $return['an_phu'] = 0;
        }
        $return['nghi_khong_luong'] = max(($total_day_request - $return['total'] - $return['nghi_70_luong'] - $return['nghi_om']), 0);
        $return['total_day_request'] = $total_day_request;
        return $return;
    }

    public static function configOt($company_id)
    {
        return DB::table('config_ot')->where('company_id', $company_id)->pluck('value', 'type');
    }

    public static function getStatusContract($typeStatus)
    {
        $statusContract = '';
        if ($typeStatus == \App\Defines\Contract::ACTIVE) {
            $statusContract = 'DANG_HOAT_DONG';
        } else if ($typeStatus == \App\Defines\Contract::TRANSFER) {
            $statusContract = 'DIEU_CHUYEN';
        } else if ($typeStatus == \App\Defines\Contract::CHO_NGHI_VIEC) {
            $statusContract = 'CHO_NGHI_VIEC';
        }
        return $statusContract;
    }
}