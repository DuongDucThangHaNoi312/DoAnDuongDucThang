<?php

namespace App\Http\Controllers\Backend;

use App\Defines\Contract as DefinesContract;
use App\Helpers\GetOption;
use App\Models\Allowance;
use App\PermissionUserObject;
use App\User;
use App\Position;
use Carbon\Carbon;
use App\Qualification;
use App\Defines\Staff;
use App\Models\Company;
use App\Models\Contract;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Models\ContractFile;
use App\Imports\ContractsImport;
use App\Models\AllowanceCategory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\ConcurrentContract;
use Illuminate\Support\Facades\Session;

class ContractController extends Controller
{
    public function index(Request $request, $type = 0, $child = null)
    {
        /*$a = User::where('department_id', null)->has('activeContract')->get();
        dd($a);*/
        $query = '1=1';
        $typeStatus = $request->input('type') ?? '';
        $child = $request->input('child-type') ?? '';
        if ($typeStatus) $query .= " AND type_status = {$typeStatus}";
        if ($typeStatus == \App\Defines\Contract::TRANSFER) {
            if ($child == 1) $query .= " AND transfer_to is not null";
            if ($child == 2) $query .= " AND transfer_to is null";
        }
        if ($typeStatus == \App\Defines\Contract::APPOINT) {
            if ($child == 1) $query .= " AND appoint_to is not null";
            if ($child == 2) $query .= " AND appoint_to is null";
        }
        $user = Auth::user();
        $deptId = $user->department_id;
        $infoPermission = \App\PermissionUserObject::getMorePermissions();
        $queryPer = User::getQueryPermission($infoPermission, $user->id, 'user_id', 'contract');
        $queryEnd = $query.$queryPer;
        if (!empty($typeStatus)) {
            $contracts = Contract::whereRaw($queryEnd)
                ->with('user', 'company', 'qualification', 'department', 'position')
                ->orderBy('updated_at', 'desc')
                ->get();
            if ($infoPermission['departments'] && !in_array($deptId, $infoPermission['departments'])) {
                $loginContract = Contract::whereRaw($query)->where('user_id', $user->id)->where('status', 1)->first();
                if (!is_null($loginContract))
                    $contracts->push($loginContract);
            }
        } else {
            $contracts = Contract::whereRaw($queryEnd)
                ->with('user', 'company', 'qualification', 'department', 'position')
                ->get()->sortBy('count_expired');
            if ($infoPermission['departments'] && !in_array($deptId, $infoPermission['departments'])) {
                $loginContract = Contract::whereRaw($query)->where('user_id', $user->id)->where('status', 1)->first();
                if (!is_null($loginContract))
                    $contracts->push($loginContract);
            }
            $contracts->sortBy('count_expired');
        }
        $companyOptionSearch  = GetOption::getCompaniesForOptionIndex($infoPermission);
        $deptOptionSearch = GetOption::getDepartmentsForOptionPermissionIndex($infoPermission);
        return view('backend.contracts.index', compact('contracts', 'companyOptionSearch', 'deptOptionSearch'));
    }

    public function create(Request $request)
    {
        $allowancesOption = AllowanceCategory::pluck('name', 'id')->toArray();
        $ref = intval($request->ref);
        $contract = null;
        if ($ref) {
            $contract = Contract::with('allowances', 'user')->find($ref);
            if (is_null($contract)) {
                Session::flash('message', trans('system.have_an_error'));
                Session::flash('alert-class', 'danger');
                return back();
            }
        }
        if (!is_null($contract)) {
            return view('backend.contracts.create_copy', compact('contract', 'allowancesOption'));
        }
        return view('backend.contracts.create', compact('allowancesOption'));
    }

