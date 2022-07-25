<?php

namespace App\Http\Controllers\Api;

use App\Models\Equipment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EquipmentController extends Controller
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
        $equipments = Equipment::get();
        return  response()->json([
            'data'=> $equipments,
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
        $equipment = Equipment::create( $request->all());
        return  response()->json([
            'data'=> $equipment,
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
        $equipment = Equipment::find($id);
        if (is_null($equipment)) {
            return  response()->json([
                'message' => $this->msgNoData
            ]);
        }
        
        return  response()->json([
            'data'=> $equipment,
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
        $equipment = Equipment::find($id);
        if (is_null($equipment)) {
            return  response()->json([
                'message' => $this->msgNoData
            ]);
        }
        
        $equipment->update($request->all());
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
        $equipment = Equipment::find($id);
        if (is_null($equipment)) {
            return  response()->json([
                'message' => $this->msgNoData
            ]);
        }

        $equipment->delete();
        return  response()->json([
            'status' => 200,
            'message' => $this->success,
        ]);
    }
}
