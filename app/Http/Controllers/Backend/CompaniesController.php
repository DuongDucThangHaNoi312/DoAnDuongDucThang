<?php

namespace App\Http\Controllers\Backend;

use App\Models\Company;
use App\Models\Contract;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;


class CompaniesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = "1=1";
        $infoPermission = \App\PermissionUserObject::getMorePermissions();
        if ($infoPermission['companies']) $query .= " AND id IN(" . implode(',', array_unique($infoPermission['companies'])) . ")";
        $companies = Company::whereRaw($query)->orderBy('updated_at', 'DESC')->get();

        return view('backend.company.index', compact('companies'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.company.create');
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
        $validator = Validator::make($data, Company::rules());

        $validator->setAttributeNames(trans('companies'));
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        Company::create($data);
        Session::flash('message', trans('system.success'));
        Session::flash('alert-class', 'success');
        return redirect()->route('admin.companies.index');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $company = Company::find($id);
        if (is_null($company)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.companies.index');
        }
        return view('backend.company.show', compact('company'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $company = Company::find($id);
        if (is_null($company)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.companies.index');
        }
        return view('backend.company.edit', compact('company'));
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
        $request->merge(['status' => $request->input('status', 0)]);
        $data = $request->all();
        $company = Company::find(intval($id));
//        $department =Department::where('company_id', $id);
        if (is_null($company)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.companies.index');
        }
        $validator = Validator::make($data, Company::rules(intval($id)));
        $validator->setAttributeNames(trans('companies'));
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
//        if($company->status == 1) {
//            $department->where('status', 1)->update(['status' => 0]);
//        }
        $company->update($data);
        Department::where('company_id', $id)->update(['status' => $company->status]);
        Session::flash('message', trans('system.success'));
        Session::flash('alert-class', 'success');
        return redirect()->route('admin.companies.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $company = Company::find(intval($id));
        if (is_null($company) || $company->status == 1 || Department::where('company_id', $id)->where('status', 1)->count()) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.companies.index');
        }
        if (Contract::checkDeleteModule('company_id', $id)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return back();
        }
        $company->delete();
        Department::where('company_id', $id)->delete();
        Session::flash('message', trans('system.success'));
        Session::flash('alert-class', 'success');
        return redirect()->route('admin.companies.index');

    }
}
