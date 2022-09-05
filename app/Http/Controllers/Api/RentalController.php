<?php

namespace App\Http\Controllers\Api;

use App\Models\Rental;
use App\Models\RentalService;
use App\Models\RentalEquipment;
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
    public function index(Request $request)
    {
        $meetingRoomId = $request->meeting_room_id;
        $dateCurrent = date("Y-m-d H:i:s");
        $data = Rental::query();
        $data->with('rentalServices', 'rentalEquipments');
        
        if ($meetingRoomId) {
            $data->where('meeting_room_id', $meetingRoomId);
        }
        $data = $data->get();
        // $data = $data->where('rental_start', '<=', $dateCurrent)
        //     ->where('renral_end', '>=', $dateCurrent)
        //     ->get();
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $data = $data['data']['0'];
        $dataRental = [
            'meeting_room_id' => $data['meeting_room_id'],
            'user_id' => $data['user_id'],
            'rental_start' => $data['rental_start'],
            'renral_end' => $data['renral_end'],
            'status' => 1,
        ];

        $rental = Rental::create($dataRental);
        $rentalServices = $data['rental_services'];
        $rentalEquipments = $data['rental_equipments'];

        foreach ($rentalServices as $rentalService) {
            $rentalService['rental_history_id'] = $rental->id;
            RentalService::create($rentalService);
        }

        foreach ($rentalEquipments as $rentalEquipment) {
            $rentalEquipment['rental_history_id'] = $rental->id;
            RentalEquipment::create($rentalEquipment);
        }

        return  response()->json([
            'message' => $this->success,
            'status' => 200,
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


        $dataRental = [
            'meeting_room_id' => $data['meeting_room_id'],
            'user_id' => $data['user_id'],
            'rental_start' => $data['rental_start'],
            'renral_end' => $data['renral_end'],
            'status' => 1,
        ];

        $rental = Rental::create($dataRental);
        $rentalServices = $data['rental_services'];
        $rentalEquipments = $data['rental_equipments'];



        foreach ($rentalServices as $rentalService) {
            $rentalService['rental_history_id'] = $rental->id;
            RentalService::create($rentalService);
        }

        foreach ($rentalEquipments as $rentalEquipment) {
            $rentalEquipment['rental_history_id'] = $rental->id;
            RentalEquipment::create($rentalEquipment);
        }

        return  response()->json([
            'message' => $this->success,
            'status' => 200,
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
