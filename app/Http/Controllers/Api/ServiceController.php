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
    public function index()
    {
        $services = Service::get();
        return  response()->json([
            'data'=> $services,
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
        $service = Service::create( $request->all());
        return  response()->json([
            'data'=> $service,
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
        $service = Service::find($id);
        if (is_null($service)) {
            return  response()->json([
                'message' => $this->msgNoData
            ]);
        }
        
        return  response()->json([
            'data'=> $service,
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
        $service = Service::find($id);
        if (is_null($service)) {
            return  response()->json([
                'message' => $this->msgNoData
            ]);
        }
        
        $service->update($request->all());
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
        $service = Service::find($id);
        if (is_null($service)) {
            return  response()->json([
                'message' => $this->msgNoData
            ]);
        }

        $service->delete();
        return  response()->json([
            'status' => 200,
            'message' => $this->success,
        ]);
    }
}
