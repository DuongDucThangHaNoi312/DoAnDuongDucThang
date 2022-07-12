<?php

namespace App\Http\Controllers\Backend;

use App\Models\MeetingRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class MeetingRoomController extends Controller
{
    public function index(Request $request)
    {
        $meetingRooms = MeetingRoom::orderBy('updated_at', 'DESC')->get();
        return view('backend.meeting_rooms.index', compact('meetingRooms'));
    }

    function create()
    {
        return view('backend.meeting_rooms.create');
    }

    public function store(Request $request)
    {

        $request->merge(['status' => intval($request->input('status', 0))]);
        $request->merge(['price' => intval($request->input('price', 0))]);
        $data = $request->all();
        $validator = Validator::make($data, MeetingRoom::rules());
        $validator->setAttributeNames(trans('meeting-rooms'));
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        MeetingRoom::create([
            'name' => $data['name'],
            'telephone' => $data['telephone'],
            'description' => $data['description'],
            'status' => $data['status'],
            'price' => intval($data['price']),
        ]);

        Session::flash('message', trans('system.success'));
        Session::flash('alert-class', 'success');
        return redirect()->route('admin.meeting-rooms.index');
    }

    public function show($id)
    {
        $meetingRoom = MeetingRoom::find($id);
        if (is_null($meetingRoom)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.meeting-rooms.index');
        }
        return view('backend.meeting_rooms.show', compact('meetingRoom'));
    }

    public function edit($id)
    {
        $meetingRoom = MeetingRoom::find($id);
        if (is_null($meetingRoom)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.meeting-rooms.index');
        }
        return view('backend.meeting_rooms.edit', compact('meetingRoom'));
    }

    public function update(Request $request, $id)
    {
        $request->merge(['status' => $request->input('status', 0)]);
        $data = $request->all();
        $meetingRoom = MeetingRoom::find(intval($id));
        if (is_null($meetingRoom)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.meeting-rooms.index');
        }
        $validator = Validator::make($data, MeetingRoom::rules(intval($id)));
        $validator->setAttributeNames(trans('meeting-rooms'));
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $meetingRoom->update($data);
        Session::flash('message', trans('system.success'));
        Session::flash('alert-class', 'success');
        return redirect()->route('admin.meeting-rooms.index');
    }

    public function destroy($id)
    {
        $meetingRoom = MeetingRoom::find(intval($id));    
        if (is_null($meetingRoom)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.meeting-rooms.index');
        }
        $meetingRoom->delete();
        Session::flash('message', trans('system.success'));
        Session::flash('alert-class', 'success');
        return redirect()->route('admin.meeting-rooms.index');
    }
}
