<?php

namespace App\Http\Controllers\Backend;

use App\Models\Service;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{
     public function index(Request $request)
    {
        $services = Service::get();
        return view('backend.services.index', compact('services'));
    }

    function create()
    {
        return view('backend.services.create');
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, Service::rules());
        $validator->setAttributeNames(trans('services'));
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        Service::create([
            'name' => $data['name'],
            'price' => $data['price'],
        ]);

        Session::flash('message', trans('system.success'));
        Session::flash('alert-class', 'success');
        return redirect()->route('admin.services.index');
    }

    public function show($id)
    {
        $service = Service::find($id);
        if (is_null($service)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.services.index');
        }
        return view('backend.services.show', compact('service'));
    }

    public function edit($id)
    {
        $service = Service::find(intval($id));
        if (is_null($service)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.services.index');
        }
        return view('backend.services.edit', compact('service'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->all();
        $service = Service::find(intval($id));
        if (is_null($service)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.services.index');
        }
        $service->update($data);
        Session::flash('message', trans('system.success'));
        Session::flash('alert-class', 'success');
        return redirect()->route('admin.services.index');
    }

    public function destroy($id)
    {
        $service = Service::find(intval($id));    
        if (is_null($service)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.services.index');
        }
        $service->delete();
        Session::flash('message', trans('system.success'));
        Session::flash('alert-class', 'success');
        return redirect()->route('admin.services.index');
    }
}
