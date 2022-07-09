<?php

namespace App\Http\Controllers\Backend;

use App\User;
use App\Models\Company;
use App\Models\Contract;
use App\Helpers\GetOption;
use App\Models\Department;
use Illuminate\Http\Request;
use App\PermissionUserObject;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class DepartmentsController extends Controller
{
    public function index(Request $request)
    {
        $query = "1=1";
        $departments = Department::whereRaw($query)->orderBy('updated_at', 'DESC')->get();
        $departmentGroupsName = $departments->groupBy('name');
        return view('backend.department.index', compact('departmentGroupsName'));
    }

    function create()
    {
        return view('backend.department.create');
    }

    public function store(Request $request)
    {

        $request->merge(['status' => intval($request->input('status', 0))]);
        $data = $request->all();
        $validator = Validator::make($data, Department::rules());
        $validator->setAttributeNames(trans('departments'));
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
       
        Department::create([
            'name' => $data['name'],
            'telephone' => $data['telephone'],
            'company_id' => $company,
            'description' => $data['description'],
            'status' => $data['status'],
            'code' => $data['code'],
            'is_ph' => intval($data['is_ph']),
        ]);

        Session::flash('message', trans('system.success'));
        Session::flash('alert-class', 'success');
        return redirect()->route('admin.departments.index');
    }

    public function show($id)
    {
        $department = Department::find($id);
        if (is_null($department)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.departments.index');
        }
        return view('backend.department.show', compact('department'));
    }

    public function edit($id)
    {
        $department = Department::find($id);
        if (is_null($department)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.departments.index');
        }
        return view('backend.department.edit', compact('department'));
    }

    public function update(Request $request, $id)
    {
        $request->merge(['status' => $request->input('status', 0)]);
        $data = $request->all();
        $department = Department::find(intval($id));
        if (is_null($department)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.departments.index');
        }
        $validator = Validator::make($data, Department::rules(intval($id)));
        $validator->setAttributeNames(trans('departments'));
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $department->update($data);
        Session::flash('message', trans('system.success'));
        Session::flash('alert-class', 'success');
        return redirect()->route('admin.departments.index');
    }

    public function destroy($id)
    {
        $department = Department::find(intval($id));    
        if (is_null($department)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.departments.index');
        }
        $department->delete();
        Session::flash('message', trans('system.success'));
        Session::flash('alert-class', 'success');
        return redirect()->route('admin.departments.index');
    }

}
