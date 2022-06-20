<?php

namespace App\Http\Controllers\Backend;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Adjustment;
use Illuminate\Support\Facades\Session;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Http\Requests\adjustmentRequest;
use Illuminate\Support\Facades\Validator;

class AdjustmentController extends Controller
{
    public function index()
    {
        $adjustment = Adjustment::orderBy('created_at', 'desc')->get();

        return view('backend.adjustments.index', compact('adjustment'));
    }
    
    public function create(Request $request)
    {
        return view('backend.adjustments.create');
        
    }

    public function show($id)
    {
        $adjustment = Adjustment::find($id);
        if (is_null($adjustment)) {
            Session::flash('message', trans('adjustments.error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.adjustments.index');
        }
        return view('backend.adjustments.show', compact('adjustment'));
    }

    public function store(Request $request)
    {
        $data = $request->all();

        if (is_null($data['action'])) $data['action'] = 0;
        $validator = Validator::make($data, Adjustment::rules());
        $validator->setAttributeNames(trans('adjustments'));
        if ($validator->fails())  return back()->withErrors($validator)->withInput();

        try {
            Adjustment::create($data);

            Session::flash('message', trans('system.success'));
            Session::flash('alert-class', 'success');

            return redirect()->route('admin.adjustments.index');
        } catch (Exception $e) {   
            Session::flash('message', trans('adjustments.error'));
            Session::flash('alert-class', 'danger');

            return redirect()->route('admin.adjustments.index');
        }
    }

    public function edit($id)
    {
        $adjustment = Adjustment::find($id);
        if (is_null($adjustment)) {
            Session::flash('message',  trans('adjustments.error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.adjustments.index');
        }

        return view('backend.adjustments.edit', compact('adjustment'));
    }

    public function update(Request $request, $id)
    {
        $requestData = $request->all();
        $adjustment = Adjustment::find($id);
        
        if (is_null($adjustment)) {
            Session::flash('message',  trans('adjustments.error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.adjustments.index');
        }

        if (is_null($requestData['action'])) $requestData['action'] = 0;

        $validator = Validator::make($requestData, Adjustment::rules(intval($id)));
        $validator->setAttributeNames(trans('adjustments'));
        if ($validator->fails())  return back()->withErrors($validator)->withInput();

        try {
            $adjustment->update($requestData);
            Session::flash('message', trans('system.success'));
            Session::flash('alert-class', 'success');

            return redirect()->route('admin.adjustments.index');
        } catch (Exception $e) { 
            Session::flash('message',  trans('adjustments.error'));
            Session::flash('alert-class', 'dange');

            return redirect()->route('admin.adjustments.index');
        }
    }

    public function destroy($id)
    {
        $adjustment = Adjustment::find($id);

        if (is_null($adjustment)) {
            Session::flash('message',  trans('adjustments.error'));
            Session::flash('alert-class', 'danger');

            return redirect()->route('admin.adjustments.index');
        }

        try {
            $adjustment->destroy($id);
            Session::flash('message', trans('system.success'));
            Session::flash('alert-class', 'success');
            return redirect()->route('admin.adjustments.index');
        } catch (Exception $e) {    
            Session::flash('message',  trans('adjustments.error'));
            Session::flash('alert-class', 'dange');

            return redirect()->route('admin.adjustments.index');
        }
    }
}
