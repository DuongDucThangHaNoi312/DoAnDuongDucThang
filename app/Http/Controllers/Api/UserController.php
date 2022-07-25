<?php

namespace App\Http\Controllers\Api;

use App\User;
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
    public function index()
    {
        $users = User::get();
        return  response()->json([
            'data'=> $users,
            'status' => 200,
            'message' => $this->success,
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
        $user = User::create( $request->all());
        return  response()->json([
            'data'=> $user,
            'message' => $this->success,
            'status' => 200,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);
        if (is_null($user)) {
            return  response()->json([
                'message' => $this->msgNoData
            ]);
        }
        
        return  response()->json([
            'data'=> $user,
            'status' => 200,
            'message' => $this->success,
        ]);
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
        $user = User::find($id);
        if (is_null($user)) {
            return  response()->json([
                'message' => $this->msgNoData
            ]);
        }
        
        $user->update($request->all());
        return  response()->json([
            'status' => 200,
            'message' => $this->success,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        if (is_null($user)) {
            return  response()->json([
                'message' => $this->msgNoData
            ]);
        }

        $user->delete();
        return  response()->json([
            'status' => 200,
            'message' => $this->success,
        ]);
    }
}
