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
    public function index(Request $request)
    {
        $query = Equipment::query();
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
        $data = $data['data'];
        $equipment = Equipment::create($data);
        return  response()->json([
            'message' => $this->success,
            'status' => 200,
            'data'=> $equipment,
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
        $data = $data['data'];
        $id   = ($data['id']);
              
        $equipment = Equipment::find($id);
        if (is_null($equipment)) {
            return  response()->json([
                'status'  => 404,
                'message' => $this->msgNoData,
                'data'    => [],
            ]);
        }
        
        $equipment->update($data);
        return  response()->json([
            'status'  => 200,
            'message' => $this->success,
            'data'    => $equipment,
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
        $data = $request->data;
        $equipment = Equipment::find($data['id']);
        if (is_null($equipment)) {
            return  response()->json([
                'status' => 404,
                'message' => $this->msgNoData,
                'data'=> [],
            ]);
        }

        $equipment->delete();
        return  response()->json([
            'status' => 200,
            'message' => $this->success,
            'data'=> [],
        ]);
    }
}
