<?php

namespace App\Http\Controllers\Backend;

use DB;
use Hash;
use App\User;
use App\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        $users = User::where('activated', 1)->orderBy('id','DESC')->paginate(\App\Define\Constant::PAGE_NUM_20);
        return view('backend.users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::select('display_name','id')->get();
        return view('backend.users.create',compact('roles'));
    }

    public function store(Request $request)
    {
        $request->merge(['activated' => $request->input('status', 0)]);

        $validator = \Validator::make($data = $request->all(), User::rules());
        $validator->setAttributeNames(trans('users'));
        if ($validator->fails()) return back()->withErrors($validator)->withInput();

        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);
        foreach ($request->input('roles') as $role) {
            $user->attachRole($role);
        }

        Session::flash('message', trans('system.success'));
        Session::flash('alert-class', 'success');

        return redirect()->route('admin.users.index');
    }

    public function show($id)
    {
        $user = User::find($id);
        return view('backend.users.show',compact('user'));
    }

    public function edit($id)
    {
        $id = intval($id);
        $user = User::where('id', $id)->where('email', '<>', 'system@' . env('APP_NAME', 'bctech.vn'))->first();
        if (is_null($user)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return back();
        }

        $uRoles = $user->roles->pluck('id','id')->toArray();

        $roles = Role::select('display_name','id')->get();

        return view('backend.users.edit',compact('user','roles','uRoles'));
    }

    public function update(Request $request, $id)
    {
        $id = intval($id);
        $user = User::where('id', $id)->where('email', '<>', 'system@' . env('APP_NAME', 'bctech.vn'))->first();
        if (is_null($user)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return back();
        }

        $request->merge(['activated' => $request->input('status', 0)]);

        $validator = \Validator::make($data = $request->all(), User::rules($id));
        $validator->setAttributeNames(trans('users'));
        if ($validator->fails()) return back()->withErrors($validator)->withInput();

        $user->update($data);
        DB::table('role_user')->where('user_id', $id)->delete();

        foreach ($request->input('roles') as $role) {
            $user->attachRole($role);
        }

        Session::flash('message', trans('system.success'));
        Session::flash('alert-class', 'success');

        return redirect()->route('admin.users.index');
    }

    public function destroy(Request $request, $id)
    {
        $user = User::find(intval($id));
        if (is_null($user) || $user->id == \Auth::guard('admin')->user()->id) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return back();
        }
        $user->deleted_by = $request->user()->id;
        $user->save();
        $user->delete();
        Session::flash('message', trans('system.success'));
        Session::flash('alert-class', 'success');

        return redirect()->route('admin.users.index');
    }

    public function changePassword(Request $request, $id)
    {
        $id = intval($id);
        $user = User::where('id', $id)->first();
        if (is_null($user)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.users.index');
        }

        return view('backend.users.change-password', compact('user'));
    }

    public function postChangePassword(Request $request, $id)
    {
        $id = intval($id);
        $validator = \Validator::make($request->all(), array(
            'new_password'      => 'required|min:6|max:30',
            're_password'       => 'same:new_password',
            ));

        $validator->setAttributeNames(trans('users'));
        if ($validator->fails()) return back()->withErrors($validator)->withInput();

        $user = User::where('id', $id)->first();
        if (is_null($user)) {
            $errors = new \Illuminate\Support\MessageBag;
            $errors->add('editError', trans('system.have_an_error'));
            return back()->withErrors($errors)->withInput();
        }

        $user->password = \Hash::make($request->input('new_password'));
        $user->save();

        Session::flash('message', trans('system.success'));
        Session::flash('alert-class', 'success');
        return redirect()->route('admin.users.index');
    }
}
