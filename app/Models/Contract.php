<?php

namespace App\Models;

use App\Defines\Schedule;
use App\Defines\Staff;
use App\User;
use App\Position;
use Carbon\Carbon;
use App\Qualification;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    protected $table='contracts';
    protected $fillable = ['currency_code', 'created_by', 'department_group_id', 'report_valid', 'staff_submit_date', 'transfer_from', 'transfer_to', 'appoint_from',  'appoint_to', 'desc_qualification', 'type_status', 'staff_id','title_id', 'user_id', 'code', 'qualification_id', 'company_id', 'department_id', 'position_id', 'type', 'status', 'is_main', 'basic_salary', 'valid_from', 'valid_to', 'dependent_person', 'set_notvalid_by', 'set_notvalid_on', 'set_valid_by', 'set_valid_on', 'is_used'];
    protected $dates = ['valid_from', 'valid_to'];

    public static function rules($id = null)
    {
        return [
            'user_id' => 'required',
            'code' => 'required|unique:contracts,code' . ($id == null ? '' : ',' . $id),
            'company_id' => 'required',
            'department_id' => 'required',
            'qualification_id' => 'required',
            'position_id' => 'required',
            'status' => 'required',
            'valid_from' => 'required',
//            'basic_salary' => 'min:0',
        ];
    }

    public function setValidFromAttribute($value)
    {
        $this->attributes['valid_from'] = date("Y-m-d", strtotime(str_replace('/', '-', $value)));
    }

    public function setValidToAttribute($value)
    {
        $this->attributes['valid_to'] = !empty($value) ? date("Y-m-d", strtotime(str_replace('/', '-', $value))) : null;
    }

    public function setBasicSalaryAttribute($value)
    {
        $this->attributes['basic_salary'] = str_replace(',', '', $value);
    }

    public function getValidFromDateAttribute()
    {
        return $this->valid_from->format('d/m/Y');
    }

    public function allowances()
    {
        return $this->hasMany(Allowance::class);
    }

//    public function staff()
//    {
//        return $this->belongsTo(Staff::class);
//    }

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
   
    public function departmentGroup()
    {
        return $this->belongsTo(DepartmentGroup::class);
    }

