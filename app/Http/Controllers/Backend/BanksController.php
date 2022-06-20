<?php

namespace App\Http\Controllers\Backend;

use App\Bank;
use App\Gateway;
use App\BankAmount;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class BanksController extends Controller
{
	public function index(Request $request)
	{
        $query = '1=1';
        $name = $request->input('name');
        $status = $request->input('status', -1);
        $qrCode = $request->input('qr_code', -1);
        $isPartner = $request->input('is_partner', -1);

        if( $name ) $query .= " AND name like '%" . $name . "%'";
        if($status <> -1) $query .= " AND status = {$status}";
        if($qrCode <> -1) $query .= " AND qr_code = {$qrCode}";
        if($isPartner <> -1) $query .= " AND is_partner = {$isPartner}";
        $banks = Bank::whereRaw($query)->orderBy('updated_at', 'DESC')->paginate(\App\Define\Constant::PAGE_NUM_20);

		return view('backend.banks.index', compact('banks'));
	}

	public function create(Request $request)
	{
        $gateways   = Gateway::where('status', 1)->pluck('name', 'id')->toArray();
        $types      = \App\Define\Bank::getSelectTypes();
        $users      = \App\Define\Bank::getSelectUsers();
        return view('backend.banks.create', compact('gateways', 'types', 'users'));
	}

	public function store(Request $request)
	{
        $request->merge(['status' => $request->input('status', 0), 'fee_fixed' => str_replace(' ', '', $request->input('fee_fixed', 0)), 'fee_percent' => str_replace(' ', '', $request->input('fee_percent', 0)), 'qr_code' => intval($request->input('qr_code', 0)), 'is_partner' => intval($request->input('is_partner', 0))]);

		$validator = \Validator::make($data = $request->all(), Bank::rules());
		$validator->setAttributeNames(trans('banks'));

		if ($validator->fails()) return back()->withErrors($validator)->withInput();

        if( Bank::where('gateway_id', $data['gateway'])->where('code', $data['code'])->where('name', $data['name'])->count() ) {
            Session::flash('message', 'Đã tồn tại ngân hàng thực hiện qua cổng thanh toán này.');
            Session::flash('alert-class', 'danger');
            return back();
        }

        $gateway    = Gateway::where('id', $data['gateway'])->where('status', 1)->first();
        if(is_null($gateway) || !in_array($data['type'], [0, 1])) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return back();
        }

        $logo = $request->file('logo');
        $data['logo'] = str_slug( $data['name'] ) . '.' . pathinfo($logo->getClientOriginalName(), PATHINFO_EXTENSION);
        $logo->move(config('upload.banks'), $data['logo']);
        $data['logo'] = config('upload.banks') . $data['logo'];

        $data['gateway_id'] = $gateway->id;
        $data['created_by'] = $request->user()->id;
        if( $data['type'] ) {
            $data['raw_fee_fixed']      = $gateway->fee_external_fixed;
            $data['raw_fee_percent']    = $gateway->fee_external_percent;
        } else {
            $data['raw_fee_fixed']      = $gateway->fee_internal_fixed;
            $data['raw_fee_percent']    = $gateway->fee_internal_percent;
        }
        Bank::create($data);

        Session::flash('message', trans('system.success'));
        Session::flash('alert-class', 'success');

        return redirect()->route('admin.banks.index');
	}

	public function show(Request $request, $id)
	{
        $bank = Bank::find(intval($id));
        if ( is_null( $bank ) ) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return back();
        }

        $gateways   = Gateway::where('status', 1)->pluck('name', 'id')->toArray();
        $types      = \App\Define\Bank::getSelectTypes();
        $users      = \App\Define\Bank::getSelectUsers();

        return view('backend.banks.show', compact( 'bank', 'gateways', 'types', 'users' ) );
	}

	public function edit(Request $request, $id)
	{
        $bank = Bank::find(intval($id));
        if ( is_null( $bank ) ) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return back();
        }

        $gateways   = Gateway::where('status', 1)->pluck('name', 'id')->toArray();
        $types      = \App\Define\Bank::getSelectTypes();
        $users      = \App\Define\Bank::getSelectUsers();

		return view('backend.banks.edit', compact( 'bank', 'gateways', 'types', 'users' ) );
	}

	public function update(Request $request, $id)
	{
        $id = intval( $id );
        $request->merge(['status' => $request->input('status', 0), 'fee_fixed' => str_replace(' ', '', $request->input('fee_fixed', 0)), 'fee_percent' => str_replace(' ', '', $request->input('fee_percent', 0)), 'qr_code' => intval($request->input('qr_code', 0)), 'is_partner' => intval($request->input('is_partner', 0))]);

        $bank = Bank::find($id);
        if ( is_null( $bank ) ) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'alert-danger');
            return back();
        }

        $validator = \Validator::make($data = $request->all(), Bank::rules($id));
        $validator->setAttributeNames(trans('banks'));

        if ($validator->fails()) return back()->withErrors($validator)->withInput();

        if( Bank::where('gateway_id', $data['gateway'])->where('code', $data['code'])->where('name', $data['name'])->where('id', '<>', $id)->count() ) {
            Session::flash('message', 'Đã tồn tại ngân hàng thực hiện qua cổng thanh toán này.');
            Session::flash('alert-class', 'danger');
            return back();
        }

        $gateway = Gateway::where('id', $data['gateway'])->where('status', 1)->first();
        if(is_null($gateway) || !in_array($data['type'], [0, 1])) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return back();
        }

        if ( $bank->orders()->count() && $data['code'] <> $bank->code ) {
            Session::flash('message', 'Ngân hàng đã phát sinh giao dịch, không thay đổi mã ngân hàng.');
            Session::flash('alert-class', 'danger');
            return back();
        }

        if ($request->hasFile('logo')) {
            if (\File::exists(public_path() . '/' . $gateway->logo)) \File::delete(public_path() . '/' . $gateway->logo);
            $logo = $request->file('logo');
            $data['logo'] = str_slug($data['name']) . date('dmYHis') . '.' . pathinfo($logo->getClientOriginalName(), PATHINFO_EXTENSION);
            $logo->move(config('upload.banks'), $data['logo']);
            $data['logo'] = config('upload.banks') . $data['logo'];
        } else {
            $data['logo'] = $bank->logo;
        }

        $data['gateway_id'] = $gateway->id;
        if( $data['type'] ) {
            $data['raw_fee_fixed'] = $gateway->fee_external_fixed;
            $data['raw_fee_percent'] = $gateway->fee_external_percent;
        } else {
            $data['raw_fee_fixed'] = $gateway->fee_internal_fixed;
            $data['raw_fee_percent'] = $gateway->fee_internal_percent;
        }
        $bank->update($data);
        Session::flash('message', trans('system.success'));
        Session::flash('alert-class', 'success');

        return redirect()->route('admin.banks.index');
	}

	public function destroy(Request $request, $id)
	{
        $bank = Bank::find($id);
        if (is_null($bank)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return back();
        }

        if ($bank->orders()->count()) {
            Session::flash('message', 'Ngân hàng đã phát sinh giao dịch, không thể xóa.');
            Session::flash('alert-class', 'danger');
            return back();
        }
        if (\File::exists(public_path() . '/' . $bank->logo)) \File::delete( public_path() . '/' . $bank->logo);
        $bank->delete();
		Session::flash('message', trans('system.success'));
        Session::flash('alert-class', 'success');

		return redirect()->route('admin.banks.index');
	}

    public function getGateway(Request $request)
    {
        if( \Request::ajax() ) {
            $return     = [ 'error' => true, 'message' => '' ];

            $gateway    = Gateway::where('id', intval($request->input('gateway_id')))->where('status', 1)->first();
            $type       = intval($request->input('type', 0));
            if(is_null($gateway) || !in_array($type, [0, 1])) {
                $return['message'] = trans('system.have_an_error');
                return \Response::json($return);
            }

            $return['error']    = false;
            if($type == \App\Define\Bank::TYPE_INTERNAL)
                $return['message']  = [ 'fee_fixed' =>  \App\Helper\HString::currencyFormat( $gateway->fee_internal_fixed ), 'fee_percent' => \App\Helper\HString::decimalFormat( $gateway->fee_internal_percent ) ];
            else
                $return['message']  = [ 'fee_fixed' =>  \App\Helper\HString::currencyFormat( $gateway->fee_external_fixed ), 'fee_percent' => \App\Helper\HString::decimalFormat( $gateway->fee_external_percent ) ];

            return response()->json($return);
        }
    }

    public function getFee(Request $request)
    {
        if( \Request::ajax() ) {
            $return     = [ 'error' => true, 'message' => '' ];

            $bank    = Bank::where('code', trim($request->input('bank_code')))->where('status', 1)->first();
            if (is_null($bank)) {
                $return['message'] = 'Bạn cần nhập thông tin Ngân hàng cho người dùng đã chọn trước.';
                return \Response::json($return);
            }

            $return['error']    = false;
            $return['message']  = [ 'fee_fixed' =>  \App\Helper\HString::currencyFormat( $bank->fee_fixed ), 'fee_percent' => \App\Helper\HString::decimalFormat( $bank->fee_percent ), 'raw_fee_fixed' =>  \App\Helper\HString::currencyFormat( $bank->raw_fee_fixed ), 'raw_fee_percent' => \App\Helper\HString::decimalFormat( $bank->raw_fee_percent ) ];

            return response()->json($return);
        }
    }
}