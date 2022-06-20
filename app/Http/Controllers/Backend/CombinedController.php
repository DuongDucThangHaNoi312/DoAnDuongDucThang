<?php

namespace App\Http\Controllers\Backend;

use App\Define\Constant;
use App\Helpers\GetOption;
use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Department;
use App\Models\DepartmentGroup;
use App\Models\DepartmentRelationship;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class CombinedController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $deptId = $user->department_id;
        $infoPermission = \App\PermissionUserObject::getMorePermissions();
        if ($infoPermission) {
            $deptPermission = $infoPermission['departments'];
            if ( !in_array($deptId, $deptPermission) ) {
                array_push($deptPermission, $deptId);
            }
            $groupIds = Department::getGroupFromMultiDepts($deptPermission);
            $groupIds = array_unique($groupIds);
            $departmentGroup = DepartmentGroup::whereIn('id', $groupIds)->get();
        } else {
            $departmentGroup = DepartmentGroup::get();
        }

        return view('backend.department.combined-index', compact('departmentGroup'));
    }

    public function create()
    {
        $deptHasGroup = [];
        $deptGroupRelation = DepartmentRelationship::get();
        if (count($deptGroupRelation) > 0) {
            foreach ($deptGroupRelation as $item) {
                $deptHasGroup[] = $item->department_id;
            }
        }
        $infoPermission = \App\PermissionUserObject::getMorePermissions();
        if ($infoPermission) {
            $deptPermission = GetOption::getArrDeptFromPermission($infoPermission);
            $departments = Department::with('company')->whereNotIn('id', $deptHasGroup)->whereIn('id', $deptPermission)->get();;
        } else $departments = Department::with('company')->whereNotIn('id', $deptHasGroup)->get();;
        $departmentGroup = [];
        if (count($departments) > 0) {
            foreach ($departments as $item) {
                $departmentGroup[$item->id] = $item->company->shortened_name. ' - ' .$item->name;
            }
        }

        return view('backend.department.combined-create', compact('departmentGroup'));
    }

    public function store(Request $request)
    {
        $request->merge(['status' => intval($request->input('status', 0))]);
        $request->merge(['only_manager' => intval($request->input('only_manager', 0))]);
        $data = $request->all();

        $validatorGroup = Validator::make($data, DepartmentGroup::rules());
        $validator = Validator::make($data, DepartmentRelationship::rules());
        $validator->setAttributeNames(trans('departments'));
        $validator->setAttributeNames(trans('departments'));
        if ($validatorGroup->fails()) {
            return back()->withErrors($validatorGroup)->withInput();
        }
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DepartmentGroup::create([
            'name' => $data['name'],
            'status' => $data['status'],
            'type' => $data['type'],
            'only_manager' => $data['only_manager']
        ]);
        $departmentGroup = DB::table('department_groups')
            ->latest('id')->first();
        foreach ($request->get('department_id') as $department) {
            DepartmentRelationship::create([
                'department_id' => $department,
                'group_id' => $departmentGroup->id
            ]);
        }
        Session::flash('message', trans('system.success'));
        Session::flash('alert-class', 'success');
        return redirect()->route('admin.combined.index');

    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $departmentGroup = DepartmentGroup::find($id);
        if (is_null($departmentGroup)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.combined.index');
        }
        $departmentRelationship = DepartmentRelationship::where('group_id', $id)->get();
        if (is_null($departmentRelationship)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.combined.index');
        }
        $departmentId = [];
        foreach ($departmentRelationship as $department) {
            array_push($departmentId, $department->department_id);
        }

        $deptHasGroup = [];
        $deptGroupRelation = DepartmentRelationship::where('group_id', '<>', $id)->get();
        if (count($deptGroupRelation) > 0) {
            foreach ($deptGroupRelation as $item) {
                $deptHasGroup[] = $item->department_id;
            }
        }
        $infoPermission = \App\PermissionUserObject::getMorePermissions();
        if ($infoPermission) {
            $deptPermission = GetOption::getArrDeptFromPermission($infoPermission);
            if ($departmentGroup->only_manager == 0) $deptPermission = array_merge($deptPermission, $departmentId);
            $deptPermission = array_unique($deptPermission);
            $departments = Department::with('company')->whereNotIn('id', $deptHasGroup)->whereIn('id', $deptPermission)->get();;
        } else $departments = Department::with('company')->whereNotIn('id', $deptHasGroup)->get();;
        $departmentOptions = [];
        if (count($departments) > 0) {
            foreach ($departments as $item) {
                $departmentOptions[$item->id] = $item->company->shortened_name. ' - ' .$item->name;
            }
        }

        return view('backend.department.combined-edit', compact('departmentGroup', 'departmentId', 'departmentOptions'));
    }

    public function update(Request $request, $id)
    {
        $request->merge(['status' => intval($request->input('status', 0))]);
        $request->merge(['only_manager' => intval($request->input('only_manager', 0))]);
        $data = $request->all();
        $validatorGroup = Validator::make($data, DepartmentGroup::rules());

        $validator = Validator::make($data, DepartmentRelationship::rules($id));
		$validatorGroup->setAttributeNames(trans('departments'));
        $validator->setAttributeNames(trans('departments'));
        if ($validatorGroup->fails()) {
            return back()->withErrors($validatorGroup)->withInput();
        }
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $departmentGroup = DepartmentGroup::find($id);
        $departmentGroup->update([
            'name' => $data['name'],
            'status' => $data['status'],
            'type' => $data['type'],
            'only_manager' => $data['only_manager']
        ]);
        $departmentRelationship = DepartmentRelationship::where('group_id', $id)->delete();
        foreach ($data['department_id'] as $department) {
            DepartmentRelationship::create([
                'department_id' => $department,
                'group_id' => $id
            ]);


            //bổ sung thêm vào nhân viên hđ
            $users = User::where('department_id', $department)->get();
            foreach ($users as $k => $user) {
                $user->dept_group_id = $id;
                $user->save();

                $contract = Contract::where('user_id', $user->id)->first();
                $contract->department_group_id = $id;
                $contract->save();
            }
            
        }
        
        Session::flash('message', trans('system.success'));
        Session::flash('alert-class', 'success');
        return redirect()->route('admin.combined.index');
    }

    public function destroy($id)
    {
        $departmentGroup = DepartmentGroup::find($id);
        if (is_null($departmentGroup) || $departmentGroup->status == 1) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.companies.index');
        }
        if (Contract::checkDeleteModule('department_group_id', $id)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return back();
        }
        $departmentGroup->delete();
        $departmentRelationship = DepartmentRelationship::where('group_id', $id)->delete();
        Session::flash('message', trans('system.success'));
        Session::flash('alert-class', 'success');
        return redirect()->route('admin.combined.index');
    }
}
