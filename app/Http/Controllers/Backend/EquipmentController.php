<?php

namespace App\Http\Controllers\Backend;

use App\Models\Equipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class EquipmentController extends Controller
{
    public function __construct()
    {
        // $this->middleware(function($request, $next) {
        //     $user = auth()->guard('admin')->user();
        //     if (($user->admin == 1)) {
        //         return $next($request);

        //     }
        //     $msg = "Bạn không có quyền truy cập";
        //     return redirect()->route('admin.403')->with(['msg' => $msg]);
        // });
    }

    public function index(Request $request)
    {
        $typeEquipments = \App\Defines\Equipment::OptionEquipment();
        $equipments = Equipment::orderBy('updated_at', 'desc')->get();
        return view('backend.equipments.index', compact('equipments', 'typeEquipments'));
    }

    function create()
    {
        return view('backend.equipments.create');
    }

    public function store(Request $request)
    {

        $data = $request->all();
        $validator = Validator::make($data, Equipment::rules());
        $validator->setAttributeNames(trans('equipments'));
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        Equipment::create([
            'type' => $data['type'],
            'price' => intval($data['price']),
            'name' => $data['name'],
            'created_by' => Auth()->id(),
        ]);

        Session::flash('message', trans('system.success'));
        Session::flash('alert-class', 'success');
        return redirect()->route('admin.equipments.index');
    }

    public function show($id)
    {
        $typeEquipments = \App\Defines\Equipment::OptionEquipment();
        $equipment = Equipment::find($id);
        if (is_null($equipment)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.equipments.index');
        }
        return view('backend.equipments.show', compact('equipment', 'typeEquipments'));
    }

    public function edit($id)
    {
        $typeEquipments = \App\Defines\Equipment::OptionEquipment();
        $equipment = Equipment::find(intval($id));
        if (is_null($equipment)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.equipments.index');
        }
        return view('backend.equipments.edit', compact('equipment', 'typeEquipments'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->all();
        $equipment = Equipment::find(intval($id));
        if (is_null($equipment)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.equipments.index');
        }
        $validator = Validator::make($data, Equipment::rules(intval($id)));
        $validator->setAttributeNames(trans('equipments'));
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $equipment->update($data);
        Session::flash('message', trans('system.success'));
        Session::flash('alert-class', 'success');
        return redirect()->route('admin.equipments.index');
    }

    public function destroy($id)
    {
        $equipment = Equipment::find(intval($id));    
        if (is_null($equipment)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.equipments.index');
        }
        $equipment->delete();
        Session::flash('message', trans('system.success'));
        Session::flash('alert-class', 'success');
        return redirect()->route('admin.equipments.index');
    }
}
