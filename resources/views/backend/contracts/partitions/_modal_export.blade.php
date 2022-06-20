<div id="modal-export-{{$item->id}}" class="modal fade modal-export" tabindex="-1" role="dialog" aria-hidden="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: #3c8dbc; font-size: 18px; font-weight: 600;">
                <span class="modal-title">{!! trans('contracts.report_user') !!} {{ $item->user->fullname }}</span>
            </div>
            <div class="modal-body">
                <!-- <div class="row">
                    <div class="col col-md-12"> -->
                <div class="row" style="border-bottom: 1px solid #ccc; padding: 5px">
                    <div class="col-md-8" style="font-size: 18px; font-weight: 500; margin-bottom: 5px">
                        {!! trans('contracts.contract_info') !!}
                        <a href="{!! route('admin.contracts.export', $item->id) !!}"
                           class="btn btn-xs"
                           target="_blank"
                           data-toggle="tooltip" data-placement="top" title="{!! trans('contracts.download') !!}"
                           data-id="{!! $item->id !!}" style="outline: none;">
                            <i class="text-success glyphicon glyphicon-download-alt"></i>
                        </a>
                    </div>
                </div>
                @if($item->type_status == \App\Defines\Contract::TRANSFER)
                    {!! Form::open(['url' => route('admin.contracts.export-tranfer', $item->id), 'role' => 'form','id'=>'form-tranfer-'. $item->id]) !!}
                    <div class="row" style="border-bottom: 1px solid #ccc; padding: 5px">
                            <span class="col col-md-12"
                                  style="font-size: 18px; font-weight: 500; margin: 0 auto; padding: 10px 15px;"
                                  data-id="{{$item->id}}">
                                {!! trans('contracts.transfer') !!}
                                @if(\App\Models\Contract::checkTransfer($item->id) === 2)
                                    <span class="text-danger">{!! trans('contracts.not_new_contract') !!}.</span>
                                @else
                                    <button type="submit" style="outline: none; background: #fff"
                                            class="btn btn-xs btn-tranfer" data-toggle="tooltip" data-placement="top"
                                            title="{!! trans('contracts.download') !!}"><i
                                                class="text-success glyphicon glyphicon-download-alt"></i></button>
                                @endif
                            </span>
                        @if(\App\Models\Contract::checkTransfer($item->id) !== 2)
                        <label class="col-md-5">{!! trans('contracts.notvalid_date') !!}</label>
                        <label class="btn btn-default btn-sm col-md-4">{!! date('d/m/Y', strtotime($item->set_notvalid_on)) !!}</label>
                        <label class="col-md-5">{!! trans('contracts.report_valid') !!}</label>
                        <label class="btn btn-default btn-sm col-md-4">{!! date('d/m/Y', strtotime($item->report_valid)) !!}</label>
                        @endif
                    </div>
                    {!! Form::close() !!}
                    {!! Form::open(['url' => route('admin.contracts.export-quit-job', $item->id), 'role' => 'form','id'=>'form-quit-job-'. $item->id]) !!}
                    {!! Form::hidden('transfer_date', $item->set_notvalid_on) !!}
                    {!! Form::hidden('transfer_valid', $item->report_valid) !!}
                    <div class="row" style="border-bottom: 1px solid #ccc; padding: 5px">
                            <span class="col col-md-12"
                                  style="font-size: 18px; font-weight: 500; margin: 0 auto; padding: 10px 15px;"
                                  data-id="{{$item->id}}">
                                {!! trans('contracts.quit_job') !!}
                                <button type="submit" style="outline: none; background: #fff"
                                        class="btn btn-xs btn-quit-job" data-toggle="tooltip" data-placement="top"
                                        title="{!! trans('contracts.download') !!}">
                                    <i class="text-success glyphicon glyphicon-download-alt"></i>
                                </button>
                            </span>
                    </div>
                    {!! Form::close() !!}
                @endif
                @if($item->type_status == \App\Defines\Contract::LEAVE_WORK || $item->type_status == \App\Defines\Contract::CHO_NGHI_VIEC)
                    {!! Form::open(['url' => route('admin.contracts.export-quit-job', $item->id), 'role' => 'form','id'=>'form-quit-job-'. $item->id]) !!}
                    <div class="row" style="border-bottom: 1px solid #ccc; padding: 5px">
                            <span class="col col-md-12"
                                  style="font-size: 18px; font-weight: 500; margin: 0 auto; padding: 10px 15px;"
                                  data-id="{{$item->id}}">
                                {!! trans('contracts.quit_job') !!}
                                <button type="submit" style="outline: none; background: #fff"
                                        class="btn btn-xs btn-quit-job" data-toggle="tooltip" data-placement="top"
                                        title="{!! trans('contracts.download') !!}"><i class="text-success glyphicon glyphicon-download-alt"></i></button>
                            </span>
                        <label class="col-md-5">{!! trans('contracts.staff_submit_date') !!}</label>
                        <label class="btn btn-default btn-sm col-md-4">{!! date('d/m/Y', strtotime($item->staff_submit_date)) !!}</label>
                        <label class="col-md-5">{!! trans('contracts.notvalid_date') !!}</label>
                        <label class="btn btn-default btn-sm col-md-4">{!! date('d/m/Y', strtotime($item->set_notvalid_on)) !!}</label>
                        <label class="col-md-5">{!! trans('contracts.report_valid') !!}</label>
                        <label class="btn btn-default btn-sm col-md-4">{!! date('d/m/Y', strtotime($item->report_valid)) !!}</label>
                    </div>
                    {!! Form::close() !!}
                @endif
                @if($item->type_status == \App\Defines\Contract::APPOINT)
                    {!! Form::open(['url' => route('admin.contracts.export-appoint', $item->id), 'role' => 'form','id'=>'form-appoint-'. $item->id]) !!}
                    <div class="row" style="border-bottom: 1px solid #ccc; padding: 5px">
                            <span class="col col-md-12"
                                  style="font-size: 18px; font-weight: 500; margin: 0 auto; padding: 10px 15px;"
                                  data-id="{{$item->id}}">
                                {!! trans('contracts.appoint') !!}
                                @if(\App\Models\Contract::checkAppoint($item->id) === 2)
                                    <span class="text-danger">{!! trans('contracts.not_new_contract') !!}.</span>
                                @else
                                    <button type="submit" style="outline: none; background: #fff"
                                            class="btn btn-xs btn-appoint" data-toggle="tooltip" data-placement="top"
                                            title="{!! trans('contracts.download') !!}"><i
                                                class="text-success glyphicon glyphicon-download-alt"></i></button>
                                @endif
                            </span>
                        @if(\App\Models\Contract::checkAppoint($item->id) !== 2)
                        <label class="col-md-5">{!! trans('contracts.notvalid_date') !!}</label>
                        <label class="btn btn-default btn-sm col-md-4">{!! date('d/m/Y', strtotime($item->set_notvalid_on)) !!}</label>
                        <label class="col-md-5">{!! trans('contracts.report_valid') !!}</label>
                        <label class="btn btn-default btn-sm col-md-4">{!! date('d/m/Y', strtotime($item->report_valid)) !!}</label>
                        @endif
                    </div>
                    {!! Form::close() !!}
                @endif
                @if($item->type_status == \App\Defines\Contract::DISMISSAL)
                    {!! Form::open(['url' => route('admin.contracts.export-dismissal', $item->id), 'role' => 'form','id'=>'form-dismissal-'. $item->id]) !!}
                    <div class="row" style="padding: 5px 5px 0 5px">
                            <span class="col col-md-8"
                                  style="font-size: 18px; font-weight: 500; margin: 0 auto; padding: 10px 15px;"
                                  data-id="{{$item->id}}">
                                {!! trans('contracts.dismissal') !!}
                                <button type="submit" style="outline: none; background: #fff"
                                        class="btn btn-xs btn-dismissal" data-toggle="tooltip" data-placement="top"
                                        title="{!! trans('contracts.download') !!}"><i class="text-success glyphicon glyphicon-download-alt"></i></button>
                            </span>
                        <label class="col-md-5">{!! trans('contracts.notvalid_date') !!}</label>
                        <label class="btn btn-default btn-sm col-md-4">{!! date('d/m/Y', strtotime($item->set_notvalid_on)) !!}</label>
                        <label class="col-md-5">{!! trans('contracts.report_valid') !!}</label>
                        <label class="btn btn-default btn-sm col-md-4">{!! date('d/m/Y', strtotime($item->report_valid)) !!}</label>
                    </div>
                    {!! Form::close() !!}
                @endif
                <div class="row text-center" style="margin-top: 5px; font-size: 14px;">
                    <div id="show-mess" class="text-danger col col-md-12"></div>
                </div>
                <!-- </div>
            </div> -->
            </div>
            <div class="modal-footer" style="text-align: center; border: none; padding: 5px 0 12px 0;">
                <button type="button"
                        class="btn btn-danger btn-flat btn-close-modal"
                        id="cancel-event" class="close"
                        data-dismiss="modal"
                        aria-label="Close">
                    {!! trans('system.action.cancel') !!}
                </button>
            </div>
        </div>
    </div>
</div>
<script>
    $(function () {
        // callDatePickerDown()
        function validateFunc(tagForm) {
            let check = true
            tagForm.find('input.datepicker').each(function () {
                let d = $(this).val()
                let title = $(this).attr('title')
                if (!d) {
                    let mess = `${title} không để trống.`
                    toastr.warning(mess, "{!! trans('system.have_error') !!}")
                    check = false
                    return false
                }
            })
            return check
        }

        $('.btn-tranfer').on('click', function (e) {
            e.preventDefault();
            let id = $(this).parents('span').attr('data-id')
            document.getElementById('form-tranfer-' + id).submit();
        })
        $('.btn-quit-job').on('click', function (e) {
            e.preventDefault();
            let id = $(this).parents('span').attr('data-id')
            document.getElementById('form-quit-job-' + id).submit();
        })
        $('.btn-appoint').on('click', function (e) {
            e.preventDefault();
            let id = $(this).parents('span').attr('data-id')
            document.getElementById('form-appoint-' + id).submit();
        })
        $('.btn-dismissal').on('click', function (e) {
            e.preventDefault();
            let id = $(this).parents('span').attr('data-id')
            document.getElementById('form-dismissal-' + id).submit();
        })
    })
</script>
