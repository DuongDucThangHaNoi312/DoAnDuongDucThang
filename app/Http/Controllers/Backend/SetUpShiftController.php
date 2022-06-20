<?php

namespace App\Http\Controllers\Backend;

use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CategoryShift;
use App\Models\Shift;
use Illuminate\Support\Facades\Session;

class SetUpShiftController extends Controller
{
    public function index()
    {
        // dd(Shift::getShiftEveryDay(2021, 6, 199));
        $items = CategoryShift::orderBy('id', 'DESC')->get();

        return view('backend.setup-shift.index', compact('items'));
    }

    public function create(Request $request)
    {
        return view('backend.setup-shift.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|max:255',
            'type' => 'required',
            'shortened_name' => 'required',
            'color' => 'required',
        ]);
        
        if ($validator->fails()) {
            return redirect()->route('admin.setupshifts.create')
                        ->withErrors($validator)
                        ->withInput();
        }
        if (CategoryShift::create($request->all())) {
            Session::flash('message', trans('system.success'));
            Session::flash('alert-class', 'success');
            return redirect()->route('admin.setupshifts.index');
        }

        Session::flash('message', trans('system.have_an_error'));
        Session::flash('alert-class', 'danger');
        return redirect()->route('admin.setupshifts.index');
    }

    public function edit(Request $request, $id)
    {
        $item = CategoryShift::find($id);
        if (empty($item)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.setupshifts.index');
        }

        return view('backend.setup-shift.edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->all();
        if (is_null($data['status'])) $data['status'] = 2;
        $item = CategoryShift::find($id);
        if (empty($item)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.setupshifts.index');
        }

        $validator = Validator::make($data, [
            'title' => 'required|max:255',
            'type' => 'required',
        ]);
        
        if ($validator->fails()) {
            return redirect()->route('admin.setupshifts.edit', $id)
                        ->withErrors($validator)
                        ->withInput();
        }

        if ($item->update($data)) {
            Session::flash('message', trans('system.success'));
            Session::flash('alert-class', 'success');
            return redirect()->route('admin.setupshifts.index');
        }

        Session::flash('message', trans('system.have_an_error'));
        Session::flash('alert-class', 'danger');
        return redirect()->route('admin.setupshifts.index');
    }
}
