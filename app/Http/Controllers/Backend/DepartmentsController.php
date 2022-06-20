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
        $user = Auth::user();
        $deptId = $user->department_id;
        $infoPermission = \App\PermissionUserObject::getMorePermissions();
        if ($infoPermission) {
            if ($infoPermission['departments']) {
                $query .= " AND id IN(" . implode(',', array_unique($infoPermission['departments'])) . ")";
            } else $query .= " AND id = {$deptId}";
        }
        $departments = Department::whereRaw($query)->orderBy('updated_at', 'DESC')->get();
        if ($infoPermission['departments'] && !in_array($deptId, $infoPermission['departments'])) {
            $dept = Department::where('id', $deptId)->first();
            $departments->push($dept);
        }
        $departmentGroupsName = $departments->groupBy('name');
        return view('backend.department.index', compact('departmentGroupsName'));
    }

    function create()
    {
        $companiesPer = GetOption::getCompaniesForOption();
        return view('backend.department.create', compact('companiesPer'));
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

        $companyShortNames = Company::whereIn('id', $data['company_id'])->pluck('shortened_name', 'id')->toArray();
        if ($data['code']) {
            foreach ($data['company_id'] as $company) {
                $validator = Validator::make(
                    [
                        'code'  => $data['code'] . '_' . $companyShortNames[$company]
                    ],
                    [
                        'code'  => 'nullable|max:50|regex:/^[A-Za-z0-9_.-]+$/|unique:departments,code',
                    ]
                );
                $validator->setAttributeNames(trans('departments'));
                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }
            }
        }

        foreach ($data['company_id'] as $company) {
            Department::create([
                'name' => $data['name'],
                'name_es' => $data['name_es'],
                'telephone' => $data['telephone'],
                'company_id' => $company,
                'address' => $data['address'],
                'address_es' => $data['address_es'],
                'description' => $data['description'],
                'status' => $data['status'],
                'type' => $data['type'],
                'code'  => $data['code'] ? $data['code'] . '_' . $companyShortNames[$company] : "",
            ]);
        }

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
        $companiesPer = GetOption::getCompaniesForOption();
        return view('backend.department.edit', compact('department', 'companiesPer'));
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
        if (Contract::checkDeleteModule('department_id', $id)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.departments.index');
        }
        $department->delete();
        Session::flash('message', trans('system.success'));
        Session::flash('alert-class', 'success');
        return redirect()->route('admin.departments.index');
    }

    public function checkMultiCurrency(Request $request)
    {
        $response = ['message' => trans('system.have_an_error'), 'data' => ""];
        $statusCode = 400;
        if ($request->ajax()) {
            try {
                $deptId = $request->deptId;
                $dept = Department::where('status', 1)
                    ->where('id', $deptId)
                    ->first(['id', 'is_multi_currency']);
                if (is_null($dept)) throw new \Exception('Department is not found');
                $response['is_multi_currency'] = boolval($dept->is_multi_currency);
                $statusCode = 200;
            } catch (\Exception $e) {
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
