<?php

namespace App\Http\Controllers\Backend;

use App\BannerAdvertisement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;

class BannerAdvertisementsController extends Controller
{
    public function index(Request $request)
    {
        $query = "1=1";
        $status = intval($request->input('status', -1));
        $type = trim($request->input('type'));
        if($status <> -1) $query .= " AND status = {$status}";
        if($type) $query .= " AND type = '{$type}'";
        $categories = BannerAdvertisement::whereRaw($query)->orderByRaw('updated_at DESC, type')->paginate(\App\Define\Constant::PAGE_NUM_20);
        return view('backend.banner-advertisements.index', compact('categories'));
    }

    public function create()
    {
        $types = \App\Define\Constant::getBannerTypes();
        return view('backend.banner-advertisements.create', compact('types'));
    }

    public function show($id)
    {
        $category = BannerAdvertisement::find(intval($id));
        if (is_null($category)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'error');
            return redirect()->route('admin.banner-advertisements.index');
        }

        return view('backend.banner-advertisements.show', compact('category'));
    }

    public function edit($id)
    {
        $category = BannerAdvertisement::find(intval($id));
        if (is_null($category)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'error');
            return redirect()->route('admin.banner-advertisements.index');
        }
        return view('backend.banner-advertisements.edit', compact('category'));
    }

    public function store(Request $request)
    {
        $request->merge(['status' => $request->input('status', 0)]);
        $validator = \Validator::make($data = $request->all(), BannerAdvertisement::rules());
        $validator->setAttributeNames(trans('banner_advertisements'));
        if ($validator->fails()) return back()->withErrors($validator)->withInput();
        $image  = $request->file('image');
        $ext    = pathinfo($image->getClientOriginalName(), PATHINFO_EXTENSION);
        $data['image'] = str_slug($data['name']) . '_' . time() . '.' . $ext;
        $image->move(config('upload.banner_advertisement'), $data['image']);
        $data['image'] = config('upload.banner_advertisement') . $data['image'];
        $data['created_by'] = $request->user()->id;
        BannerAdvertisement::create($data);
        Session::flash('message', trans('system.success'));
        Session::flash('alert-class', 'success');

        return redirect()->route('admin.banner-advertisements.index');
    }

    public function update(Request $request, $id)
    {
        $category = BannerAdvertisement::find(intval($id));
        if (is_null($category)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.banner-advertisements.index');
        }
        $request->merge(['status' => $request->input('status', 0)]);
        $validator = \Validator::make($data = $request->all(), BannerAdvertisement::rules(intval($id)));//only(['status', 'name', 'seo_description', 'seo_keywords', 'image'])
        $validator->setAttributeNames(trans('banner_advertisements'));
        if ($validator->fails()) return back()->withErrors($validator)->withInput();

        if ($request->hasFile('image')) {
            if (\File::exists(public_path() . '/' . $category->image)) \File::delete(public_path() . '/' . $category->image);
            $image  = $request->file('image');
            $ext    = pathinfo($image->getClientOriginalName(), PATHINFO_EXTENSION);
            $data['image'] = str_slug($data['name']) . '_' . time(). '.' . $ext;
            $image->move(config('upload.banner_advertisement'), $data['image']);
            $data['image'] = config('upload.banner_advertisement') . $data['image'];
        }
        $category->update($data);
        Session::flash('message', trans('system.success'));
        Session::flash('alert-class', 'success');

        return redirect()->route('admin.banner-advertisements.index');
    }

    public function destroy($id)
    {
        $category = BannerAdvertisement::find(intval($id));
        if (is_null($category)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.banner-advertisements.index');
        }
        if (\File::exists(public_path() . '/' . $category->image)) \File::delete(public_path() . '/' . $category->image);
        $category->delete();

        Session::flash('message', trans('system.success'));
        Session::flash('alert-class', 'success');
        return redirect()->route('admin.banner-advertisements.index');
    }
}