<?php

namespace App\Http\Controllers\Api;

use App\Models\Department;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DepartmentController extends Controller
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
        $departments = Department::get();
        return  response()->json([
            'data'=> $departments,
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
        $departments = Department::create( $request->all());
        return  response()->json([
            'data'=> $departments,
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
        $departments = Department::find($id);
        if (is_null($departments)) {
            return  response()->json([
                'message' => $this->msgNoData
            ]);
        }
        
        return  response()->json([
            'data'=> $departments,
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
        $department = Department::find($id);
        if (is_null($department)) {
            return  response()->json([
                'message' => $this->msgNoData
            ]);
        }
        
        $department->update($request->all());
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
        $department = Department::find($id);
        if (is_null($department)) {
            return  response()->json([
                'message' => $this->msgNoData
            ]);
        }

        $department->delete();
        return  response()->json([
            'status' => 200,
            'message' => $this->success,
        ]);
    }
}
