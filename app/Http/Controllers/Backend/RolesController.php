<?php

namespace App\Http\Controllers\Backend;

use DB;
use App\Role;
use App\Permission;
use App\Commons\Pemission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $roles = Role::orderBy('id','ASC')->paginate(5);
        return view('backend.roles.index',compact('roles'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
       // \App\Commons\Pemission::sync();
        Pemission::sync();
        $pGroups = Permission::whereNotNull('module')->groupBy('module')->select('module')->get()->toArray();
        $pGroups = array_column($pGroups, 'module', 'module');
        foreach ($pGroups as $key => $value) {
            $tmp = Permission::where('module', $key)->orderBy('action')->select('id', 'display_name', 'action')->get()->toArray();
            $pGroups[$key] = array_column($tmp, 'id', 'action');
        }


        return view('backend.roles.create',compact('pGroups'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = \Validator::make($data = $request->all(), Role::rules());
        $validator->setAttributeNames(trans('roles'));
        if ($validator->fails()) return back()->withErrors($validator)->withInput();

        $role = Role::create($data);
        foreach ($request->input('permissions') as $permission) {
            $role->attachPermission($permission);
        }

        Session::flash('message', trans('system.success'));
        Session::flash('alert-class', 'success');

        return redirect()->route('admin.roles.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $id = intval($id);
        $role = Role::find($id);

        if (is_null($role)) {
             Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return back();
        }

        $permissions = array_column($role->permissions()->select("id")->get()->toArray(), 'id', 'id');

        $pGroups = Permission::whereNotNull('module')->groupBy('module')->select('module')->get()->toArray();
        $pGroups = array_column($pGroups, 'module', 'module');
        foreach ($pGroups as $key => $value) {
            $tmp = Permission::where('module', $key)->orderBy('action')->select('id', 'display_name', 'action')->get()->toArray();
            $pGroups[$key] = array_column($tmp, 'id', 'action');
        }

        return view('backend.roles.show',compact('role','pGroups', 'permissions'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $id = intval($id);
        $role = Role::where('id', $id)->first();
        if (is_null($role)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return back();
        }
        // \App\Commons\Pemission::sync();
        Pemission::sync();
        $permissions = array_column($role->permissions()->select("id")->get()->toArray(), 'id', 'id');
        $pGroups = Permission::whereNotNull('module')->groupBy('module')->select('module')->get()->toArray();
        $pGroups = array_column($pGroups, 'module', 'module');
        foreach ($pGroups as $key => $value) {
            $tmp = Permission::where('module', $key)->orderBy('action')->select('id', 'display_name', 'action')->get()->toArray();
            $pGroups[$key] = array_column($tmp, 'id', 'action');
        }

        return view('backend.roles.edit',compact('role','pGroups', 'permissions'));
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
        $id = intval($id);
        $role = Role::where('id', $id)->first();

        if (is_null($role)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return back();
        }

        $validator = \Validator::make($data = $request->all(), Role::rules($id));
        $validator->setAttributeNames(trans('roles'));
        if ($validator->fails()) return back()->withErrors($validator)->withInput();

        $role->update($data);
        DB::table("permission_role")->where("role_id", $id)->delete();
        foreach ($request->input('permissions') as $permission) {
            $role->attachPermission($permission);
        }

        Session::flash('message', trans('system.success'));
        Session::flash('alert-class', 'success');

        return redirect()->route('admin.roles.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $id = intval($id);
        $role = Role::where('id', $id)->where('name', '<>', 'System')->first();
        if (is_null($role)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return back();
        }
        if ($role->users()->count()) {
            Session::flash('message', "Đã tồn tại user với vai trò này.");
            Session::flash('alert-class', 'danger');
            return back();
        }

        $role->permissions()->detach();
        $role->delete();

        Session::flash('message', trans('system.success'));
        Session::flash('alert-class', 'success');

        return redirect()->route('admin.roles.index');
    }
}