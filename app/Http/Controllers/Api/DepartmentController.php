<?php

namespace App\Http\Controllers\Api;

use GuzzleHttp\Client;
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
    public function index(Request $request)
    {
        $query = Department::query();
        if ($request->id) {
            $query->where('id', $request->id);
        }
        $data = $query->get();

        if (count($data) < 1 ) {
            return  response()->json([
                'status'  => 200,
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        if (isset($data['id'])) {
            $department = Department::find($data['id']);
            if (is_null($department)) {
                return  response()->json([
                    'status'  => 200,
                    'message' => $this->msgNoData,
                    'data'    => [],
                ]);
            }
            $department = $department->update($data);
            $department = Department::find($data['id']);
        } else {
            $department = Department::create($data);
        }

        return  response()->json([
            'message' => $this->success,
            'status' => 200,
            'data'=> $department,
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
        $data = $data['data']['0'];
        $id   = ($data['id']);
              
        $department = Department::find($id);
        if (is_null($department)) {
            return  response()->json([
                'status'  => 200,
                'message' => $this->msgNoData,
                'data'    => [],
            ]);
        }
        
        $department->update($data);
        return  response()->json([
            'status'  => 200,
            'message' => $this->success,
            'data'    => $department,
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
        
        $department = Department::find($data['id']);
        if (is_null($department)) {
            return  response()->json([
                'status' => 200,
                'message' => $this->msgNoData,
                'data'=> [],
            ]);
        }

        $department->delete();
        return  response()->json([
            'status' => 200,
            'message' => $this->success,
            'data'=> [],
        ]);
    }
}
