<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    protected $msgNoData = "Không tìm thấy dữ liệu";
    protected $success = "Thành công";
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = User::query();
        if ($request->id) {
            $query->where('id', $request->id);
        }
        $data = $query->get();

        if (count($data) < 1 ) {
            return  response()->json([
                'status'  => 404,
                'message' => $this->msgNoData,
                'data'    => [],
            ]);
        }
        
        return  response()->json([
            'status' => 200,
            'message' => $this->success,
            'data'=> $data,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $data = $request->all();
        $user = User::where('fullname', $data['fullname'])
            // ->where('password', bcrypt($data['password']))
            ->first();

        if (is_null($user)) {
             return  response()->json([
                'status' => 404,
                'message' => $this->msgNoData,
                'data'=> [],
            ]);
        }

        return  response()->json([
            'status' => 200,
            'message' => $this->success,
            'data'=> $user,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        if (isset($data['id'])) {
            $user = User::find($data['id']);
            if (is_null($user)) {
                return  response()->json([
                    'status'  => 404,
                    'message' => $this->msgNoData,
                    'data'    => [],
                ]);
            }
            $user = $user->update($data);
            $user = User::find($data['id']);
        } else {
            $user = User::create($data);
        }

        return  response()->json([
            'message' => $this->success,
            'status' => 200,
            'data'=> $user,
        ]);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $data = $request->all();
        $user = User::find($id);
        if (is_null($user)) {
            return  response()->json([
                'status'  => 404,
                'message' => $this->msgNoData,
                'data'    => [],
            ]);
        }
        
        $user->update($data);
        return  response()->json([
            'status'  => 200,
            'message' => $this->success,
            'data'    => $user,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $data = $request->all();
        
        $user = User::find($data['id']);
        if (is_null($user)) {
            return  response()->json([
                'status' => 404,
                'message' => $this->msgNoData,
                'data'=> [],
            ]);
        }

        $user->delete();
        return  response()->json([
            'status' => 200,
            'message' => $this->success,
            'data'=> [],
        ]);
    }

    public function getUserSameDep(Request $request)
    {
        $data = $request->all();
        $user = User::where('department_id', $data['department_id'])->get();

        if (is_null($user)) {
             return  response()->json([
                'status' => 404,
                'message' => $this->msgNoData,
                'data'=> [],
            ]);
        }

        return  response()->json([
            'status' => 200,
            'message' => $this->success,
            'data'=> $user,
        ]);
    }
}
