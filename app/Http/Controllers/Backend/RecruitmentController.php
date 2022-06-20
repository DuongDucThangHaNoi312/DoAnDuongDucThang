<?php

namespace App\Http\Controllers\Backend;

use App\Models\Company;
use App\Models\Department;
use App\Models\Recruitment;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;



class RecruitmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = "1=1";
        $name = $request->input('name');
        $company = $request->input('company_id');
        $title = $request->input('title_id');
        if ($name) $query .= " AND name like '%" . $name . "%'";
        if ($company) $query .= " AND company_id = '{$company}'";
        if ($title) $query .= " AND title_id = '{$title}'";
        $recruitment = Recruitment::whereRaw($query)->paginate(20);
        return view('backend.recruitment.index', compact('recruitment'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.recruitment.create');

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data=$request->all();
        $validator = Validator::make($request->all(), Recruitment::rules());
        $validator->setAttributeNames(trans('recruitment'));
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        if($request->hasFile('file_cv')){
            $file = $request->file('file_cv');
            $filename=$file->getClientOriginalName('file_cv');
            $file->move('storage',$filename);
            $request->merge(['file_cv'=>$filename]);
            $data['file_cv']=$filename;
        }
        $data['dob'] = Carbon::createFromFormat('d/m/Y', $request->get('dob'));
        Recruitment::create($data);
        Session::flash('message', trans('system.success'));
        Session::flash('alert-class', 'success');
        return redirect()->route('admin.recruitment.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $recruitment = Recruitment::find(intval($id));
        if (is_null($recruitment)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.recruitment.index');
        }
        return view('backend.recruitment.show', compact('recruitment'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $recruitment = Recruitment::find(intval($id));
        if (is_null($recruitment)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.recruitment.index');
        }
        $departmentOption = Department::where('company_id', $recruitment->company_id)->pluck('name', 'id')->toArray();

        return view('backend.recruitment.edit', compact('recruitment','departmentOption'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->all();
        $validator = Validator::make( $request->all(), Recruitment::rules(intval($id)));
        $validator->setAttributeNames(trans('recruitment'));
        if ($validator->fails()){
            return back()->withErrors($validator)->withInput();
        }
        $recruitment = Recruitment::find(intval($id));
        if (is_null($recruitment)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.staffs.index');
        }
        if($request->hasFile('file_cv')){
            $file = $request->file('file_cv');
            $filename=$file->getClientOriginalName('file_cv');
            $file->move('storage',$filename);
            $data['file_cv'] = $filename;
        }
        $data['dob'] = Carbon::createFromFormat('d/m/Y', $request->get('dob'));
        $recruitment->update($data);
        Session::flash('message', trans('system.success'));
        Session::flash('alert-class', 'success');
        return redirect()->route('admin.recruitment.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $recruitment = Recruitment::find(intval($id));
        if (is_null($recruitment)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.recruitment.index');
        }
        $recruitment->delete();
        Session::flash('message', trans('system.success'));
        Session::flash('alert-class', 'success');
        return redirect()->route('admin.recruitment.index');
    }
}