    public function store(Request $request)
    {
        try {
            $request->merge(['status' => $request->input('status', 1)]);
            $data = $request->all();
            $data['staff_id'] = $data['user_id'];
            $data['title_id'] = $data['qualification_id'];
            //$data['set_notvalid_on'] = $data['type'] == \App\Defines\Contract::TYPE_UNLIMITED ? null : date("Y-m-d 00:00:00", strtotime(str_replace('/', '-', $data['valid_to'])));
            $userData = User::find($data['user_id']);
            if (is_null($userData)) throw new \Exception('Nhân viên không tồn tại');
            $validFrom = date("Y-m-d", strtotime(str_replace('/', '-', $data['valid_from'])));;
            $dateNow = now()->format('Y-m-d');

            if ($validFrom > $dateNow) {
                $data['type_status'] = DefinesContract::FUTURE;
                $data['status'] = 0;
                if (!is_null(Contract::where('user_id', $userData->id)->where('type_status', \App\Defines\Contract::FUTURE)->first()))
                    throw new \Exception('Nhân viên đã có hợp đồng Chờ áp dụng');
                if ($data['is_main'] == Staff::STATUS_PROBATIONARY) {
                    $validator = \Validator::make($data, [
                        'valid_to' => 'required',
                    ]);
                    $validator->setAttributeNames(trans('contracts'));
                    if ($validator->fails()) return back()->withErrors($validator)->withInput();
                    $data['valid_to'] = date("Y-m-d", strtotime(str_replace('/', '-', $data['valid_to'])));
                }
                if ($data['is_main'] == Staff::STATUS_OFFICIAL) {
                    $validator = \Validator::make($data, [
                        'type' => 'required',
                    ]);
                    $validator->setAttributeNames(trans('contracts'));
                    if ($validator->fails()) return back()->withErrors($validator)->withInput();
                    $data['valid_to'] = Contract::setValidTo($data['valid_from'], $data['type']);
                }
                $data['set_notvalid_on'] = empty($data['valid_to']) ? null : Carbon::parse($data['valid_to'])->addDay()->format('Y-m-d');
                $dept = Department::where('status', 1)
                    ->where('id', $data['department_id'])
                    ->first();
                if (!$dept) throw new \Exception('Phòng ban không tồn tại');
                if (!$dept->is_multi_currency) {
                    $data['currency_code'] = null;
                } else {
                    if (!$data['currency_code']) throw new \Exception('Phải chọn loại tiền với phòng ban này.');
                }
                if ($data['is_main'] == Staff::STATUS_PROBATIONARY) $data['type'] = null;
                $data['department_group_id'] = Department::getGroupOfDept($data['department_id']);
                $validator = \Validator::make($data, Contract::rules());
                $validator->setAttributeNames(trans('contracts'));
                if ($validator->fails()) return back()->withErrors($validator)->withInput();

                $check = !in_array($data['qualification_id'], \App\Defines\Contract::DRIVER_ID);
                if ($check || $data['is_main'] == Staff::STATUS_OFFICIAL) {
                    $validator = \Validator::make($data, [
                        'basic_salary' => 'required',
                    ]);
                    $validator->setAttributeNames(trans('contracts'));
                    if ($validator->fails()) return back()->withErrors($validator)->withInput();
                } else $data['basic_salary'] = 0;

                $codeUser = User::find(intval($data['user_id']))->code;
                $companyName = Company::find(intval($data['company_id']))->shortened_name;
                $data['code'] = date("dmy", strtotime( str_replace('/', '-', $data['valid_from']) )) . '-' . $codeUser . '-' . strtoupper($companyName) . '/' . ($data['is_main'] == Staff::STATUS_OFFICIAL ? 'HDLD' : 'HDTV');
                $data['created_by'] = Auth::id();

                DB::beginTransaction();
                $contract = Contract::create($data);
                //Contract::where('user_id', $data['user_id'])->where('id', '<', $contract->id)->update(['status' => 0]);
                $allowance_cat = $request->input('allowance_cat');
                $allowance_cat = array_values($allowance_cat);
                if ($allowance_cat[0]) {
                    $isDeptWarehouse = Department::isDeptWarehouse($data['department_id']);
                    if (count(array_unique($allowance_cat)) != count($allowance_cat)) {
                        return back()->with('err_allowance', trans('contracts.validate_allowance_category'))->withInput();
                    }
                    $allowance_cost = $request->input('allowance_cost');
                    $allowance_cost = array_values($allowance_cost);
                    $desc = array_values($request->input('desc'));
                    $allowances = [];
                    $count = count($allowance_cat);
                    for ($i = 0; $i < $count; $i++) {
                        if (empty($allowance_cost[$i])) {
                            return back()->with('err_allowance', trans('contracts.validate_allowance_cost'))->withInput();
                        }
                        $allowances[$i]['category_id'] = $allowance_cat[$i];
                        $allowances[$i]['expense'] = str_replace(',', '', $allowance_cost[$i]);
                        $allowances[$i]['desc'] = $desc[$i];
                        if ($isDeptWarehouse && $allowance_cat[$i] == 1) $allowances[$i]['type_dept'] = 1;
                    }
                    $contract->allowances()->createMany($allowances);
                }
                if ($request->hasFile('file')) {
                    $files = $request->file('file');
                    $validator_file = \Validator::make($request->only('file'), ContractFile::rules(), [], ['file.*' => 'file']);
                    if ($validator_file->fails()) {
                        return back()->withErrors($validator_file)->withInput();
                    }
                    foreach ($files as $file) {
                        $name = $file->getClientOriginalName();
                        $name = str_replace(' ', '-', trim($name));
                        $path = $file->move(\Config::get('upload.contracts'), $name);
                        $contract->contractFiles()->create([
                            'name' => $name,
                            'path' => $path,
                            'user_id' => $contract->user_id,
                            'status' => 1
                        ]);
                    }
                }

            } else {
                if ($userData->active) throw new \Exception(trans('contracts.validate_staff_contract'));
                //if (User::find($data['user_id'])->active) return back()->with('err_staff_contract', trans('contracts.validate_staff_contract'))->withInput();
                if (!Department::validateDepartmentPosition($data['department_id'], $data['position_id'], null, null, null)) {
                    return back()->with('err_position', trans('contracts.validate_manager'))->withInput();
                }
                if ($data['is_main'] == Staff::STATUS_PROBATIONARY) {
                    $validator = \Validator::make($data, [
                        'valid_to' => 'required',
                    ]);
                    $validator->setAttributeNames(trans('contracts'));
                    if ($validator->fails()) return back()->withErrors($validator)->withInput();
                    $data['valid_to'] = date("Y-m-d", strtotime(str_replace('/', '-', $data['valid_to'])));
                }
                if ($data['is_main'] == Staff::STATUS_OFFICIAL) {
                    $validator = \Validator::make($data, [
                        'type' => 'required',
                    ]);
                    $validator->setAttributeNames(trans('contracts'));
                    if ($validator->fails()) return back()->withErrors($validator)->withInput();
                    $data['valid_to'] = Contract::setValidTo($data['valid_from'], $data['type']);
                }

                $data['set_notvalid_on'] = empty($data['valid_to']) ? null : Carbon::parse($data['valid_to'])->addDay()->format('Y-m-d');

                //Xử lý phòng ban có nhiều loại tiền
                $dept = Department::where('status', 1)
                    ->where('id', $data['department_id'])
                    ->first();
                if (!$dept) throw new \Exception('Phòng ban không tồn tại');
                if (!$dept->is_multi_currency) {
                    $data['currency_code'] = null;
                } else {
                    if (!$data['currency_code']) throw new \Exception('Phải chọn loại tiền với phòng ban này.');
                }

                // if (in_array($data['qualification_id'], \App\Defines\Contract::DRIVER_ID)) $data['basic_salary'] = 0;

                if ($data['is_main'] == Staff::STATUS_PROBATIONARY) $data['type'] = null;
                $data['type_status'] = \App\Defines\Contract::ACTIVE;
                $data['department_group_id'] = Department::getGroupOfDept($data['department_id']);
                $validator = \Validator::make($data, Contract::rules());
                $validator->setAttributeNames(trans('contracts'));
                if ($validator->fails()) return back()->withErrors($validator)->withInput();
                $check = !in_array($data['qualification_id'], \App\Defines\Contract::DRIVER_ID);
                if ($check || $data['is_main'] == Staff::STATUS_OFFICIAL) {
                    $validator = \Validator::make($data, [
                        'basic_salary' => 'required',
                    ]);
                    $validator->setAttributeNames(trans('contracts'));
                    if ($validator->fails()) return back()->withErrors($validator)->withInput();
                } else $data['basic_salary'] = 0;
                $codeUser = User::find(intval($data['user_id']))->code;
                $companyName = Company::find(intval($data['company_id']))->shortened_name;
                $data['code'] = date("dmy", strtotime( str_replace('/', '-', $data['valid_from']) )) . '-' . $codeUser . '-' . strtoupper($companyName) . '/' . ($data['is_main'] == Staff::STATUS_OFFICIAL ? 'HDLD' : 'HDTV');
                $data['created_by'] = Auth::id();

                DB::beginTransaction();
                $contract = Contract::create($data);
                //Contract::where('user_id', $data['user_id'])->where('id', '<', $contract->id)->update(['status' => 0]);

                $oldContract = Contract::find($data['old_contract']);
                if ($data['user_id'] == $oldContract->user_id) {
                    if ($oldContract && $oldContract->type_status == \App\Defines\Contract::TRANSFER) {
                        $oldContract->update([
                            'transfer_to' => $contract->id
                        ]);
                        $contract->update([
                            'transfer_from' => $oldContract->id
                        ]);
                    }
                    if ($oldContract && $oldContract->type_status == \App\Defines\Contract::APPOINT) {
                        $oldContract->update([
                            'appoint_to' => $contract->id
                        ]);
                        $contract->update([
                            'appoint_from' => $oldContract->id
                        ]);
                    }
                }
                $allowance_cat = $request->input('allowance_cat');
                $allowance_cat = array_values($allowance_cat);
                if ($allowance_cat[0]) {
                    $isDeptWarehouse = Department::isDeptWarehouse($data['department_id']);
                    if (count(array_unique($allowance_cat)) != count($allowance_cat)) {
                        return back()->with('err_allowance', trans('contracts.validate_allowance_category'))->withInput();
                    }
                    $allowance_cost = $request->input('allowance_cost');
                    $allowance_cost = array_values($allowance_cost);
                    $desc = array_values($request->input('desc'));
                    $allowances = [];
                    $count = count($allowance_cat);
                    for ($i = 0; $i < $count; $i++) {
                        if (empty($allowance_cost[$i])) {
                            return back()->with('err_allowance', trans('contracts.validate_allowance_cost'))->withInput();
                        }
                        $allowances[$i]['category_id'] = $allowance_cat[$i];
                        $allowances[$i]['expense'] = str_replace(',', '', $allowance_cost[$i]);
                        $allowances[$i]['desc'] = $desc[$i];
                        if ($isDeptWarehouse && $allowance_cat[$i] == 1) $allowances[$i]['type_dept'] = 1;
                    }
                    $contract->allowances()->createMany($allowances);
                }
                if ($request->hasFile('file')) {
                    $files = $request->file('file');
                    $validator_file = \Validator::make($request->only('file'), ContractFile::rules(), [], ['file.*' => 'file']);
                    if ($validator_file->fails()) {
                        return back()->withErrors($validator_file)->withInput();
                    }
                    foreach ($files as $file) {
                        $name = $file->getClientOriginalName();
                        $name = str_replace(' ', '-', trim($name));
                        $path = $file->move(\Config::get('upload.contracts'), $name);
                        $contract->contractFiles()->create([
                            'name' => $name,
                            'path' => $path,
                            'user_id' => $contract->user_id,
                            'status' => 1
                        ]);
                    }
                }
                //Nếu user chưa có role set mặc định role Nhân viên
                if (!DB::table('role_user')->where('user_id', $contract->user_id)->first()) {
                    $userData->attachRole(Staff::ROLE_NV);
                }
            }

            DB::commit();
            Session::flash('message', trans('system.success'));
            Session::flash('alert-class', 'success');
            return redirect()->route('admin.contracts.index');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('err_try_catch', $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $contract = Contract::where(['id' => $id])
            ->with('user', 'company', 'qualification', 'department', 'allowances.allowanceCategory', 'concurrentContracts', 'contractFiles')
            ->first();
        if (is_null($contract)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return back();
        }
        $infoPermission = \App\PermissionUserObject::getMorePermissions();
        if ($infoPermission && $contract->user_id != Auth::id()) {
            if (!in_array($contract->department_id, $infoPermission['departments'])) {
                Session::flash('message', 'Bạn không có quyền xem hợp đồng nhân viên này!');
                Session::flash('alert-class', 'danger');
                return back();
            }
        }
        $appendixAllowances = $contract->appendixAllowances3 ? $contract->appendixAllowances3->sortByDesc('created_at')->groupBy('code')->all() : [];
        $lastAppendixAllowance = reset($appendixAllowances);
        $departmentOption = Company::find($contract->company->id)->departments->pluck('name', 'id')->toArray();
        $allowancesOption = AllowanceCategory::pluck('name', 'id')->toArray();
        $allowancesOptionIdCurrent = [];
        foreach ($contract->allowances as $allowance) {
            array_push($allowancesOptionIdCurrent, $allowance->allowanceCategory->id);
        }
        $allowanceCategoryKpi = AllowanceCategory::allowanceCategoryHasKpi();
        return view('backend.contracts.show', compact('contract', 'departmentOption', 'allowancesOptionIdCurrent', 'allowancesOption', 'appendixAllowances', 'lastAppendixAllowance', 'allowanceCategoryKpi'));
    }

    public function edit($id)
    {
        $contract = Contract::where('id', $id)->with('allowances.allowanceCategory', 'qualification', 'position', 'department', 'user', 'contractFiles')->first();
        if (is_null($contract)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return back();
        }
        $infoPermission = \App\PermissionUserObject::getMorePermissions();
        if ($infoPermission && $contract->user_id != Auth::id()) {
            if (!in_array($contract->department_id, $infoPermission['departments'])) {
                Session::flash('message', 'Bạn không có quyền sửa hợp đồng nhân viên này!');
                Session::flash('alert-class', 'danger');
                return back();
            }
        }
        $allowancesOption = AllowanceCategory::pluck('name', 'id')->toArray();
        $allowancesOptionIdCurrent = [];
        foreach ($contract->allowances as $allowance) {
            array_push($allowancesOptionIdCurrent, $allowance->allowanceCategory->id);
        }

        $departmentOption = Company::find($contract->company->id)->departments->pluck('name', 'id')->toArray();
        return view('backend.contracts.edit', compact('contract', 'allowancesOption', 'allowancesOptionIdCurrent', 'departmentOption'));
    }

    public function update(Request $request, $id)
    {
        try {
            $contract = Contract::find(intval($id));
            if (is_null($contract)) {
                Session::flash('message', trans('system.have_an_error'));
                Session::flash('alert-class', 'danger');
                return back();
            }
            $typeStatus = $request->input('type_status');
            $status = $request->input('status');
            if ($typeStatus != \App\Defines\Contract::ACTIVE) $status = 0;
            /*if ($contract->check_valid == \App\Defines\Contract::NOT_VALID) {
                Session::flash('message', trans('system.have_an_error'));
                Session::flash('alert-class', 'danger');
                return redirect()->route('admin.contracts.index');
            }*/
            $data = $request->all();
            $validFrom = date("Y-m-d", strtotime(str_replace('/', '-', $data['valid_from'])));;
            $dateNow = now()->format('Y-m-d');

            if ($contract->type_status != \App\Defines\Contract::FUTURE && $typeStatus == \App\Defines\Contract::FUTURE)
                throw new \Exception('Không đổi từ hợp đồng trạng thái khác sang Chờ áp dụng');

            if ($validFrom > $dateNow && $typeStatus != \App\Defines\Contract::FUTURE)
                throw new \Exception('Hợp đồng bắt đầu trong tương lai phải để trạng thái Chờ áp dụng');
            if ($validFrom <= $dateNow && $typeStatus == \App\Defines\Contract::FUTURE)
                throw new \Exception('Trạng thái Chờ áp dụng chỉ dùng cho hợp đồng có hiệu lực trong tương lai');
            if ($validFrom > $dateNow || $typeStatus == \App\Defines\Contract::FUTURE) {
                if (!Department::validateDepartmentPosition($data['department_id'], $data['position_id'], $contract->department_id, $contract->position_id, $contract->user_id)) {
                    return back()->with('err_position', trans('contracts.validate_position'))->withInput();
                }
            }

            //Xử lý phòng ban có nhiều loại tiền
            $dept = Department::where('status', 1)
                ->where('id', $data['department_id'])
                ->first();
            if (!$dept) throw new \Exception('Phòng ban không tồn tại');
            if (!$dept->is_multi_currency) {
                $data['currency_code'] = null;
            } else {
                if (!$data['currency_code']) throw new \Exception('Phải chọn loại tiền với phòng ban này.');
            }

            //$data['set_notvalid_on'] = $data['type'] == DefinesContract::TYPE_UNLIMITED ? null : date("Y-m-d 00:00:00", strtotime(str_replace('/', '-', $data['valid_to'])));
            if ($data['is_main'] == Staff::STATUS_PROBATIONARY) {
                $validator = \Validator::make($request->only(['valid_to']), [
                    'valid_to' => 'required',
                ]);
                $validator->setAttributeNames(trans('contracts'));
                if ($validator->fails()) return back()->withErrors($validator)->withInput();
                $data['valid_to'] = date("Y-m-d", strtotime(str_replace('/', '-', $data['valid_to'])));
                $data['type'] = null;
            }
            if ($data['is_main'] == Staff::STATUS_OFFICIAL) {
                $validator = \Validator::make($request->only(['type']), [
                    'type' => 'required',
                ]);
                $validator->setAttributeNames(trans('contracts'));
                if ($validator->fails()) return back()->withErrors($validator)->withInput();
                $data['valid_to'] = Contract::setValidTo($data['valid_from'], $data['type']);
            }
            $data['set_notvalid_on'] = empty($data['valid_to']) ? null : Carbon::parse($data['valid_to'])->addDay()->format('Y-m-d');

            $userUpdate = [];
            if ($typeStatus != \App\Defines\Contract::FUTURE) {
                if (Contract::checkStaffHasActiveContract($id)) {
                    return back()->with('err_status', 'Nhân viên đang có hợp đồng đang hoạt động thì không cập nhật hợp đồng khác!')->withInput();
                }
            }
            if (!$status) {
                if ($typeStatus != \App\Defines\Contract::FUTURE) {
                    if ($typeStatus == DefinesContract::EXPIRED) {
                        if ($data['type'] == DefinesContract::TYPE_UNLIMITED) {
                            return back()->with('err_status', 'Hợp đồng vô thời hạn không cho hết hạn!')->withInput();
                        }
                        if ($data['is_main'] == Staff::STATUS_PROBATIONARY) {
                            return back()->with('err_status', 'Hợp đồng thử việc chỉ chuyển sang trạng thái Hết hạn thử việc!')->withInput();
                        }
                        if ($contract->set_notvalid_on > now()) {
                            return back()->with('err_status', 'Chỉ chuyển hết hạn khi hợp đồng qua ngày kết thúc!')->withInput();
                        }
                        $data['status'] = $status;
                        $data['set_notvalid_by'] = Auth::id();
                    } else {
                        $staffSubmitDate = $request->input('staff_submit_date');
                        $setNotvalidDate = $request->input('set_notvalid_date');
                        $reportDate = $request->input('report_valid');
                        if ($data['is_main'] == Staff::STATUS_PROBATIONARY) {
                            $validator = \Validator::make($request->only(['set_notvalid_date', 'report_valid']), [
                                'set_notvalid_date' => 'required',
                            ]);
                            $validator->setAttributeNames(trans('contracts'));
                            if ($validator->fails()) return back()->withErrors($validator)->withInput();
                        } else {
                            $validator = \Validator::make($request->only(['set_notvalid_date', 'report_valid']), [
                                'set_notvalid_date' => 'required',
                                'report_valid' => 'sometimes|required',
                            ]);
                            $validator->setAttributeNames(trans('contracts'));
                            if ($validator->fails()) return back()->withErrors($validator)->withInput();
                        }
                        if ($typeStatus == \App\Defines\Contract::LEAVE_WORK) {
                            $validator = \Validator::make($request->only(['staff_submit_date', 'set_notvalid_date']), [
                                'staff_submit_date' => 'required',
                            ]);
                            $validator->setAttributeNames(trans('contracts'));
                            if ($validator->fails()) return back()->withErrors($validator)->withInput();
                        }
                        $data['is_leave'] = ($typeStatus == \App\Defines\Contract::LEAVE_WORK) ? 1 : null;
                        $data['status'] = $status;
                        $data['set_notvalid_on'] = date("Y-m-d", strtotime(str_replace('/', '-', $setNotvalidDate)));
                        $data['report_valid'] = date('Y-m-d', strtotime(str_replace('/', '-', $reportDate)));
                        $data['staff_submit_date'] = $staffSubmitDate ? date('Y-m-d', strtotime(str_replace('/', '-', $staffSubmitDate))) : null;
                        $data['set_notvalid_by'] = Auth::id();
                    }
                }


            } else {
                /*if (Contract::checkStaffHasActiveContract($id)) {
                    return back()->with('err_status', trans('contracts.validate_staff_contract'))->withInput();
                }*/
                $data['status'] = $status;
                //$data['set_notvalid_on'] = $contract->type == \App\Defines\Contract::TYPE_UNLIMITED ? null : date("Y-m-d 00:00:00", strtotime(str_replace('/', '-', $contract->valid_to)));
                $data['report_valid'] = null;
                $data['staff_submit_date'] = null;
                $data['set_valid_by'] = Auth::id();
                $data['set_valid_on'] = now()->format('Y-m-d H:m:s');
                $data['is_leave'] = null;
            }
            $data['department_group_id'] = Department::getGroupOfDept($data['department_id']);

            if ($typeStatus != \App\Defines\Contract::FUTURE) {
                if ($data['type_status'] != DefinesContract::CHO_NGHI_VIEC) {
                    $userUpdate = [
                        'company_id' => $status ? $data['company_id'] : null,
                        'department_id' => $status ? $data['department_id'] : null,
                        'position_id' => $status ? $data['position_id'] : null,
                        'qualification_id' => $status ? $data['qualification_id'] : null,
                        'status' => $status ? $data['is_main'] : null,
                        'active' => $status ? 1 : 0,
                        'dept_group_id' => $contract->department_group_id,
                        'is_leave' => $data['is_leave']
                    ];
                } else {
                    $userUpdate = [
                        'company_id' => $data['company_id'],
                        'department_id' => $data['department_id'],
                        'position_id' => $data['position_id'],
                        'qualification_id' => $data['qualification_id'],
                        'status' => $data['is_main'],
                        'active' => 1,
                        'dept_group_id' => $contract->department_group_id,
                        'is_leave' => null
                    ];
                }
            }

            $codeUser = User::find(intval($data['user_id']))->code;
            $companyName = Company::find(intval($data['company_id']))->shortened_name;
            $data['code'] = date("dmy", strtotime( str_replace('/', '-', $data['valid_from']) )) . '-' . $codeUser . '-' . strtoupper($companyName) . '/' . ($data['is_main'] == Staff::STATUS_OFFICIAL ? 'HDLD' : 'HDTV');
            $validator = \Validator::make($data, Contract::rules($id));
            $validator->setAttributeNames(trans('contracts'));
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            $check = !in_array($data['qualification_id'], \App\Defines\Contract::DRIVER_ID);
            if ($check || $data['is_main'] == Staff::STATUS_OFFICIAL) {
                $validator = \Validator::make($data, [
                    'basic_salary' => 'required',
                ]);
                $validator->setAttributeNames(trans('contracts'));
                if ($validator->fails()) return back()->withErrors($validator)->withInput();
            } else $data['basic_salary'] = 0;
            if ($contract->type_status = \App\Defines\Contract::FUTURE) {
                $data['is_used'] = 0;
            }
            $allowance_cat = array_values($request->input('allowance_cat'));
            $data['staff_id'] = $data['user_id'];

            DB::beginTransaction();
            $contract->update($data);
            $contract->user()->update($userUpdate);
            $oldAllowances = Allowance::where('contract_id', $contract->id)->pluck('active', 'category_id')->toArray();
            $contract->allowances()->delete();

            if ($allowance_cat[0]) {
                $isDeptWarehouse = Department::isDeptWarehouse($data['department_id']);
                if (count(array_unique($allowance_cat)) != count($allowance_cat)) {
                    return back()->with('err_allowance', trans('contracts.validate_allowance_category'))->withInput();
                }
                $allowance_cost = array_values($request->input('allowance_cost'));
                $desc = array_values($request->input('desc'));
                $allowances = [];
                $count = count($allowance_cat);
                for ($i = 0; $i < $count; $i++) {
                    if (empty($allowance_cost[$i])) {
                        return back()->with('err_allowance', trans('contracts.validate_allowance_cost'))->withInput();
                    }
                    $allowances[$i]['category_id'] = $allowance_cat[$i];
                    $allowances[$i]['expense'] = str_replace(',', '', $allowance_cost[$i]);
                    $allowances[$i]['desc'] = $desc[$i];
                    $allowances[$i]['active'] = $oldAllowances[$allowance_cat[$i]] ?? 1;
                    if ($isDeptWarehouse && $allowance_cat[$i] == 1) $allowances[$i]['type_dept'] = 1;
                }
                $contract->allowances()->createMany($allowances);
            }

            $fileEdits = $request->input('file_edits') ?? [];
            $contract->contractFiles()->whereNotIn('id', $fileEdits)->delete();
            if ($request->hasFile('file')) {
                $files = $request->file('file');
                $validator_file = \Validator::make($request->only('file'), ContractFile::rules(), [], ['file.*' => 'file']);
                if ($validator_file->fails()) {
                    return back()->withErrors($validator_file)->withInput();
                }
                foreach ($files as $file) {
                    $name = $file->getClientOriginalName();
                    $name = str_replace(' ', '-', trim($name));
                    $path = $file->move(\Config::get('upload.contracts'), $name);
                    $contract->contractFiles()->create([
                        'name' => $name,
                        'path' => $path,
                        'user_id' => $contract->user_id,
                        'status' => 1
                    ]);
                }
            }
            DB::commit();
            Session::flash('message', trans('system.success'));
            Session::flash('alert-class', 'success');
            return redirect()->route('admin.contracts.index');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('err_status', $e->getMessage().$e->getLine())->withInput();
        }
    }

    public function destroy($id)
    {
        $contract = Contract::find($id);
        if (is_null($contract) || $contract->type_status == \App\Defines\Contract::ACTIVE || $contract->type_statu != \App\Defines\Contract::FUTURE) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.contracts.index');
        }
        /*if ($contract->check_valid != \App\Defines\Contract::NOT_YET_VALID) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.contracts.index');
        }*/
        try {
            DB::beginTransaction();
            $contract->allowances()->delete();
            $contract->appendixAllowances3()->delete();
            $contract->contractFiles()->delete();

            $userId = $contract->user_id;
            $activeContract = Contract::where('user_id', $userId)
                ->where('id', '<>', $contract->id)
                ->first();
            if (is_null($activeContract)) {
                $user = User::find($userId);
                if ($user) {
                    $user->update([
                        'company_id' => null,
                        'department_id' => null,
                        'position_id' => null,
                        'qualification_id' => null,
                        'status' => null,
                        'active' => 0,
                        'dept_group_id' => null
                    ]);
                }

            }

            $contract->delete();
            DB::commit();
            Session::flash('message', trans('system.success'));
            Session::flash('alert-class', 'success');
            return back();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors($e->getMessage());
        }

    }

    public function setDepartmentOption(Request $request)
    {
        $response = ['message' => trans('system.have_an_error'), 'data' => ""];
        $statusCode = 400;
        if ($request->ajax()) {
            try {
                $userId = Auth::id();
                $companyId = $request->input('companyId');
                $route = $request->input('route');
                if (empty($companyId)) throw new \Exception(trans('system.no_item_selected'));
                $infoPermission = \App\PermissionUserObject::getMorePermissions($userId, $route);
                $query = '1=1';

                // $companyId == -1 cho Trường hợp chọn tất cả
                if ($companyId == -1) {
                    $query .= " AND status = 1";
                    if ($infoPermission) {
                        $deptId = Auth::user()->department_id;
                        $deptIdArr = $infoPermission['departments'];
                        if (!in_array($deptId, $deptIdArr)) array_push($deptIdArr, $deptId);
                        $query .= " AND departments.id IN(" . implode(',', $deptIdArr) . ")";
                    }
                }
                else {
                    $query .= " AND status = 1 AND company_id = {$companyId}";
                    if ($infoPermission) {
                        $deptArr = $infoPermission['detail'][$companyId] ?? [];
                        if ($deptArr) $query .= " AND id IN(" . implode(',', $deptArr) . ")";
                    }
                }

                $departmentsOption = Department::whereRaw($query)->with(['company'])->get();
                $result = [];
                if (count($departmentsOption)) {
                    foreach ($departmentsOption as $dept) {
                        $result[$dept->id] = $dept->company->shortened_name . ' - ' . $dept->name;
                    }
                }
                $statusCode = 200;
                return response()->json($result);
            } catch (\Exception $e) {
                $response['message'] = $e->getMessage();
                return response()->json($response, $statusCode);
            }
        } else {
            $statusCode = 405;
            return response()->json($response, $statusCode);
        }
    }

    public function validateManager(Request $request)
    {
        if ($request->ajax()) {
            $check = Department::validateDepartmentPosition($request->input('departmentId'), $request->input('positionId'), $request->input('oldDepartmentId'), $request->input('oldPositionId'), $request->input('userId'));
            return response()->json($check);
        }
    }

    public function checkStaffHasContract(Request $request)
    {
        if ($request->ajax()) {
            $staffId = $request->staffId;
            $user = User::find(intval($staffId));
            if ($user->active) {
                return response()->json([
                    'mess' => trans('contracts.validate_staff_contract'),
                    'data' => true
                ]);
            }
            if (!count(Contract::where('user_id', $staffId)->first())) {
                return response()->json([
                    'data' => false
                ]);
            }
            $contract = Contract::where('user_id', $staffId)->orderBy('id', 'desc')->first();
            if ($contract->type_status === \App\Defines\Contract::TRANSFER || $contract->type_status === \App\Defines\Contract::APPOINT) {
                $allowancesOption = AllowanceCategory::pluck('name', 'id')->toArray();
                $allowanceCat = [];
                $allowanceDesc = [];
                $allowanceCot = [];
                $allowances = $contract->allowances;
                if ($allowances) {
                    $allowanceDesc = $allowances->pluck('desc');
                    $allowanceCat = $allowances->pluck('category_id');
                    $allowanceCot = $allowances->pluck('expense');
                }
				// $departmentOption = Department::where('company_id', $contract->company->id)->pluck('name', 'id')->toArray();
                $template = view('backend.contracts._allowance', compact('allowanceDesc', 'allowanceCat', 'allowanceCot', 'allowancesOption'))->render();
                return response()->json([
                    'data' => true,
                    'template' => $template,
                    'contract' => $contract,
                    'count'     => count($allowanceCat)
                ]);
            }
            return response()->json([
                'data' => false
            ]);
        }
    }

    public function searchUserForSelect(Request $request)
    {
        $response = ['message' => trans('system.have_an_error'), 'data' => ""];
        $statusCode = 200;
        if ($request->ajax()) {
            try {
                $search = $request->input('search') ?? '';
                $page_limit = 20;
                $userLogin = Auth::user();
                $route = $request->input('route');
                $listDeptPermission = [];
                $infoPermission = \App\PermissionUserObject::getMorePermissions(Auth::id(), $route);
                if ($infoPermission) {
                    $listDeptPermission =  $infoPermission['departments'];
                    if (!in_array($userLogin->department_id, $listDeptPermission)) array_push($listDeptPermission, $userLogin->department_id);
                }
                $query = "1=1";
                /*$query .= User::getQueryPermission($infoPermission, Auth::id(), 'id', 'usermodule');*/
                /*$users = User::whereRaw($query)->whereNotIn('fullname', ['System', 'Administrator', 'KT LOG', 'KT PAC'])
                    ->whereNull('is_leave')
                    ->where(function ($q) use ($search) {
                        $q->where('fullname', 'LIKE', '%' . $search . '%')
                            ->orWhere('code', 'LIKE', '%' . $search . '%');
                    })->where
                    ->orderBy('created_at', 'desc')
                    ->orderBy('active', 'asc')
                    ->paginate($page_limit);*/

                // Tìm tất cả user chưa có hợp đồng, hoặc những user có hợp đồng thuộc phòng ban trong phân quyền
                // Trường hợp có phân quyền
                if ($listDeptPermission) {
                    $users = User::whereNotIn('fullname', Staff::USER_EXCEPT)
                        ->where('active', 0)
                        ->whereNull('is_leave')
                        ->where(function ($q) use ($listDeptPermission) {
                            $q->doesntHave('contracts')
                                ->orwhereHas('contracts', function ($q1) use ($listDeptPermission) {
                                    $q1->whereIn('contracts.department_id', $listDeptPermission)
                                        ->orWhere('contracts.created_by', Auth::id());
                                })->orWhere('users.created_by', Auth::id());
                        })->where(function ($q) use ($search) {
                            $q->where('fullname', 'LIKE', '%' . $search . '%')
                                ->orWhere('code', 'LIKE', '%' . $search . '%');
                        })->orderBy('updated_at', 'desc')
                        ->orderBy('created_at', 'desc')
                        ->paginate($page_limit);
                } else {
                    // TH full quyền []
                    $users = User::whereNotIn('fullname', Staff::USER_EXCEPT)
                        ->where('active', 0)
                        ->whereNull('is_leave')
                        ->where(function ($q) use ($search) {
                            $q->where('fullname', 'LIKE', '%' . $search . '%')
                                ->orWhere('code', 'LIKE', '%' . $search . '%');
                        })->orderBy('updated_at', 'desc')
                        ->orderBy('created_at', 'desc')
                        ->paginate($page_limit);
                }

                $response['data'] = $users;
                $response['message'] = trans('system.success');
            } catch (\Exception $e) {
                if ($statusCode == 200) $statusCode = 500;
                $response['message'] = $e->getMessage();
                $response['data'] = 'fail';
            } finally {
                return response()->json($response, $statusCode);
            }
        } else {
            $statusCode = 405;
            return response()->json($response, $statusCode);
        }
    }

    public function setOldUser(Request $request)
    {
        $response = ['message' => trans('system.have_an_error'), 'data' => ""];
        if ($request->ajax()) {
            $userId = $request->input('userId');
            $user = User::find($userId);
            return response()->json($user);
        } else {
            $statusCode = 405;
            return response()->json($response, $statusCode);
        }
    }

    public function download($id)
    {
        $contractFile = ContractFile::find($id);
        if (is_null($contractFile)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.contracts.index');
        }
        $file = public_path() . '/' . $contractFile->path;

		// $headers = [
		// 	'Content-Type: application/xls',
		// ];
        return response()->download($file, $contractFile->name);
    }

    public function createBulk(Request $request)
    {
        return view('backend.contracts.create_multi');
    }

    public function downloadExcel(Request $request)
    {
        $file = public_path() . "/assets/media/files/templates/contracts-12Jun.xlsx";
        $headers = [
            'Content-Type: application/xls',
        ];
        return response()->download($file, 'template-contracts-' . time() . '.xlsx', $headers);
    }

    public function setAllowanceDefault(Request $request)
    {
        $response = ['message' => trans('system.have_an_error'), 'data' => ""];
        $statusCode = 200;
        if ($request->ajax()) {
            try {
                $deptId = $request->deptId;
                $allowances = AllowanceCategory::where('status', 1)->get();
                $allowanceCat = [];
                $allowanceDesc = [];
                foreach ($allowances as $item) {
                    if (in_array($deptId, explode(',', $item->department))) {
                        array_push($allowanceCat, $item->id);
                        array_push($allowanceDesc, $item->desc);
                    }
                }
                $allowancesOption = AllowanceCategory::pluck('name', 'id')->toArray();
                $response['template'] = view('backend.contracts._allowance', compact('allowanceDesc', 'allowanceCat', 'allowancesOption'))->render();
                $response['count'] = count($allowanceCat);
                $response['message'] = trans('system.success');
            } catch (\Exception $e) {
                if ($statusCode == 200) $statusCode = 500;
                $response['message'] = $e->getMessage();
            } finally {
                return response()->json($response, $statusCode);
            }
        } else {
            $statusCode = 405;
            return response()->json($response, $statusCode);
        }
    }

    public function showModalExport(Request $request)
    {
        $response = ['message' => trans('system.have_an_error'), 'data' => ""];
        $statusCode = 200;
        if ($request->ajax()) {
            try {
                $id = $request->id;
                $item = Contract::find(intval($id));
                if (is_null($item)) {
                    $message = trans('Hợp đồng không tồn tại.');
                    throw new \Exception($message, 1);
                }
                $response['template'] = view('backend.contracts.partitions._modal_export', compact('item'))->render();
                $response['message'] = trans('system.success');
            } catch (\Exception $e) {
                if ($statusCode == 200) $statusCode = 500;
                $response['message'] = $e->getMessage();
            } finally {
                return response()->json($response, $statusCode);
            }
        } else {
            $statusCode = 405;
            return response()->json($response, $statusCode);
        }
    }

    public function readBulk(Request $request)
    {
        ini_set('memory_limit', '4096M');
        $response = ['message' => trans('system.have_an_error')];
        $statusCode = 200;
        if ($request->ajax()) {
            try {
                $file = $request->file;
                switch ($file->getClientOriginalExtension()) {
                    case 'xlsx':
                        $data = \Excel::toArray(new \App\Imports\ContractsImport, $file);
                        if ($data) $data = $data[0];
                        // dd($data);
                        if (count($data) == 1 || !isset($data[1][0]) || count($data[1]) < 24) {
                            throw new \Exception("Không được sửa dòng đầu tiên của file mẫu nhập liệu", 1);
                        }
                        // if (count($data[1]) > 24) {
                        //     throw new \Exception("File excel chỉ đến cột W, vui lòng xoá các cột khác" . json_encode($data[1]), 1);
                        // }
                        if (!isset($data[2][0])) {
                            throw new \Exception("File tải lên không có dữ liệu", 1);
                        }
                        $response['message'] = view('backend.contracts.excel', compact('data'))->render();
                        break;
                    default:
                        throw new \Exception("Không hỗ trợ định dạng", 1);
                }
            } catch (\Exception $e) {
                if ($statusCode == 200) $statusCode = 500;
                $response['message'] = $e->getMessage();
            } finally {
                return response()->json($response, $statusCode);
            }
        } else {
            $statusCode = 405;
            return response()->json($response, $statusCode);
        }
    }

    public function saveBulk(Request $request)
    {
        $response = ['message' => trans('system.have_an_error')];
        $statusCode = 200;
        if ($request->ajax()) {
            try {
                $data = $request->data;
                if (!is_array($data) || count($data) == 0) {
                    $statusCode = 400;
                    throw new \Exception(trans('system.have_an_error'), 1);
                }
                $staffCodes = [];
                foreach ($data as $d) {
                    if (!is_array($d) || count($d) <> 24) {
                        throw new \Exception("Dữ liệu dòng số " . ($d[0] ?? "") . " khác 24 cột");
                    }
                    if (!isset($d[2]) || trim($d[2]) == "") {
                        throw new \Exception("Mã nhân viên dòng số " . ($d[0] ?? "") . " không được bỏ trống");
                    }
                    if (isset($staffCodes[trim($d[2])])) {
                        throw new \Exception("Mã nhân viên dòng số " . ($d[0] ?? "") . " đã có bên trên");
                    }
                    $staffCodes[trim($d[2])] = $d[0] ?? "";
                }
                $staffs = User::whereIn('code', array_keys($staffCodes))->get()->keyBy('code');
                if ($staffs->count() <> count($staffCodes)) {
                    foreach($staffCodes as $staffCode => $no) {
                        if (!isset($staffs[$staffCode])) {
                            throw new \Exception("Mã nhân viên tại dòng số " . $no . " không tìm thấy trong dữ liệu Nhân viên");
                        }
                    }
                }
                foreach($staffs as $staff) {
                    if ($staff->active == 1) {
                        throw new \Exception("Nhân viên {$staff->fullname} tại dòng số " . $staffCodes[$staff->code] . " đang có hợp đồng còn hiệu lực");
                    }
                }
                // $staffsIds = array_column($staffs->toArray(), 'code', 'id');
                // $staffContract = Contract::whereIn('staff_id', array_keys($staffsIds))->where('type_status', \App\Defines\Contract::ACTIVE)->where('status', 1)->first();
                // if (!is_null($staffContract)) {
                //     throw new \Exception("Nhân viên tại dòng số " . $staffCodes[$staffsIds[$staffContract->staff_id]] . " đang có hợp đồng còn hiệu lực");
                // }
                $contractData = [];
                $existedCompanies = Company::selectRaw("id, LOWER(shortened_name) as shortened_name")->pluck('id', 'shortened_name')->toArray();
                $existedDepts = Department::select('name', 'id', 'company_id')->get()->keyBy('id')->toArray();
                $existedPositions = Position::selectRaw("id, LOWER(name) as name")->pluck('id', 'name')->toArray();
                $existedQualifications = Qualification::selectRaw("id, LOWER(name) as name, description")->get()->keyBy('name')->toArray();
                foreach($existedPositions as $k => $v) {
                    unset($existedPositions[$k]);
                    $existedPositions[\App\Helper\HString::removeVietnameseSign($k)] = $v;
                }
                foreach($existedQualifications as $k => $v) {
                    unset($existedQualifications[$k]);
                    $existedQualifications[\App\Helper\HString::removeVietnameseSign($k)] = [
                        'id'            => $v['id'],
                        'description'   => $v['description'],
                    ];
                }
                $existedDeptInCompanies = [];
                foreach ($existedDepts as $deptId => $deptInfo) {
                    if (!isset($existedDeptInCompanies[$deptInfo['company_id']])) {
                        $existedDeptInCompanies[$deptInfo['company_id']] = [];
                    }
                    $existedDeptInCompanies[$deptInfo['company_id']][strtolower(\App\Helper\HString::removeVietnameseSign($deptInfo['name']))] = $deptInfo['id'];
                }
                foreach ($data as $d) {
                    $code = trim($d[1]);
                    $staffCode = trim($d[2]);
                    $company = strtolower(\App\Helper\HString::removeVietnameseSign(trim($d[4])));
                    $deptCode = strtolower(\App\Helper\HString::removeVietnameseSign(trim($d[5])));
                    if (!isset($existedCompanies[$company])) throw new \Exception("Kiểm tra lại Công ty tại dòng số " . ($d[0] ?? "") . "");
                    $depts = $existedDeptInCompanies[$existedCompanies[$company]];
                    if (!isset($depts[$deptCode])) throw new \Exception("Kiểm tra lại Phòng ban tại dòng số " . ($d[0] ?? "") . "");
                    $position = strtolower(\App\Helper\HString::removeVietnameseSign(trim($d[6])));
                    $qualification = strtolower(\App\Helper\HString::removeVietnameseSign(trim($d[7])));
                    $desQualification = trim($d[8]);
                    if (!isset($existedPositions[$position])) throw new \Exception("Kiểm tra lại Cấp bậc tại dòng số " . ($d[0] ?? "") . "");
                    if (!Department::validateDepartmentPosition($depts[$deptCode], $existedPositions[$position], null, null)) throw new \Exception("Kiểm tra lại Cấp bậc TRONG PHÒNG tại dòng số " . ($d[0] ?? "") . "");
                    if (!isset($existedQualifications[$qualification])) throw new \Exception("Kiểm tra lại Chức danh tại dòng số " . ($d[0] ?? "") . "");
                    $typeContract = strtolower(\App\Helper\HString::removeVietnameseSign(trim($d[9])));
                    if ($typeContract == "chinh thuc") {
                        $typeContract = Staff::STATUS_OFFICIAL;
                    } elseif ($typeContract == "thu viec") {
                        $typeContract = Staff::STATUS_PROBATIONARY;
                    } else {
                        throw new \Exception("Kiểm tra lại Loại hợp đồng tại dòng số " . ($d[0] ?? "") . ": Chính thức hoặc thử việc");
                    }
                    $periodContract = strtolower(\App\Helper\HString::removeVietnameseSign(trim($d[10])));
                    $stringAddTime = "";
                    if ($typeContract <> Staff::STATUS_PROBATIONARY) {
                        if ($periodContract == "vo thoi han") {
                            $periodContract = \App\Defines\Contract::TYPE_UNLIMITED;
                        } elseif ($periodContract == "6 thang") {
                            $periodContract = \App\Defines\Contract::TYPE_6_MONTH;
                            $stringAddTime = "+6 months";
                        } elseif ($periodContract == "1 nam") {
                            $periodContract = \App\Defines\Contract::TYPE_1_YEAR;
                            $stringAddTime = "+12 months";
                        } elseif ($periodContract == "3 nam") {
                            $periodContract = \App\Defines\Contract::TYPE_3_YEAR;
                            $stringAddTime = "+36 months";
                        } else {
                            throw new \Exception("Kiểm tra lại Thời hạn hợp đồng tại dòng số " . ($d[0] ?? "") . ": 6 tháng, 1 năm, 3 năm, Vô thời hạn");
                        }
                    } else {
                        $periodContract = null;
                    }
                    $validFrom = trim($d[11]);
                    $validFrom = strtotime(str_replace('/', '-', $validFrom));
                    if (!$validFrom) throw new \Exception("Kiểm tra lại Hiệu lực từ tại dòng số " . ($d[0] ?? "") . "");
                    $validTo = 0;
                    if ($typeContract == Staff::STATUS_PROBATIONARY) {
                        $validTo = trim($d[12]);
                        $validTo = strtotime(str_replace('/', '-', $validTo));
                        if (!$validTo) throw new \Exception("Kiểm tra lại Hiệu lực đến tại dòng số " . ($d[0] ?? "") . "");
                    }
                    $baseSalary = intval($d[13]);
                    $allowances = [];
                    $alwLunch = intval($d[14]);
                    $alwTravel = intval($d[15]);
                    $alwResponsibility = intval($d[16]);
                    $alwContribution = intval($d[17]);
                    $alwPerformance = intval($d[18]);
                    $alwTelephone = intval($d[19]);
                    $alwWork = intval($d[20]);
                    $alwSpecified = intval($d[21]);
                    $alwSpecified = intval($d[21]);
                    $alwAttendance = intval($d[22]);
                    $alwOther = intval($d[23]);
                    if ($alwLunch > 0) array_push($allowances, ['category_id' => 1, 'expense' => $alwLunch, 'desc' => 'excel']);
                    if ($alwTravel > 0) array_push($allowances, ['category_id' => 2, 'expense' => $alwTravel, 'desc' => 'excel']);
                    if ($alwResponsibility > 0) array_push($allowances, ['category_id' => 3, 'expense' => $alwResponsibility, 'desc' => 'excel']);
                    if ($alwContribution > 0) array_push($allowances, ['category_id' => 4, 'expense' => $alwContribution, 'desc' => 'excel']);
                    if ($alwPerformance > 0) array_push($allowances, ['category_id' => 5, 'expense' => $alwPerformance, 'desc' => 'excel']);
                    if ($alwTelephone > 0) array_push($allowances, ['category_id' => 6, 'expense' => $alwTelephone, 'desc' => 'excel']);
                    if ($alwWork > 0) array_push($allowances, ['category_id' => 7, 'expense' => $alwWork, 'desc' => 'excel']);
                    if ($alwSpecified > 0) array_push($allowances, ['category_id' => 8, 'expense' => $alwSpecified, 'desc' => 'excel']);
                    if ($alwAttendance > 0) array_push($allowances, ['category_id' => 10, 'expense' => $alwAttendance, 'desc' => 'excel']);
                    if ($alwOther > 0) array_push($allowances, ['category_id' => 9, 'expense' => $alwOther, 'desc' => 'excel']);
                    if ($baseSalary < 1) {
                        throw new \Exception("Kiểm tra lại Lương cơ bản tại dòng số " . ($d[0] ?? "") . "");
                    }
                    array_push($contractData, [
                        'user_id' => $staffs[$staffCode]->id,
                        'type'      => $periodContract,
                        'code'      => $code ? $code : (date("dmy", $validFrom) . '-' . $staffs[$staffCode]->code . '-' . strtoupper($company) . '/' . ($typeContract == Staff::STATUS_OFFICIAL ? 'HDLD' : 'HDTV')),
                        'qualification_id'      => $existedQualifications[$qualification]['id'],
                        'desc_qualification'    => $desQualification ? $desQualification : $existedQualifications[$qualification]['description'],
                        'company_id'    => $existedCompanies[$company],
                        'department_id' => $depts[$deptCode],
                        'position_id'   => $existedPositions[$position],
                        'type_status'   => \App\Defines\Contract::ACTIVE,
                        'status'        => 1,
                        'is_main'       => $typeContract,
                        'valid_from'    => date("Y-m-d", $validFrom), // $typeContract == Staff::STATUS_OFFICIAL ? : null,

                        'valid_to'      => $typeContract == Staff::STATUS_OFFICIAL ? ($periodContract == \App\Defines\Contract::TYPE_UNLIMITED ? null : (date("Y-m-d", strtotime($stringAddTime, $validFrom)))) : date("Y-m-d", $validTo),
                        'set_notvalid_on' => $typeContract == Staff::STATUS_OFFICIAL ? ($periodContract == \App\Defines\Contract::TYPE_UNLIMITED ? null : (date("Y-m-d", strtotime($stringAddTime, $validFrom)))) : date("Y-m-d", $validTo),
                        'staff_id'      => $staffs[$staffCode]->id,
                        'title_id'      => $existedPositions[$position],
                        'type_status'   => \App\Defines\Contract::ACTIVE,
                        'department_group_id' => Department::getGroupOfDept($depts[$deptCode]),
                        'basic_salary'  => $baseSalary,
                        'allowances'    => $allowances,
                    ]);
                }
                foreach ($contractData as $cData) {
                    $allowances = $cData['allowances'];
                    unset($cData['allowances']);
                    $contract = Contract::create($cData);
                    $contract->allowances()->createMany($allowances);
                }
                $response['message'] = trans('system.success');
                Session::flash('message', $response['message']);
                Session::flash('alert-class', 'success');

            } catch (\Exception $e) {
                if ($statusCode == 200) $statusCode = 500;
                $response['message'] = $e->getMessage();
            } finally {
                return response()->json($response, $statusCode);
            }
        } else {
            $statusCode = 405;
            return response()->json($response, $statusCode);
        }
    }

    public function cancelConcurrent(Request $request)
    {
        $response = ['message' => trans('system.have_an_error')];
        $statusCode = 200;
        if ($request->ajax()) {
            try {
                $concurrent = ConcurrentContract::find(intval($request->input('id')));
                if (!is_null($concurrent)) {
                    $concurrent->status = 0;
                    $concurrent->save();
                }
                
                $response['message'] = trans('system.success');
                Session::flash('message', $response['message']);
                Session::flash('alert-class', 'success');

            } catch (\Exception $e) {
                DB::rollBack();
                if ($statusCode == 200) $statusCode = 500;
                $response['message'] = $e->getMessage();
            } finally {
                return response()->json($response, $statusCode);
            }
        } else {
            $statusCode = 405;
            return response()->json($response, $statusCode);
        }
    }
}
