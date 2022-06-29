@extends('backend.master')
@section('title')
    {!! trans('menus.home') !!}
@stop
@section('head')
    <link rel="stylesheet"
          href="{!! asset('assets/backend/plugins/fullcalendar3.10.2/fullcalendar.min.css') !!}">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.css">
    <style>
        button {
            outline: none;
            border: none;
        }
        div.fc-toolbar .fc-center h2 {
            text-transform: capitalize;
        }
        .fc-past {
            background: #F5F5F6;
        }
        .fc-toolbar {
            padding: 0 10px !important;
            margin-top: 0 !important;
            margin-bottom: 2px !important;
        }
        canvas {
            /*margin: 0 auto;*/
        }
        #no-data{
            display: none;
            /*position: absolute;*/
            padding: 50px 0;
            text-align: center;
            font-size: 3rem;
            /*top: 15%;*/
            width: 100%;
        }
    </style>
@stop
@section('content')
    <section class="content-header" >
        <h1 class="title">{!! trans('system.dashboard') !!}</h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}"><i class="fa fa-home"></i> {!! trans('menus.home') !!}</a></li>
        </ol>
    </section>
{{--    <div style="border-bottom: 1px solid #ccc; margin: 10px 0 -15px 0"></div>--}}
    <section class="content">
		<?php $user = auth()->guard('admin')->user();?>
        @include('backend.pages._home_system')
           
    </section>
@stop
@section('footer')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"
            integrity="sha256-4iQZ6BVL4qNKlQ27TExEhBN1HFPvAvAMbFavKKosSWQ=" crossorigin="anonymous"></script>
    <script src="{!! asset('assets/backend/plugins/fullcalendar3.10.2/fullcalendar.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/fullcalendar3.10.2/locales/vi.min.js') !!}"></script>
{{--    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>--}}
{{--    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@1"></script>--}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.2.1/dist/chart.min.js"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/no-data-to-display.js"></script>
    <script type="text/javascript" charset="utf8"
            src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.js"></script>

@stop