<?php

namespace App\Http\Controllers\Api;

use App\Models\MeetingRoom;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MeetingRoomController extends Controller
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
        $query = MeetingRoom::query();
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
            $meetingRoom = MeetingRoom::find($data['id']);
            if (is_null($meetingRoom)) {
                return  response()->json([
                    'status'  => 200,
                    'message' => $this->msgNoData,
                    'data'    => [],
                ]);
            }
            $meetingRoom = $meetingRoom->update($data);
            $meetingRoom = MeetingRoom::find($data['id']);
        } else {
            $meetingRoom = MeetingRoom::create($data);
        }

        return  response()->json([
            'message' => $this->success,
            'status' => 200,
            'data'=> $meetingRoom,
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
              
        $meetingRoom = MeetingRoom::find($id);
        if (is_null($meetingRoom)) {
            return  response()->json([
                'status'  => 200,
                'message' => $this->msgNoData,
                'data'    => [],
            ]);
        }
        
        $meetingRoom->update($data);
        return  response()->json([
            'status'  => 200,
            'message' => $this->success,
            'data'    => $meetingRoom,
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
        
        $meetingRoom = MeetingRoom::find($data['id']);
        if (is_null($meetingRoom)) {
            return  response()->json([
                'status' => 200,
                'message' => $this->msgNoData,
                'data'=> [],
            ]);
        }

        $meetingRoom->delete();
        return  response()->json([
            'status' => 200,
            'message' => $this->success,
            'data'=> [],
        ]);
    }
}
