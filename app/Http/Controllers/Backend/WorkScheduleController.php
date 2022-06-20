<?php

namespace App\Http\Controllers\Backend;

use App\Models\Company;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CategoryShift;
use App\Models\ShiftTime;
use App\Models\WorkSchedule;
use App\PermissionUserObject;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class WorkScheduleController extends Controller
{
    public function index(Request $request)
    {
        // $query = "1=1";
        // // $page_num = intval($request->input('page_num', \App\Define\Constant::PAGE_NUM));
        // $company_id = $request->input('company_id');
        // $department_id = $request->input('department_id');
        // if ($company_id) {
        //     $query .= " AND company_id = {$company_id}";
        // } else if ($department_id) {
        //     $query .= " AND department_id = {$department_id}";
        // }


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
        // }
        $query = PermissionUserObject::getQueryPermission(Auth::id());
        $workschedule = WorkSchedule::whereRaw($query)->orderBy('created_at', 'DESC')->get();
        $workschedule->load('company', 'department');
        return view('backend.work-schedule.index', compact('workschedule','department_group','companysOption'));
    }

    public function store(Request $request)
    {
        $reponse = [];
        $data = $request->all();
        $department = Department::find($data['department_id']);
        if ($department->type == \App\Define\Department::FUNCTIONAL_OFFICE) {
            $validator = \Validator::make($data, WorkSchedule::rules());
            $validator->setAttributeNames(trans('work_schedule'));
            $insert = [
                'company_id'        => $data['company_id'],
                'department_id'     => $data['department_id'],
                'from_morning'      => $data['from_morning'],
                'to_morning'        => $data['to_morning'],
                'from_afternoon'    => $data['from_afternoon'],
                'to_afternoon'      => $data['to_afternoon'],
                'from_sa_morning'   => $data['from_sa_morning'],
                'to_sa_morning'     => $data['to_sa_morning'],
                'from_sa_afternoon' => $data['from_sa_afternoon'],
                'to_sa_afternoon'   => $data['to_sa_afternoon'],
                'created_by'        => $request->user()->id,
                'type'              => $data['type'],
                'ot'                => $data['ot']
            ];
        } else if ($department->type == \App\Define\Department::DECLARATION_OFFICE) {
            $validator = \Validator::make($data, ShiftTime::rules());
            $validator->setAttributeNames(trans('work_schedule'));
            $insert = [
                'company_id'    => $data['company_id'],
                'department_id' => $data['department_id'],
                'category_shift_id' => $data['category_shift_id'],
                'time_in'  => $data['time_in'],
                'time_out' => $data['time_out'],
                'off_mid_shift'  => $data['off_mid_shift'],
                'start_mid_shift' => $data['start_mid_shift'],
                'limit_time_in'  => $data['limit_time_in'],
                'limit_time_out' => $data['limit_time_out'],
                'created_by' => $request->user()->id,

            ];
        }
        if ($validator->passes()) {
            if ($department->type == \App\Define\Department::FUNCTIONAL_OFFICE) {
                $check = WorkSchedule::where('company_id', $data['company_id'])->where('department_id', $data['department_id'])
                                    ->get();
                if (count($check) > 0) {
                    return \Response::json([
                        'status' => 'FAIL',
                        'message' => 'Thời gian làm việc đã tồn tại'
                    ]);
                }

                if (WorkSchedule::create($insert)) {
                    return \Response::json([
                        'status' => 'SUCCESS',
                        'message' => 'Tạo lịch làm việc thành công'
                    ]);
                }
            }

            if ($department->type == \App\Define\Department::DECLARATION_OFFICE) {
                if (ShiftTime::create($insert)) {
                    return \Response::json([
                        'status' => 'SUCCESS',
                        'message' => 'Tạo lịch làm việc thành công'
                    ]);
                }
            }
        }

        return \Response::json(['errors' => $validator->errors()]);
    }

    public function edit(Request $request, $id)
    {
        if ($request->input('type') == 'shift') {
            $data = ShiftTime::findOrFail($id);
        } else {
            $data = WorkSchedule::findOrFail($id);
        }
        $data['department_name'] = $data->department->name;
        
        return \Response::json([
            'status' => 'SUCCESS',
            'data' => $data
        ]);
    }

    public function destroy($id)
    {
        $workschedule = WorkSchedule::findOrFail($id)->delete();
        if ($workschedule) {
            Session::flash('message', trans('system.success'));
            Session::flash('alert-class', 'success');
        }
        
        return redirect()->route('admin.workschedule.index');
    }
    
    public function update(Request $request, $id)
    {
        $workSchedule = WorkSchedule::find($id);
        if (empty($workSchedule)) {
            return \Response::json([
                'status' => 'FAIL',
                'message' => trans('workschedule.error')
            ]);
        }
        $data = $request->all();

        $data['company_id'] = $workSchedule->company_id;
        $data['department_id'] = $workSchedule->department_id;
        $data['update_by'] = Auth::user()->id;
        if (is_null($data['type'])) $data['type'] = 2;

        $validator = \Validator::make($data, WorkSchedule::rules());
        $validator->setAttributeNames(trans('work_schedule'));
        if ($validator->passes()) {
            if ($workSchedule->update($data)) {
                return \Response::json([
                    'status' => 'SUCCESS',
                    'message' => trans('workschedule.edit_success')
                ]);
            } else {
                return \Response::json([
                    'status' => 'FAIL',
                    'message' => trans('workschedule.error')
                ]);
            }
        }

        return \Response::json(['errors' => $validator->errors()]);
    }

    public function setDepartmentOption(Request $request)
    {
        $response = ['message' => trans('system.have_an_error'), 'data' => ""];
        if ($request->ajax()) {
            $companyId = $request->input('companyId');
            if (empty($companyId)) return response()->json(['error' => true, 'message' => trans('system.no_item_selected')]);
            $departmentsOption = Company::find($companyId)->departmentOffice->pluck('name', 'id')->toArray();
            return response()->json($departmentsOption);
        } else {
            $statusCode = 405;
            return response()->json($response, $statusCode);
        }
    }

    public function checkDepartment(Request $request)
    {
        $response = ['message' => trans('system.have_an_error'), 'data' => ""];
        if ($request->ajax()) {
            $departmentId = $request->input('departmentId');
            $department = Department::find($departmentId);
            if (empty($department)) return response()->json(['error' => true, 'message' => trans('system.no_item_selected')]);

            return response()->json($department);
        } else {
            $statusCode = 405;
            return response()->json($response, $statusCode);
        }
    }
    

    public function listShift()
    {
        $query = PermissionUserObject::getQueryPermission(Auth::id(), 'workschedule.read');
        $workschedule = ShiftTime::whereRaw($query)->get();
        $workschedule->load('company', 'department', 'category');

        return view('backend.work-schedule.shift', compact('workschedule'));
    }

    public function update1(Request $request, $id)
    {
        $workSchedule = ShiftTime::find($id);
        if (empty($workSchedule)) {
            return \Response::json([
                'status' => 'FAIL',
                'message' => trans('workschedule.error')
            ]);
        }
        $data = $request->all();
        
        $data['company_id'] = $workSchedule->company_id;
        $data['department_id'] = $workSchedule->department_id;
        $data['update_by'] = Auth::user()->id;
        
        $validator = \Validator::make($data, ShiftTime::rules());
        $validator->setAttributeNames(trans('work_schedule'));
        if ($validator->passes()) {
            if ($workSchedule->update($data)) {
                return \Response::json([
                    'status' => 'SUCCESS',
                    'message' => trans('workschedule.edit_success')
                ]);
            } else {
                return \Response::json([
                    'status' => 'FAIL',
                    'message' => trans('workschedule.error')
                ]);
            }
        }

        return \Response::json(['errors' => $validator->errors()]);
    }

    public function destroy1($id)
    {
        $workschedule = ShiftTime::findOrFail($id)->delete();
        if ($workschedule) {
            Session::flash('message', trans('system.success'));
            Session::flash('alert-class', 'success');
        }

        return redirect()->route('admin.workschedules.list-shift');
    }
}
