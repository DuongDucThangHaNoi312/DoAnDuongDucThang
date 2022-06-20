<?php

namespace App\Http\Controllers\Backend;

use App\Models\Contract;
use App\Position;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class StaffPositionsController extends Controller
{
    public function index(Request $request)
    {
        $query = "1=1";
        $page_num = intval($request->input('page_num', \App\Define\Constant::PAGE_NUM));
        $code = $request->input('code');
        $name = $request->input('name');
        if ($code) $query .= " AND (code like '%" . $code . "%')";
        if ($name) $query .= " AND (name like '%" . $name . "%')";
        $positions = Position::whereRaw($query)->orderBy('updated_at', 'DESC')->paginate($page_num);
        return view('backend.staff-positions.index', compact('positions'));
    }
    public function create()
    {
        return view('backend.staff-positions.create');
    }

    public function store(Request $request)
    {
        $request->merge(['unique_in_dept' => intval($request->input('unique_in_dept', 0))]);
        $validator = \Validator::make($data = $request->all(), Position::rules());
        $validator->setAttributeNames(trans('staff_positions'));
        if ($validator->fails()) return back()->withErrors($validator)->withInput();
        $data['is_system']=0;
        Position::create($data);
        Session::flash('message', trans('system.success'));
        Session::flash('alert-class', 'success');
        return redirect()->route('admin.positions.index');
    }

    public function edit($id)
    {
        $positions = Position::find(intval($id));
        if ($positions->is_system==1){
            abort(401);
        }
        if (is_null($positions)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.staff-positions.index');
        }
        return view('backend.staff-positions.edit', compact('positions'));
    }

    public function update(Request $request, $id)
    {
        $positions = Position::find(intval($id));
        if (is_null($positions)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.positions.index');
        }
        $validator = \Validator::make($data = $request->only(['name', 'code','unique_in_dept']), Position::rules(intval($id)));
        $validator->setAttributeNames(trans('staff_positions'));
        if ($validator->fails()) return back()->withErrors($validator)->withInput();
        $positions->update($data);
        Session::flash('message', trans('system.success'));
        Session::flash('alert-class', 'success');
        return redirect()->route('admin.positions.index', compact('positions'));
    }

    public function destroy($id)
    {
        $positions = Position::find(intval($id));
        if ($positions->is_system==1){
            abort(401);
        }
        if (is_null($positions)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.positions.index');
        }
        if (Contract::checkDeleteModule('position_id', $id)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return back();
        }
        $positions->delete();

        Session::flash('message', trans('system.success'));
        Session::flash('alert-class', 'success');
        return redirect()->route('admin.positions.index');
    }
}
