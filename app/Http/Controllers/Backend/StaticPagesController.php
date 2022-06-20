<?php

namespace App\Http\Controllers\Backend;

use App\StaticPage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class StaticPagesController extends Controller
{
    public function index(Request $request)
    {
        $title = trim($request->title);
        if ($title) {
            $news = StaticPage::where('title', 'like', '%' . $title . '%')->get();
        } else {
            $news = StaticPage::get();
        }

        return view('backend.static-pages.index', compact('news'));
    }

    public function create(Request $request)
    {
        return view('backend.static-pages.create');
    }

    public function store(Request $request)
    {
        $request->merge(['status' => intval($request->input('status', 0))]);
        $validator = Validator::make($data = $request->only('group', 'description', 'status', 'title'), StaticPage::rules());
        $validator->setAttributeNames(trans('static_pages'));
        if ($validator->fails()) return back()->withErrors($validator)->withInput();

        $data['slug'] = str_slug($data['title']);
        if (StaticPage::where('slug', $data['slug'])->count()) {
            $errors = new \Illuminate\Support\MessageBag;
            $errors->add(
                'editError',
                'Tiêu đề đã tồn tại'
            );
            return back()->withErrors($errors)->withInput();
        }
        $data['type'] = \App\Define\Constant::STATIC_PAGE_SIMPLE;
        StaticPage::create($data);

        Session::flash('message', trans('system.success'));
        Session::flash('alert-class', 'success');

        return redirect()->route('admin.static-pages.index');
    }

    public function show(Request $request, $id)
    {
        $news = StaticPage::find($id);
        if ( is_null( $news ) ) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return back();
        }

        return view('backend.static-pages.show', compact('news'));
    }

    public function edit(Request $request, $id)
    {
        $news = StaticPage::find($id);
        if ( is_null( $news ) ) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return back();
        }

        return view('backend.static-pages.edit', compact('news'));
    }

    public function update(Request $request, $id)
    {
        $id = intval($id);
        $news = StaticPage::find($id);
        if (is_null($news)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return back();
        }

        $request->merge(['status' => intval($request->status)]);
        $data = $request->only('title', 'group', 'description', 'status');
        if ($news->group) unset($data['group']);
        $validator = Validator::make($data, StaticPage::rules($id, $news->type == \App\Define\Constant::STATIC_PAGE_IMAGE));
        $validator->setAttributeNames(trans('static_pages'));
        if ($validator->fails()) return back()->withErrors($validator)->withInput();
        // if ($news->group) $data['slug'] = str_slug($data['title']);

        if ($news->type == \App\Define\Constant::STATIC_PAGE_IMAGE && $request->hasFile('description')) {
            if (\File::exists(public_path() . '/' . $news->description)) \File::delete(public_path() . '/' . $news->description);
            $description  = $request->file('description');
            $title  = str_slug($data['title']) . '_' . date("dmY.His");
            $ext    = pathinfo($description->getClientOriginalName(), PATHINFO_EXTENSION);
            $data['description'] = $title . '.' . $ext;
            $description->move(config('upload.static'), $data['description']);
            $path = config('upload.static') . $data['description'];
            $description = \Image::make($path);
            $description->save(config('upload.static') . $data['description']);
            $data['description'] = config('upload.static') . $data['description'];
        }
        $news->update($data);

        Session::flash('message', trans('system.success'));
        Session::flash('alert-class', 'success');

        return redirect()->route('admin.static-pages.index');
    }

    public function destroy(Request $request, $id)
    {
        $news = StaticPage::find($id);
        if (is_null($news) || !$news->group) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return back();
        }

        $news->delete();

        Session::flash('message', trans('system.success'));
        Session::flash('alert-class', 'success');
        return redirect()->route('admin.static-pages.index');
    }
}