<?php

namespace App\Http\Controllers\Backend;

use App\Models\Contract;
use App\Qualification;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class StaffTitlesController extends Controller
{
    public function index(Request $request)
    {
        $query = "1=1";
        $page_num = intval($request->input('page_num', \App\Define\Constant::PAGE_NUM));
        $code = $request->input('code');
        $fullname = $request->input('name');
        if ($code) $query .= " AND (code like '%" . $code . "%')";
        if ($fullname) $query .= " AND (name like '%" . $fullname . "%')";
        $qualification = Qualification::whereRaw($query)->orderBy('updated_at', 'DESC')->get();
        return view('backend.staff-titles.index', compact('qualification'));
    }

    public function create()
    {
        return view('backend.staff-titles.create');
    }

    public function store(Request $request)
    {

        $validator = \Validator::make($data = $request->all(), Qualification::rules());
        $validator->setAttributeNames(trans('staff_titles'));
        if ($validator->fails()) return back()->withErrors($validator)->withInput();
        Qualification::create($data);
        Session::flash('message', trans('system.success'));
        Session::flash('alert-class', 'success');
        return redirect()->route('admin.titles.index');
    }

    public function edit($id)
    {
        $titles = Qualification::find(intval($id));
        if (is_null($titles)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.titles.index');
        }
        return view('backend.staff-titles.edit', compact('titles'));
    }

    public function update(Request $request, $id)
    {
        $titles = Qualification::find(intval($id));
        if (is_null($titles)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.titles.index');
        }
        $validator = \Validator::make($data = $request->all(), Qualification::rules(intval($id)));
        $validator->setAttributeNames(trans('staff_titles'));
        if ($validator->fails()) return back()->withErrors($validator)->withInput();
        $titles->update($data);
        Session::flash('message', trans('system.success'));
        Session::flash('alert-class', 'success');
        return redirect()->route('admin.titles.index');
    }

    public function destroy($id)
    {
        $title = Qualification::find(intval($id));
        if (is_null($title) || $title->is_system) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.titles.index');
        }
        if (Contract::checkDeleteModule('qualification_id', $id)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return back();
        }
        $title->delete();
        Session::flash('message', trans('system.success'));
        Session::flash('alert-class', 'success');
        return redirect()->route('admin.titles.index');
    }
}
