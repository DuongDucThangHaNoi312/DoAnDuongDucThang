<?php

namespace App\Http\Controllers\Backend;

use App\User;
use App\Config;
use App\Container;
use App\StaffDayOff;
use App\Defines\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class HomeController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function index(Request $request)
    {
        return view('backend.pages.home');
    }

    public function getLogin()
    {
        if(\Auth::guard('admin')->check()) return redirect()->route('admin.home');
        return view('backend.pages.login');
    }

    public function postLogin(Request $request)
    {
        $request->merge(['remember' => $request->input('remember', 0)]);
        // $rules = [
        //     'code'     => 'required|max:50',
        //     'password'  => 'required|min:6|max:25',
        //     //'g-recaptcha-response' => 'required|captcha',
        // ];

        // $this->validate($data = $request, $rules);
        $errors = new \Illuminate\Support\MessageBag;
        try {
            if (\Auth::guard('admin')->attempt(['code' => $request->input('code'), 'password' => $request->input('password'), 'activated' => 1], $data['remember'])) {
                \Auth::guard('admin')->user()->last_login = date('Y-m-d H:i:s');
                \Auth::guard('admin')->user()->save();
                if (\Auth::guard('admin')->user()->hasRole(['system', 'administrator'])) {
                    Session::put('is_admin', 1);
                }
                // if(\Auth::guard('customer')->check()) \Auth::guard('customer')->logout();
                if (Session::get('loginRedirect_admin', '') == '') {
                    return redirect()->route('admin.home');
                }
                if ($request->input('password') == '123@123') {
                    Session::flash('message', 'Đổi mật khẩu mặc định để sử dụng hệ thống');
                    Session::flash('alert-class', 'warning');
                    return redirect()->route('admin.change-password');
                }
                return redirect()->intended(Session::get('loginRedirect_admin', route('admin.home')));
            }
            $errors->add('invalid', "Invalid code/password.");
        } catch (\Exception $e) {
            $errors->add('error', $e->getMessage());
        }
        return back()->withErrors($errors)->withInput();
    }

    public function getLogout()
    {
        if(\Auth::guard('admin')->check()) \Auth::guard('admin')->logout();
        Session::forget('is_admin');
        return redirect()->route('admin.home');
    }

    public function get403()
    {
        return view('backend.pages.403');
    }

    public function get404()
    {
        return view('backend.pages.404');
    }

    public function changePassword()
    {
        return view('backend.pages.change_password');
    }

    public function account()
    {
        $user = \Auth::guard('admin')->user();
        return view('backend.pages.account', compact('user'));
    }

    public function postChangePassword(Request $request)
    {
        $validator = \Validator::make($request->all(), array(
            'current_password'  => 'required|min:6|max:50',
            'new_password'      => 'required|min:6|max:50',
            're_password'       => 'same:new_password'
            ));

        $validator->setAttributeNames(trans('users'));
        if($validator->fails()) return back()->withErrors($validator)->withInput();

        $user = \Auth::guard('admin')->user();
        if ( !\Hash::check($request->input('current_password'), $user->password)) {
            $errors = new \Illuminate\Support\MessageBag;
            $errors->add('editError', 'Mật khẩu hiện tại không đúng');
            return back()->withErrors($errors);
        }

        $user->password = \Hash::make($request->input('new_password'));
        $user->save();
        return redirect()->route('admin.home');
    }

    public function postAccount(Request $request)
    {
        $request->merge(['menu_is_collapse' => $request->input('menu_is_collapse', 0)]);
        $validator = \Validator::make($request->all(), array(
            'fullname'          => 'required|min:6|max:30',
            'menu_is_collapse'  => 'required|in:0,1',
            ));

        $validator->setAttributeNames(trans('users'));
        if($validator->fails()) return back()->withErrors($validator)->withInput();

        $user = \Auth::guard('admin')->user();
        $user->fullname         = $request->input('fullname');
        $user->menu_is_collapse = $request->input('menu_is_collapse');
        $user->save();

        \Session::flash('message', trans('system.success'));
        \Session::flash('alert-class', 'success');

        return redirect()->route('admin.home');
    }

    public function flushCache()
    {
        echo 123; exit;
        \Redis::flushall();
        dd('done');
    }


    public function uploadImage(Request $request)
    {
        $response = [ 'message' => trans('system.have_an_error') ];
        $statusCode = 200;
        if($request->ajax()) {
            try {
                $ext = strtolower($request->file('image')->extension());
                if (!in_array($ext, ['png', 'jpg', 'jpeg'])) {
                    $statusCode = 400;
                    throw new \Exception("Chỉ hỗ trợ file ảnh với đuôi mở rộng là: png, jpg, jpeg", 1);
                }
                $image = $request->image;
                $rawName = $image->getClientOriginalName();
                $rawName = substr($rawName, 0, strlen($rawName) - (strlen($ext) + 1));
                $name = substr(str_slug($rawName), 0, 20) . '_' . time(). '_' . str_random(6) . '.' . $ext;
                $path = config('upload.image') .  date("Ymd") . '/';
                \File::makeDirectory($path, 0775, true, true);
                $image->move($path, $name);
                $response['message'] = trans('system.success');
                $response['url'] = asset($path . $name);
            } catch (\Exception $e) {
                if ($statusCode == 200) $statusCode = 500;
                $response['message'] = $e->getMessage();
            } finally {
                return response()->json($response, $statusCode);
            }
        } else {
            $statusCode = 405;
            return response()->json($response, $statusCode);
        }
    }

    public function updateConfig(Request $request)
    {
        $response = ['message' => trans('system.have_an_error')];
        $statusCode = 200;
        if($request->ajax()) {
            try {
                $ids = json_decode($request->input('ids'));
                $configs = [];
                $i = 1;
                foreach ($ids as $id) {
                    array_push($configs, [
                        'field'     => $id,
                        'position'  => $i++,
                        'user_id'   => $request->user()->id,
                        'source'    => \App\Define\Container::SOURCE_ADMIN,
                        'created_at'=> date("Y-m-d H:i:s"),
                        'updated_at'=> date("Y-m-d H:i:s"),
                        'status'    => 1,
                    ]);
                }
                Config::where('user_id', $request->user()->id)->where('source', \App\Define\Container::SOURCE_ADMIN)->delete();
                foreach ($configs as $cfg) {
                    Config::create($cfg);
                }
                $response['message'] = trans('system.success');
                Session::flash('message', trans('system.success'));
                Session::flash('alert-class', 'success');
            } catch (\Exception $e) {
                if ($statusCode == 200) $statusCode = 500;
                $response['message'] = $e->getMessage();
            } finally {
                return response()->json($response, $statusCode);
            }
        } else {
            $statusCode = 405;
            return response()->json($response, $statusCode);
        }
    }

    public function loginAs(Request $request)
    {
        $response = ['message' => trans('system.have_an_error')];
        $statusCode = 400;
        if($request->ajax()) {
            try {
                if (!Session::has('is_admin')) throw new \Exception($response['have_an_error'], 1);
                $user = User::find($request->user_id);
                if (is_null($user)) throw new \Exception($response['have_an_error'], 1);
                \Auth::guard('admin')->loginUsingId($user->id);
                $statusCode = 200;
                $response['message'] = trans('system.success');
            } catch (\Exception $e) {
                $response['message'] = $e->getMessage();
            } finally {
                return response()->json($response, $statusCode);
            }
        } else {
            $statusCode = 405;
            return response()->json($response, $statusCode);
        }
    }
}
