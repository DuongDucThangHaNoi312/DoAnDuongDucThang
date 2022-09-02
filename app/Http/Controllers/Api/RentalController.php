<?php

namespace App\Http\Controllers\Api;

use App\Models\Rental;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RentalController extends Controller
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
        $dateCurrent = date("Y-m-d H:i:s");
        $data = Rental::with('rentalServices', 'rentalEquipments')
            ->where('rental_start', '<=', $dateCurrent)
            ->where('renral_end', '>=', $dateCurrent)
            ->get();
        return  response()->json([
            'data'=> $data,
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
        $meetingRooms = MeetingRoom::create( $request->all());
        return  response()->json([
            'data'=> $meetingRooms,
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
    
        $dateCurrent = date("Y-m-d H:i:s");
        $data = Rental::with('rentalServices', 'rentalEquipments')
            ->where('meeting_room_id', $id)
            ->where('rental_start', '<=', $dateCurrent)
            ->where('renral_end', '>=', $dateCurrent)
            ->get();
        if (count($data) == 0) {
            return  response()->json([
                'message' => $this->msgNoData
            ]);
        }    
        return  response()->json([
            'data'=> $data,
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
        $meetingRoom = MeetingRoom::find($id);
        if (is_null($meetingRoom)) {
            return  response()->json([
                'message' => $this->msgNoData
            ]);
        }
        
        $meetingRoom->update($request->all());
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
        $meetingRoom = MeetingRoom::find($id);
        if (is_null($meetingRoom)) {
            return  response()->json([
                'message' => $this->msgNoData
            ]);
        }

        $meetingRoom->delete();
        return  response()->json([
            'status' => 200,
            'message' => $this->success,
        ]);
    }
}