//    public function title()
//    {
//        return $this->belongsTo(StaffTitle::class);
//    }

    public function appendixAllowances()
	{
		return $this->hasMany(AppendixAllowance::class)->where('allowance_active', 1);
	}

    public function appendixAllowances3()
    {
        return $this->hasMany(AppendixAllowance::class);
    }

	public function concurrentContracts()
	{
		return $this->hasMany(ConcurrentContract::class);
	}

	public function getCheckValidAttribute()
	{
		$today = now()->timestamp;
		if ($this->set_notvalid_on) {
			if($today >= strtotime($this->set_notvalid_on)) return \App\Defines\Contract::NOT_VALID; //1
		}
		if($today < strtotime($this->valid_from)) return \App\Defines\Contract::NOT_YET_VALID; //2
		return \App\Defines\Contract::IS_VALID; //3
	}

	public static function checkStaffHasActiveContract($contractId) //e
	{
		$currentContract = Contract::find($contractId);
		$contract = Contract::where('user_id', $currentContract->user_id)
            ->where('id', '<>', $contractId)
            ->where('type_status', \App\Defines\Contract::ACTIVE)
            ->first();
		if (!$contract) return false;
        return true;
	}

	public static function setActiveAllowance($contractId, $allowanceId, $code)
	{
		$appendixes = AppendixAllowance::where('contract_id', $contractId)->where('code', '!=', $code)->where('allowance_id', $allowanceId)->update(['allowance_active' => 0]);
		if (!$appendixes) {
			Allowance::where('contract_id', $contractId)->where('category_id', $allowanceId)->update(['active' => 0]);
		}
	}

	public static function setActiveSalary($contractId, $code)
	{
		$appendixes = AppendixAllowance::where('contract_id', $contractId)->where('code', '!=', $code)->update(['salary_active' => 0]);
	}

	public static function getSeniority($staffId, $year = null) {
        $year = $year ?? now()->year;
    	$date = Carbon::createFromDate($year, 12,31);
    	$staffStart = User::find(intval($staffId))->staff_start;
    	if (!$staffStart) return 0;
    	return round(($date->diff(Carbon::parse($staffStart))->days + 1)/365, 1);
	}

    public static function getCountAddLeave($staffId, $year = null)
    {
        return intval(self::getSeniority($staffId, $year)/5);
    }

	public static function getStaffBySeniority($departmentId = null)
	{
		$users = $departmentId ? User::where('department_id', $departmentId)->get('id') : User::where('active', 1)->get('id');
		$arr = [0, 0, 0, 0, 0];
		foreach ($users as $user) {
			$temp = self::getSeniority($user->id);
			if ($temp < 2) $arr[0] += 1;
			elseif ($temp >= 2 && $temp < 4) $arr[1] += 1;
			elseif ($temp >= 4 && $temp < 7) $arr[2] += 1;
			elseif ($temp >= 7 && $temp < 10) $arr[3] += 1;
			else $arr[4] += 1;
		}
		return $arr;
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function qualification()
	{
		return $this->belongsTo(Qualification::class);
	}

	public function allowanceCategories()
	{
		return $this->belongsToMany(AllowanceCategory::class, Allowance::class, 'contract_id', 'category_id', 'id')
			->withPivot('expense', 'desc');
	}

    public function insurancePremiums()
    {
        return $this->belongsToMany(AllowanceCategory::class, Allowance::class, 'contract_id', 'category_id')
                    ->where('is_social_security', 1)->withPivot('expense', 'active')->wherePivot('active', 1);
    }

    public function nonTaxAllowances()
    {
        return $this->belongsToMany(AllowanceCategory::class, Allowance::class, 'contract_id', 'category_id')
                    ->where('is_exemp', 1)->withPivot('expense', 'active')->wherePivot('active', 1);
    }

    public function taxableAllowances()
    {
        return $this->belongsToMany(AllowanceCategory::class, Allowance::class, 'contract_id', 'category_id')
                    ->where('is_exemp', 0)->withPivot('expense', 'active')->wherePivot('active', 1);
    }

    public function appendixAllowances1()
    {
        return $this->belongsToMany(AllowanceCategory::class, AppendixAllowance::class, 'contract_id', 'allowance_id')
                    ->where('is_social_security', 1)->withPivot('expense', 'salary', 'status', 'allowance_active')->wherePivot('status', 1)
                    ->wherePivot('allowance_active', 1);
    }

    public function nonTaxAllowances1()
    {
        return $this->belongsToMany(AllowanceCategory::class, AppendixAllowance::class, 'contract_id', 'allowance_id')
                    ->where('is_exemp', 1)->withPivot('expense', 'salary', 'status', 'allowance_active')->wherePivot('status', 1)
                    ->wherePivot('allowance_active', 1);
    }

    public function taxableAllowances1()
    {
        return $this->belongsToMany(AllowanceCategory::class, AppendixAllowance::class, 'contract_id', 'allowance_id')
                    ->where('is_exemp', 0)->withPivot('expense', 'salary', 'status', 'allowance_active')->wherePivot('status', 1)
                    ->wherePivot('allowance_active', 1);
    }

    public function foodAllowance()
    {
        return $this->belongsToMany(AllowanceCategory::class, Allowance::class, 'contract_id', 'category_id')
                    ->withPivot('expense', 'active')->wherePivot('active', 1);
    }

    public function foodAllowance1()
    {
        return $this->belongsToMany(AllowanceCategory::class, AppendixAllowance::class, 'contract_id', 'allowance_id')
                    ->withPivot('expense', 'allowance_active')->wherePivot('allowance_active', 1);
    }

	public function contractFiles()
	{
		return $this->hasMany(ContractFile::class);
    }

    public function listLogs()
    {
        return $this->morphMany(ListLog::class, 'object');
    }

	public static function getContractsInAMonth($userId, $month = null, $year = null)
	{
        $currentMonth = $month ?? Carbon::now()->month;
        $currentYear = $year ?? Carbon::now()->year;
        $checkDate = Carbon::createFromDate($currentYear, $currentMonth - 1,Schedule::DATE_START_SALARY)->format('Y-m-d');
        $checkEndDate = Carbon::createFromDate($currentYear, $currentMonth,Schedule::DATE_END_SALARY)->format('Y-m-d');
        $contracts = Contract::where('user_id', $userId)
            ->where('valid_from', '<=', $checkEndDate)
            ->get();
        $count = count($contracts);
        if ($count == 0) return 0;
        if ($count == 1) return 1;
        $activeContract = Contract::where('user_id', $userId)->where('type_status', 1)->first();
        if ($activeContract && $activeContract->valid_from <= $checkDate) return 1;
        $beforeContracts = Contract::where('user_id', $userId)
            ->where('type_status', '<>', 1)
            ->whereBetween('set_notvalid_on', [$checkDate, $activeContract->valid_from])
            ->where('is_used', 1)
            ->orderByDesc('set_notvalid_on')
            ->get();
        if (!$beforeContracts->count()) return 1;
        $arrContracts = [];
//		$oldDate = $beforeContracts->valid_from <= $checkDate ? $beforeContracts->valid_from : $checkDate;
        foreach ($beforeContracts as $beforeContract) {
            $arrContracts[] = [
                'id' => $beforeContract->id,
                'is_main' => $beforeContract->is_main,
                'start'=> $beforeContract->valid_from,
                'end' => Carbon::create($beforeContract->set_notvalid_on) ?? $beforeContract->valid_to
            ];
        }
        if ($activeContract) {
            array_push($arrContracts, [
                'id' => $activeContract->id,
                'is_main' => $activeContract->is_main,
                'start'=> $activeContract->valid_from,
                'end' => $activeContract->valid_to ?? Carbon::createFromDate($currentYear, $currentMonth,25)->format('Y-m-d')
            ]);
        }
        $arrContracts[0]['start'] = $checkDate;
        return $arrContracts;

		/*$currentMonth = $month ?? Carbon::now()->month;
		$currentYear = $year ?? Carbon::now()->year;
		$checkDate = Carbon::createFromDate($currentYear, $currentMonth - 1,Schedule::DATE_START_SALARY)->format('Y-m-d');
		$checkEndDate = Carbon::createFromDate($currentYear, $currentMonth,Schedule::DATE_END_SALARY)->format('Y-m-d');
		$contracts = Contract::where('user_id', $userId)
            ->where('valid_from', '<', $checkEndDate)
            ->get();
		$count = count($contracts);
		if ($count == 0) return 0;
		if ($count == 1) return 1;
        $activeContract = $beforeContracts = collect();
		foreach ($contracts as $item) {
		    if ($item->type_status == \App\Defines\Contract::ACTIVE) $activeContract = $item;
        }
		if ($activeContract->count() == 0) return 1;
		//$activeContract = Contract::where('user_id', $userId)->where('type_status', 1)->first();
		if ($activeContract->valid_from <= $checkDate) return 1;
		foreach ($contracts as $item) {
		    if ($item->type_status != \App\Defines\Contract::ACTIVE &&
                $item->set_notvalid_on > $checkDate &&
                $item->set_notvalid_on <= $activeContract->valid_from) {
                $beforeContracts->push($item);
            }
        }
		if (!$beforeContracts->count()) return 1;
		$arrContracts = [];
		foreach ($beforeContracts as $beforeContract) {
			$arrContracts[] = [
				'id' => $beforeContract->id,
				'is_main' => $beforeContract->is_main,
				'start'=> $beforeContract->valid_from,
				'end' => Carbon::create($beforeContract->set_notvalid_on) ?? $beforeContract->valid_to
			];
		}
		array_push($arrContracts, [
			'id' => $activeContract->id,
			'is_main' => $activeContract->is_main,
			'start'=> $activeContract->valid_from,
			'end' => $activeContract->valid_to ?? Carbon::createFromDate($currentYear, $currentMonth,25)->format('Y-m-d')
		]);
		$arrContracts[0]['start'] = $checkDate;
		return $arrContracts;*/
    }

	public function getTimeRemainsAttribute()
	{
	    if ($this->type_status != 1) return '';
		if (!$this->valid_to) return trans('contracts.types.' . \App\Defines\Contract::TYPE_UNLIMITED);
		$validTo = Carbon::parse($this->set_notvalid_on);
		$validToTz = $validTo->timestamp; $tz = now()->timestamp;
		$validFromTz = strtotime($this->valid_from);
		if ($validToTz < $tz || $validFromTz > $tz) return '';
		$date = today();
		return $validTo->diff($date)->format('%y năm %m tháng %d ngày');
    }

	public function getNearlyExpiredAttribute()
	{
		if ($this->type_status > 1 || !$this->valid_to) return false;
        $validToTz = strtotime($this->valid_to);
        $tz = now()->timestamp;
        if ($validToTz < $tz) return true;
		$check = Carbon::parse($this->valid_to)->diffInDays(now());
		return 0 <= $check && $check < 15;
    }

    public function getCountExpiredAttribute()
    {
        if ($this->type_status > 1 || !$this->valid_to) return 1000000;
        return Carbon::parse($this->set_notvalid_on)->diffInDays(Carbon::now()) + 1;
    }

	public static function allowanceFullExpenses($contractId)
	{
		return AllowanceCategory::join('allowances', function ($query) use($contractId) {
			$query->on('allowances.category_id', 'allowance_categories.id')
				->where('contract_id', $contractId);
		})->get(['allowance_categories.id', 'name', 'name_es', 'expense']);
	}

	public static function countTypeContracts($companyId = null)
	{
        $time_start = microtime(true);
        /*$probation = Contract::where('status', 1)->where('is_used', 1)->where('type', null)->count();
		$oneYear = Contract::where('status', 1)->where('is_used', 1)->where('type', \App\Defines\Contract::TYPE_1_YEAR)->count();
		$sixMonth = Contract::where('status', 1)->where('is_used', 1)->where('type', \App\Defines\Contract::TYPE_6_MONTH)->count();
		$threeYear = Contract::where('status', 1)->where('is_used', 1)->where('type', \App\Defines\Contract::TYPE_3_YEAR)->count();
		$unlimited = Contract::where('status', 1)->where('is_used', 1)->where('type', \App\Defines\Contract::TYPE_UNLIMITED)->count();*/
        [$probation, $sixMonth, $oneYear, $threeYear, $unlimited] = [0, 0, 0, 0, 0];
		$typeContracts = Contract::groupBy('type')->select('type', DB::raw('count(*) as total'))->get();
		foreach ($typeContracts as $item) {
		    if (is_null($item->type)) $probation = $item['total'];
		    elseif (($item->type == \App\Defines\Contract::TYPE_6_MONTH)) $sixMonth = $item['total'];
            elseif (($item->type == \App\Defines\Contract::TYPE_1_YEAR)) $oneYear = $item['total'];
            elseif (($item->type == \App\Defines\Contract::TYPE_3_YEAR)) $threeYear = $item['total'];
            elseif (($item->type == \App\Defines\Contract::TYPE_UNLIMITED)) $unlimited = $item['total'];
        }
		return [$probation, $sixMonth, $oneYear, $threeYear, $unlimited];
	}

	public static function checkTransfer($contractId)
	{
		$contract = Contract::find($contractId);
		if ($contract->type_status !== \App\Defines\Contract::TRANSFER) return 1;
		else {
			$contract = Contract::orderBy('id')->where('user_id', $contract->user_id)->where('id', '>', $contractId)->first();
			if (!count($contract)) return 2;
			return $contract;
		}
	}

	public static function checkAppoint($contractId)
	{
		$contract = Contract::find($contractId);
		if ($contract->type_status !== \App\Defines\Contract::APPOINT) return 1;
		else {
			$contract = Contract::orderBy('id')->where('user_id', $contract->user_id)->where('id', '>', $contractId)->first();
			if (!count($contract)) return 2;
			return $contract;
		}
	}

    public static function isFirstStaffContract($userId, $contractId)
    {
        $contractIdMin = Contract::where('user_id', $userId)->min('id');
        if ($contractIdMin == $contractId) return true;
        return false;
	}

	public static function getStaffVariation($year = null)
	{
		$selectYear = $year ?? now()->year;
        $countStaffNew = $countStaffLeave = array_fill(0, 12, 0);

        $leaveStaffs = Contract::whereNotNull('set_notvalid_on')
            ->whereYear('set_notvalid_on', $selectYear)
			->whereIn('type_status', [\App\Defines\Contract::LEAVE_WORK, \App\Defines\Contract::CHO_NGHI_VIEC])
            ->groupBy(DB::raw('MONTH(set_notvalid_on)'))
            ->select(DB::raw('MONTH(set_notvalid_on) as month'), DB::raw('count(*) as total'))
			->get()->keyBy('month')->toArray();
        foreach ($leaveStaffs as $m => $data) {
            $countStaffLeave[$m-1] = $data['total'];
        }
        $countUserPerMonths = User::whereNotNull('staff_start')
            ->whereYear('staff_start', $selectYear)
            ->groupBy(DB::raw('MONTH(staff_start)'))
            ->select(DB::raw('MONTH(staff_start) as month'), DB::raw('count(*) as total'))
            ->get()->keyBy('month')->toArray();
        foreach ($countUserPerMonths as $m => $data) {
            $countStaffNew[$m-1] = $data['total'];
        }
        return [$countStaffLeave, $countStaffNew];
	}

	public function checkTarget()
    {
        return $this->belongsToMany(AllowanceCategory::class, Allowance::class, 'contract_id', 'category_id')->where('type', 1)
                    ->whereNull('type_work')
                    ->withPivot('expense', 'active')->wherePivot('active', 1);
    }

	public function checkTarget1()
    {
        return $this->belongsToMany(AllowanceCategory::class, AppendixAllowance::class, 'contract_id', 'allowance_id')
                    ->where('type', 1)->withPivot('expense', 'salary', 'status', 'allowance_active')->wherePivot('status', 1)
                    ->wherePivot('allowance_active', 1);
    }

    public static function boot() {
        parent::boot();
        
        static::created(function($contract) {
            if ($contract->type_status != \App\Defines\Contract::FUTURE) {
                $contract->user()->update([
                    'company_id' => $contract->company_id,
                    'department_id' => $contract->department_id,
                    'position_id' => $contract->position_id,
                    'qualification_id' => $contract->qualification_id,
                    'status' => $contract->is_main,
                    'active' => 1,
                    'dept_group_id' => $contract->department_group_id,
                    'is_leave' => null
                ]);
            }
        });
        static::updated(function ($model) {
            $changedData = $model->changes;
            unset($changedData['updated_at']);
            unset($changedData['updated_by']);
            $logs = [];
            $fieldChange = array_keys($changedData);
            foreach ($fieldChange as $field) {
                if (isset(auth()->guard('admin')->user()->id) && $model->wasChanged($field)) {
                    $logs[] = [
                        'new_data'                  => $model->$field,
                        'old_data'                  => $model->getOriginal('' . $field . ''),
                        'field'                     => $field,
                        //'object_id'             => $model->id,
                        //'object_type'           => self::class,
                        'action_at'                 => now(),
                        'action_by'                 => auth()->guard('admin')->user()->id,
                        'key' => now()->timestamp
                        //'ip_address'                => \Request::ip(),
                        //'device'                    => \Request::server('HTTP_USER_AGENT'),
                        //'parent_objectable_id'      => isset($parentModel) ? $parentModel->id : null,
                        //'parent_objectable_type'    => isset($parentModel) ? get_class($parentModel) : null,
                    ];
                }
            }
            if ($logs) {
                $model->listLogs()->createMany($logs);
            }
        });
    }

    public static function setValidTo($validFrom, $typeContract)
    {
        switch ($typeContract) {
            case \App\Defines\Contract::TYPE_6_MONTH:
                $validTo = Carbon::createFromFormat('d/m/Y', $validFrom)->addMonths(6)->subDay();
                break;
            case \App\Defines\Contract::TYPE_1_YEAR:
                $validTo = Carbon::createFromFormat('d/m/Y', $validFrom)->addYear()->subDay();
                break;
            case \App\Defines\Contract::TYPE_3_YEAR:
                $validTo = Carbon::createFromFormat('d/m/Y', $validFrom)->addYears(3)->subDay();
                break;
            default:
                $validTo = null;
        }
        return  $validTo ? $validTo->format('Y-m-d') : null;
    }

    public static function checkDeleteModule($key, $value)
    {
        $temp = self::where($key, $value)->first();
        if ($temp) return true;
        return false;
    }

    public function allowanceTargetByWorking()
    {
        return $this->belongsToMany(AllowanceCategory::class, Allowance::class, 'contract_id', 'category_id')->where('type', 1)
                    ->where('type_work', 'BY_WORKING_DAY')->whereNotIn('category_id', [1])
                    ->withPivot('expense', 'active')->wherePivot('active', 1);
    }

    public function allowanceByWorking()
    {
        return $this->belongsToMany(AllowanceCategory::class, Allowance::class, 'contract_id', 'category_id')
                    ->where('type', 0)->whereNotIn('category_id', [1])
                    ->where('type_work', 'BY_WORKING_DAY')
                    ->withPivot('expense', 'active')->wherePivot('active', 1);
    }

    public function dataAllowance()
    {
        return $this->belongsToMany(AllowanceCategory::class, Allowance::class, 'contract_id', 'category_id')
                    ->where('type', 0)->whereNotIn('category_id', [1])
                    ->whereNull('type_work')
                    ->withPivot('expense', 'active')->wherePivot('active', 1);
    }

    public function phuCapKhongTinhThue()
    {
        return $this->belongsToMany(AllowanceCategory::class, Allowance::class, 'contract_id', 'category_id')
                    ->whereIn('category_id', [6])
                    ->withPivot('expense', 'active')->wherePivot('active', 1);
    }

    public function phuCapOtChiuThue()
    {
        return $this->belongsToMany(AllowanceCategory::class, Allowance::class, 'contract_id', 'category_id')
                    ->where('ot_tax', 1)
                    ->withPivot('expense', 'active')->wherePivot('active', 1);
    }

    public function phucapOtMienThue()
    {
        return $this->belongsToMany(AllowanceCategory::class, Allowance::class, 'contract_id', 'category_id')
                    ->where('ot', 1)
                    ->withPivot('expense', 'active')->wherePivot('active', 1);
    }

    public function phuCapAn()
    {
        return $this->belongsToMany(AllowanceCategory::class, Allowance::class, 'contract_id', 'category_id')
                    ->withPivot('expense', 'active')->wherePivot('active', 1)->wherePivot('category_id', 1);
    }

    public function phuCapChuyenCan()
    {
        return $this->belongsToMany(AllowanceCategory::class, Allowance::class, 'contract_id', 'category_id')
                    ->withPivot('expense', 'active')->wherePivot('active', 1)->wherePivot('category_id', 10);
    }

    public static function getMinMaxValidDate($contractAll)
    {
        $contract = $contractAll->first();
        $minValidFrom = $contract->valid_from;
        $maxValidTo = $contract->set_notvalid_on;
        foreach ($contractAll as $con) {
            if ($minValidFrom > $con->valid_from) $minValidFrom = $con->valid_from;
            if (is_null($maxValidTo)) continue;
            if (is_null($con->set_notvalid_on)) {
                $maxValidTo = null;
                continue;
            }
            if ($maxValidTo < $con->set_notvalid_on) $maxValidTo = $con->set_notvalid_on;
        }
        return ['min' => $minValidFrom->format('Y-m-d'), 'max' => $maxValidTo];
    }

    public static function getTypeContractByDate($contracts, $date)
    {
        $contractType = '';
        if (count($contracts) == 1) { // kiểm tra hợp đồng trong 1 tháng
            $contractType = $contracts->first()->is_main == Staff::STATUS_OFFICIAL ? 'HOP_DONG' : 'THU_VIEC';
        } else {
            foreach ($contracts as $p => $v) {
                if ($date >= strtotime($v['valid_from']) && (is_null($v['set_notvalid_on']) || $date < strtotime($v['set_notvalid_on']))) {
                    $contractType = $v['is_main'] == Staff::STATUS_OFFICIAL ? 'HOP_DONG' : 'THU_VIEC';
                    break;
                }
            }
        }
        return $contractType;
    }

    public static function getContractsInRangeByDept($deptId, $startDate, $endDate)
    {
        $contracts = Contract::where('department_id', $deptId)
            ->whereDate('valid_from', '<', $endDate)
            ->orderBy('id', 'DESC')
            ->get();

        foreach ($contracts as $key => $item) {
            if (is_null($item->set_notvalid_on)) continue;
            if (date('Y-m-d', strtotime($item->set_notvalid_on)) <= $startDate)
                unset($contracts[$key]);
        }
        return $contracts;
    }

    public static function isSameTwoContracts($contracts)
    {
        $result = ['is_same' => false];
        if (count($contracts) != 2) return $result;
        $contract1 = $contracts[0]; $contract2 = $contracts[1];
        if ($contract1->basic_salary == $contract2->basic_salary) {
            $allowance1 = $contract1->allowances;
            $allowance2 = $contract2->allowances;
            if (count($allowance1) == count($allowance2)) {
                if (count($allowance1) == 0) {
                    $result['is_same'] = true;
                    $result = array_merge($result, self::getMinMaxValidDate($contracts));
                } else {
                    $allowance1 = $allowance1->pluck('expense', 'category_id');
                    $allowance2 = $allowance2->pluck('expense', 'category_id');
                    $check = true;
                    foreach ($allowance1 as $id => $amount) {
                        if (!$allowance2[$id] || $allowance2[$id] != $amount) $check == false;
                    }
                    if ($check) {
                        $result['is_same'] = true;
                        $result = array_merge($result, self::getMinMaxValidDate($contracts));
                    }
                }
            }
        }
        return $result;
    }
}
