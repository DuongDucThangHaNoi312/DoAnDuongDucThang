<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Impale;
use App\Models\PayOff;
use App\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ImpaleController extends Controller
{
    public function create(Request $request)
    {
        $type = '';
        $data = $request->all();
        if (!is_null($data['department_id'])) {
            $users = User::where('department_id', intval($data['department_id']))->where('active', 1)->get(['id', 'fullname', 'code']);
        }
        // $users = User::whereNotIn('id', ['1', '2', '3'])->paginate(20, ['id', 'fullname', 'code']);
        $month = $data['month'];
        $year = $data['year'];

        return view('backend.impales.create', compact('users', 'type', 'data'));
    }

    public function store(Request $request)
    {
        if ($request->ajax()) {
            $data = $request->all();
            $return = [];

            foreach ($data['user_ids'] as $key => $user_id) {
                $impale = Impale::where('user_id', $user_id)->where('month', $data['month'])->where('year', $data['year'])->pluck('id');
                try {
                    Impale::destroy($impale->toArray());
                } catch (Exception $e) {
                    return response()->json(['status' => 200, 'message' => 'Thất bại']);
                }

                $count = count($data['content_' . $user_id]);
                for ($i = 0; $i < $count; $i++) {
                    $content = $data['content_' . $user_id][$i];

                    if (!is_null($content)) {
                        $return[] = [
                            'content' => $data['content_' . $user_id][$i],
                            'amount_money' => str_replace('.', '', $data['amount_money_' . $user_id][$i]),
                            'user_id' => $user_id,
                            'created_by' => Auth::user()->id,
                            'month' => $data['month'],
                            'year' => $data['year']
                        ];
                    }
                    
                }

            }
            try {
                DB::table('impales')->insert($return);
                return response()->json(['status' => 200, 'message' => 'Thành công']);

            } catch (Exception $e) {
                return response()->json(['status' => 400, 'message' => $e->getMessage()]);
            }

        }
    }
}
