<section class="content-header">
    <h1>
        {!! trans($name) !!}
        <small>{!! trans($key) !!}</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
        <li><a href="{!! route('admin.staffs.index') !!}">{!! trans($name) !!}</a></li>
    </ol>
</section>