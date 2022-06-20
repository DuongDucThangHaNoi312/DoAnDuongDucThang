<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Department;
use App\Models\OverTimes;
use App\Models\Shift;
use App\PermissionUserObject;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Response;

class OverTimeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        // $query = "1=1";
        // $name = $request->input('name');
        // $company = $request->input('company_id');
        // $department = $request->input('department_id');
        // $status = $request->input('status', -1);
        // $start_date = $request->get('start_date') ? Carbon::createFromFormat('d/m/Y', $request->get('start_date'))->toDateString() : '';
        // $end_date = $request->get('end_date') ? Carbon::createFromFormat('d/m/Y', $request->get('end_date'))->toDateString() : '';
        // if ($name) $query .= " AND name like '%" . $name . "%'";
        // if ($company) $query .= " AND company_id = '{$company}'";
        // if ($department) $query .= " AND department_id = '{$department}'";
        // if ($status <> -1) $query .= " AND status = '{$status}'";
        // if ($start_date) $query .= " AND start_date = '{$start_date}'";
        // if ($end_date) $query .= " AND end_date = '{$end_date}'";

        // if(Auth::user()->hasRole('TP') &&  !Auth::user()->hasRole('TGD') || Auth::user()->hasRole('TPNS') && !Auth::user()->hasRole('TGD')){
        //     $department_group = Department::departmentsRole();
        //     $departmentID = Auth::user()->department_id;
        //     if (count($department_group) > 1) {
        //         $str = implode(", ", $department_group);
        //         $query .= " AND department_id IN  ({$str})";
        //     } else {
        //         $query .= " AND department_id =  '{$departmentID}'";
        //     }
        //     $companysOption = Company::companysOption();
        //     $departmentOption = Department::departmentsOption();
        // }
        // if (Auth::user()->hasRole('LEADER')) {
        //     $user = User::find(Auth::user()->id);
        //     if ($user->company) $query .= " AND company_id = '{$user->company->id}'";
        //     if ($user->department) $query .= " AND department_id = '{$user->department->id}'";
        //     if ($user) $query .= " AND created_by = '{$user->id}'";
        // }
        $query = PermissionUserObject::getQueryPermission(Auth::id());
        $overtime = OverTimes::whereRaw($query)->orderBy('id', 'DESC')->get();

        return view('backend.overtime.index', compact('overtime','department_group','companysOption','departmentOption'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(Auth::user()->hasRole('TP') &&  !Auth::user()->hasRole('TGD') || Auth::user()->hasRole('TPNS') && !Auth::user()->hasRole('TGD')){
            $department_group = Department::departmentsRole();
            $companysOption = Company::companysOption();
        }

        return view('backend.overtime.create',compact('department_group','companysOption'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->merge(['status' => intval($request->input('status', 0))]);
        $data = $request->all();
        
        $validator = Validator::make($data, OverTimes::rules());
        if ($data['end_date'] != null) {
            $data['start_date'] = Carbon::createFromFormat('d/m/Y', $request->get('start_date'))->toDateString();
            $data['end_date'] = Carbon::createFromFormat('d/m/Y', $request->get('end_date'))->toDateString();
            $validator = Validator::make($data, [
                'end_date' => 'required',
                'overtime_hours' => 'required',
            ]);
        }
        if ($data['display_with_type'] == 2) {
            $validator = Validator::make($data, [
                'display_with_data' => 'required',
                'overtime_hours' => 'required',
            ]);

        }
        $validator->setAttributeNames(trans('overtimes'));
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $data['start_date'] = Carbon::createFromFormat('d/m/Y', $request->get('start_date'));
        $data['end_date'] != null ? $data['end_date'] = Carbon::createFromFormat('d/m/Y', $request->get('end_date')) : '';
        if ($data['end_date']) {
            $data['status'] = 2;
        }
        if ($data['end_date'] == null && $data['status'] == 1) {
            $dayStart = Carbon::createFromFormat('d/m/Y', $request->get('start_date'))->day;
            $monthStart = Carbon::createFromFormat('d/m/Y', $request->get('start_date'))->month;
            $yearStart = Carbon::createFromFormat('d/m/Y', $request->get('start_date'))->year;
            $dayStart > 25 ? $data['end_date'] = Carbon::create($yearStart, $monthStart + 1, 25) : $data['end_date'] = Carbon::create($yearStart, $monthStart, 25);

        }
        // if ($data['shifts']) {
        //     $expect = $this->expectUserOption($request);
        //     if ($data['hidden_with_users'] == 'null') {
        //         $data['hidden_with_users'] = json_decode($expect);
        //     } else {
        //         $data['hidden_with_users'] = array_merge($data['hidden_with_users'], json_decode($expect));
        //     }
        // }

        $data['display_with_data'] = json_encode($data['display_with_data']);
        $data['hidden_with_users'] = json_encode($data['hidden_with_users']);
        $data['created_by'] = Auth::user()->id;

        OverTimes::create($data);
        Session::flash('message', trans('system.success'));
        Session::flash('alert-class', 'success');
        return redirect()->route('admin.overtimes.index');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $overtime = OverTimes::find(intval($id));
        if (is_null($overtime)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.overtimes.index');
        }
        $departmentOption = Department::where('company_id', $overtime->company_id)->pluck('name', 'id')->toArray();

        $userOption = User::where('company_id', $overtime->company_id)->where('department_id', $overtime->department_id)->pluck('fullname', 'id')->toArray();

        $shiftsOption = '';
        if ($overtime->shifts == 1 || $overtime->shifts == 2 || $overtime->shifts == 3) {
            $shiftsOption = [
                '1' => 'Ngày',
                '3' => 'Đêm',
            ];
        } else if ($overtime->shifts == 4 || $overtime->shifts == 5) {
            $shiftsOption = [
                '4' => 'Ca 4',
                '5' => 'Ca 5',
            ];
        }
        return view('backend.overtime.show', compact('overtime', 'departmentOption', 'userOption','shiftsOption'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $overtime = OverTimes::find(intval($id));
        if (is_null($overtime)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.overtimes.index');
        }
        $departmentOption = Department::where('company_id', $overtime->company_id)->pluck('name', 'id')->toArray();
        $userOption = User::where('company_id', $overtime->company_id)->where('department_id', $overtime->department_id)->pluck('fullname', 'id')->toArray();

        $shiftsOption = '';
        if ($overtime->shifts == 1 || $overtime->shifts == 2 || $overtime->shifts == 3 || $overtime->shifts == 4) {
            $shiftsOption = [
                '1' => 'Ngày',
                '3' => 'Đêm',
            ];
        } 
        // else if ($overtime->shifts == 4 || $overtime->shifts == 5) {
        //     $shiftsOption = [
        //         '4' => 'Kíp 1',
        //         '5' => 'Kíp 2',
        //     ];
        // }
        if(Auth::user()->hasRole('TP') &&  !Auth::user()->hasRole('TGD') || Auth::user()->hasRole('TPNS') && !Auth::user()->hasRole('TGD')){
            $department_group = Department::departmentsRole();
            $companysOption = Company::companysOption();
            if(in_array($overtime->department_id,$department_group)){
                $departmentsOption = Department::where('id', $overtime->department_id)->pluck('name', 'id')->toArray();
            }
        }
        return view('backend.overtime.edit', compact('overtime', 'departmentOption', 'userOption', 'shiftsOption','department_group','companysOption','departmentsOption'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $request->merge(['status' => intval($request->input('status', 0))]);
        $data = $request->all();
        $validator = Validator::make($data, OverTimes::rules());
        if ($data['end_date'] != null) {
            $data['start_date'] = Carbon::createFromFormat('d/m/Y', $request->get('start_date'))->toDateString();
            $data['end_date'] = Carbon::createFromFormat('d/m/Y', $request->get('end_date'))->toDateString();
            $validator = Validator::make($data, [
                'end_date' => 'required|after:start_date',
                'overtime_hours' => 'required',
            ]);
        }
        if ($data['display_with_type'] == 2) {
            $validator = Validator::make($data, [
                'display_with_data' => 'required',
                'overtime_hours' => 'required',
            ]);
        }
        $validator->setAttributeNames(trans('overtimes'));
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $overtime = OverTimes::find(intval($id));
        if (is_null($overtime)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.overtimes.index');
        }

        $data['start_date'] = Carbon::createFromFormat('d/m/Y', $request->get('start_date'));
        $data['end_date'] != null ? $data['end_date'] = Carbon::createFromFormat('d/m/Y', $request->get('end_date')) : '';
        if ($data['end_date']) {
            $data['status'] = 2;
        }
        if ($data['end_date'] == null && $data['status'] == 1) {
            $dayStart = Carbon::createFromFormat('d/m/Y', $request->get('start_date'))->day;
            $monthStart = Carbon::createFromFormat('d/m/Y', $request->get('start_date'))->month;
            $yearStart = Carbon::createFromFormat('d/m/Y', $request->get('start_date'))->year;
            $dayStart > 25 ? $data['end_date'] = Carbon::create($yearStart, $monthStart + 1, 25) : $data['end_date'] = Carbon::create($yearStart, $monthStart, 25);

        }
        // if ($data['shifts']) {
        //     $expect = $this->expectUserOption($request);
        //     if ($data['hidden_with_users'] == 'null') {
        //         $data['hidden_with_users'] = json_decode($expect);
        //     } else {
        //         $data['hidden_with_users'] = array_merge($data['hidden_with_users'], json_decode($expect));
        //     }
        // }
        $data['display_with_data'] = json_encode($data['display_with_data']);
        $data['hidden_with_users'] = json_encode($data['hidden_with_users']);
        $data['created_by'] = Auth::user()->id;

        $overtime->update($data);
        Session::flash('message', trans('system.success'));
        Session::flash('alert-class', 'success');
        return redirect()->route('admin.overtimes.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $overtime = OverTimes::find(intval($id));
        if (is_null($overtime)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.overtimes.index');
        }
        $overtime->delete();
        Session::flash('message', trans('system.success'));
        Session::flash('alert-class', 'success');
        return redirect()->route('admin.overtimes.index');
    }

    public function setUserOption(Request $request)
    {
        $response = ['message' => trans('system.have_an_error'), 'data' => ""];
        if ($request->ajax()) {
            $companyId = $request->input('companyId');
            $departmentId = $request->input('departmentId');
            if (empty($companyId)) return response()->json(['error' => true, 'message' => trans('system.no_item_selected')]);
            if (empty($departmentId)) return response()->json(['error' => true, 'message' => trans('system.no_item_selected')]);
            $userOption = User::where('company_id', $companyId)->where('department_id', $departmentId)->pluck('fullname', 'id')->toArray();
            return response()->json($userOption);
        } else {
            $statusCode = 405;
            return response()->json($response, $statusCode);
        }
    }

    public function setShiftsOption(Request $request)
    {
        $response = ['message' => trans('system.have_an_error'), 'data' => ""];
        if ($request->ajax()) {
            $departmentId = $request->input('departmentId');
            if (empty($departmentId)) return response()->json(['error' => true, 'message' => trans('system.no_item_selected')]);
            $status = Department::findOrFail($departmentId);
            return response()->json($status);
        } else {
            $statusCode = 405;
            return response()->json($response, $statusCode);
        }
    }

    public function setEndDate(Request $request)
    {
        $response = ['message' => trans('system.have_an_error'), 'data' => ""];
        if ($request->ajax()) {

            $startDate = Carbon::createFromFormat('d/m/Y', $request->get('startDate'));
            $departmentId = $request->input('departmentId');
            if (empty($departmentId)) return response()->json(['error' => true, 'message' => trans('system.no_item_selected')]);
            if (empty($startDate)) return response()->json(['error' => true, 'message' => trans('system.no_item_selected')]);
            $shifts = DB::table('department_working_day')->where('department_id', $departmentId)->get();
            $endDate = '';
            foreach ($shifts as $shift) {
                if ($startDate->between($shift->start_date, $shift->end_date)) {
                    $endDate = $shift->end_date;
                }
            }
            return response()->json($endDate);
        } else {
            $statusCode = 405;
            return response()->json($response, $statusCode);
        }
    }

    public function expectUserOption(Request $request)
    {

        $startDate = Carbon::createFromFormat('d/m/Y', $request->get('start_date'));
        $shifts = $request->input('shifts');
        $departmentId = $request->input('department_id');
        $people = '';
        if (empty($departmentId)) return response()->json(['error' => true, 'message' => trans('system.no_item_selected')]);
        if (empty($shifts)) return response()->json(['error' => true, 'message' => trans('system.no_item_selected')]);
        $shiftWork = DB::table('department_working_day')->where('department_id', $departmentId)->get();
        foreach ($shiftWork as $shift) {
            if ($startDate->between($shift->start_date, $shift->end_date)) {
                $people = DB::table('department_working_day')
                    ->where('department_id', $departmentId)
                    ->where('start_date', $shift->start_date)
                    ->where('end_date', $shift->end_date)
                    ->first();
                if ($shifts == 1) {
                    $people = $people->first_shift;
                } else if ($shifts == 2) {
                    $people = $people->second_shift;
                } else if ($shifts == 3) {
                    $people = $people->third_shift;
                } else if ($shifts == 4) {
                    $people = $people->first_shift_and_ot;
                } else if ($shifts == 5) {
                    $people = $people->second_shift_and_ot;
                }

            }
        }
        return $people;
    }

    public function setUserOptionForShift(Request $request)
    {
        $response = ['message' => trans('system.have_an_error'), 'data' => ""];
        if ($request->ajax()) {
            $startDate = Carbon::createFromFormat('d/m/Y', $request->get('startDate'));
            $departmentId = $request->input('departmentId');
            $shifts = $request->input('shifts');
            if (empty($departmentId)) return response()->json(['error' => true, 'message' => trans('system.no_item_selected')]);
            $shiftWork = DB::table('department_working_day')->where('department_id', $departmentId)->get();
            foreach ($shiftWork as $shift) {
                if ($startDate->between($shift->start_date, $shift->end_date)) {
                    $people = DB::table('department_working_day')
                        ->where('department_id', $departmentId)
                        ->where('start_date', $shift->start_date)
                        ->where('end_date', $shift->end_date)
                        ->first();
                    $userOption = DB::table('users')->where('department_id', $departmentId);
                    if ($shifts == 1) {
                        if ($people->first_shift != "null") {
                            $people = $userOption->whereNotIn('id', json_decode($people->first_shift))->pluck('fullname', 'id')->toArray();
                        } else {
                            $people = $userOption->pluck('fullname', 'id')->toArray();
                        }
                    } else if ($shifts == 2) {
                        if ($people->second_shift != "null") {
                            $people = $userOption->whereNotIn('id', json_decode($people->second_shift))->pluck('fullname', 'id')->toArray();
                        } else {
                            $people = $userOption->pluck('fullname', 'id')->toArray();
                        }
                    } else if ($shifts == 3) {
                        if ($people->third_shift != "null") {
                            $people = $userOption->whereNotIn('id', json_decode($people->third_shift))->pluck('fullname', 'id')->toArray();
                        } else {
                            $people = $userOption->pluck('fullname', 'id')->toArray();
                        }
                    } else if ($shifts == 4) {
                        if ($people->first_shift_and_ot != "null") {
                            $people = $userOption->whereNotIn('id', json_decode($people->first_shift_and_ot))->pluck('fullname', 'id')->toArray();
                        } else {
                            $people = $userOption->pluck('fullname', 'id')->toArray();
                        }
                    } else if ($shifts == 5) {
                        if ($people->second_shift_and_ot != "null") {
                            $people = $userOption->whereNotIn('id', json_decode($people->second_shift_and_ot))->pluck('fullname', 'id')->toArray();
                        } else {
                            $people = $userOption->pluck('fullname', 'id')->toArray();
                        }
                    }
                }
            }
            return response()->json($people);
        } else {
            $statusCode = 405;
            return response()->json($response, $statusCode);
        }
    }

    public function checkOverTime($data)
    {

        $overtimes = OverTimes::where('company_id', $data['company_id'])->where('department_id', $data['department_id'])->get();

        foreach ($overtimes as $OT) {
            if ($data['display_with_type'] == 1) {
                if ($data['hidden_with_users']) {
                    if ($data['hidden_with_users'] != json_decode($OT->display_with_data)) {
                        if ($OT->start_date >= $data['start_date'] && $OT->start_date <= $data['end_date']) {
                            return false;
                        }
                        if ($data['start_date'] >= $OT->start_date && $data['start_date'] <= $OT->end_date) {
                            return false;
                        }
                    }
                    return true;
                } else {
                    if ($OT->start_date >= $data['start_date'] && $OT->start_date <= $data['end_date']) {
                        return false;
                    }
                    if ($data['start_date'] >= $OT->start_date && $data['start_date'] <= $OT->end_date) {
                        return false;
                    }
                }
            }

            if ($data['display_with_type'] == 2) {
                if ($OT->display_with_type == 1) {
                    $diff = array_diff($data['display_with_data'], json_decode($OT->hidden_with_users));
                    foreach ($diff as $user) {
                        if (in_array($user, json_decode($OT->hidden_with_users)) == false) {
                            if ($OT->start_date >= $data['start_date'] && $OT->start_date <= $data['end_date']) {
                                return false;
                            }
                            if ($data['start_date'] >= $OT->start_date && $data['start_date'] <= $OT->end_date) {
                                return false;
                            }
                        }
                    }
                }
                if ($OT->display_with_type == 2) {
                    $same = array_intersect($data['display_with_data'], json_decode($OT->display_with_data));
                    if (count($same) != 0) {
                        return false;
                    } else {
                        foreach ($data['display_with_data'] as $user) {
                            if (in_array($user, json_decode($OT->hidden_with_users))) {
                                if ($OT->start_date >= $data['start_date'] && $OT->start_date <= $data['end_date']) {
                                    return false;
                                }
                                if ($data['start_date'] >= $OT->start_date && $data['start_date'] <= $OT->end_date) {
                                    return false;
                                }
                            }
                        }
                    }
                }
            }
        }
        return true;

    }

}
