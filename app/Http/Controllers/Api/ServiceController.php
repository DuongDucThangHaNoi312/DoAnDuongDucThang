<?php

namespace App\Http\Controllers\Api;

use App\Models\Service;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ServiceController extends Controller
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
        $query = Service::query();
        if ($request->id) {
            $query->where('id', $request->id);
        }
        $data = $query->get();

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
        $data = $data['data']['0'];

        $service = Service::create($data);
        return  response()->json([
            'message' => $this->success,
            'status' => 200,
            'data'=> $service,
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
        $service = Service::find($id);
        if (is_null($service)) {
            return  response()->json([
                'status'  => 404,
                'message' => $this->msgNoData,
                'data'    => [],
            ]);
        }
        
        $service->update($data);
        return  response()->json([
            'status'  => 200,
            'message' => $this->success,
            'data'    => $service,
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
        $data = $data['data']['0'];
        
        $service = Service::find($data['id']);
        if (is_null($service)) {
            return  response()->json([
                'status' => 404,
                'message' => $this->msgNoData,
                'data'=> [],
            ]);
        }

        $service->delete();
        return  response()->json([
            'status' => 200,
            'message' => $this->success,
            'data'=> [],
        ]);
    }
}
