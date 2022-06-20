{!! Form::open(['method' => 'POST', 'id' => 'formShiftAndOT']) !!}
<div class="col-md-12">
    <label>{!! trans('shifts.shift_and_ot_one') !!} :</label>
</div>
<div class="col-md-12">
    <div class="form-group selectfirstShiftAndOt">
        {!! trans('shifts.people_shift_and_ot') !!}
        {!! Form::select('first_shift_and_ot[]', App\Define\Shift::getPeopleInDepartment($department->id) , old('first_shift_and_ot[]'), ['class' => 'form-control select2 first_shift_and_ot','multiple','id'=>'first_shift_and_ot']) !!}
    </div>
</div>
<div class="col-md-12">
    <label>{!! trans('shifts.shift_and_ot_two') !!} :</label>
</div>
<div class="col-md-12">
    <div class="form-group selectsecondShiftAndOt">
        {!! trans('shifts.people_shift_and_ot') !!}
        {!! Form::select('second_shift_and_ot[]',  App\Define\Shift::getPeopleInDepartment($department->id) , old('second_shift_and_ot[]'), ['class' => 'form-control select2 second_shift_and_ot','multiple','id'=>'second_shift_and_ot']) !!}
    </div>
</div>
<div class="col-md-12">
    <div class="form-group">
        <label>{!! trans('shifts.start_date') !!}<span style="color: red">*</span></label>

        <div class='input-group'>
            {!! Form::text('start_date', old('start_date'), ['class' => 'form-control start_date_shift_and_ot','id'=>'start_date_shift_and_ot' ,'placeholder'=>trans('shifts.start_date_placeholder'),'autocomplete'=>'off']) !!}
            <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                </span>
        </div>
    </div>
</div>
<div class="col-md-12">
    <div class="form-group">
        <label>{!! trans('shifts.end_date') !!}<span style="color: red">*</span></label>
        <div class='input-group'>
            {!! Form::text('end_date', old('end_date'), ['class' => 'form-control end_date_shift_and_ot','id'=>'end_date_shift_and_ot' ,'placeholder'=>trans('shifts.end_date_placeholder'),'autocomplete'=>'off']) !!}
            <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                </span>
        </div>
    </div>
</div>
<input type="hidden" name="department_id" value="{!! $department->id !!}">
<input type="hidden" name="day_click_shift" value="">
<input type="hidden" name="idShift" id="idShiftAndOT" value="">
<div class="modal-footer">
    <input type="button" id="delete-all-shift-and-ot" class="btn btn-danger btn-flat "
           value="{!! trans('calendar_departments.delete-all') !!}">
    <input type="button" id="delete-one-shift-and-ot" class="btn btn-danger btn-flat"
           value="{!! trans('calendar_departments.delete-one') !!}">
    <button type="button" class="btn btn-default" id="cancel-shift-and-ot"
            data-dismiss="modal">{!! trans('system.action.cancel') !!}</button>
    <button type="button" class="btn btn-primary"
            id="save-shift-and-ot">{!! trans('system.action.save') !!}</button>
    <input type="button" class="btn btn-primary btn-flat" id="update-shift-and-ot"
           value="{!! trans('calendar_departments.update') !!}">
    <button type="button" class="btn btn-primary"
            id="edit-shift-and-ot">{!! trans('system.action.save') !!}</button>
</div>
{!! Form::close() !!}
<script>
    !function ($) {
        $(function () {
            $('#start_date_shift_and_ot').datepicker({
                format: 'dd/mm/yyyy',
                useCurrent: false,
                autoclose: true,
                language: "vi",
            }).on('changeDate', function (e) {
                var minDate = new Date(e.date.valueOf());
                $('#end_date_shift_and_ot').datepicker('setStartDate', minDate);
            });
            $('#end_date_shift_and_ot').datepicker({
                format: 'dd/mm/yyyy',
                useCurrent: false,
                autoclose: true,
                language: "vi",
            }).on('changeDate', function (e) {
                var maxDate = new Date(e.date.valueOf());
                $('#start_date_shift_and_ot').datepicker('setEndDate', maxDate);
            });
        });
    }(window.jQuery);
</script>
<script>

    var departmentId = {!! $department->id !!};
    var url = ''

    $('body').on('click', 'a.work-shift', function () {

        if ($('input#idShiftAndOT').val() == '') {
            $('body').on('change', '#first_shift_and_ot', setUserOption)
        }

        $('#delete-all-shift-and-ot').hide()
        $('#delete-one-shift-and-ot').hide()
        $('#cancel-shift-and-ot').show()
        $('#update-shift-and-ot').hide()
        $('#save-shift-and-ot').show()
        $('#edit-shift-and-ot').hide()

    })

    $('body').on('click', '.ShiftAndOT', function () {
        var id = $(this).find('span.idShiftAndOT').text();
        $('input[name=idShift]').val(id)

        $('#formWorkShiftAndOT').modal({
            backdrop: 'static',
            keyboard: false
        })
        $('#formWorkShiftAndOT').modal('show')
        $('#delete-all-shift-and-ot').hide()
        $('#delete-one-shift-and-ot').hide()
        $('#cancel-shift-and-ot').hide()
        $('#update-shift-and-ot').show()
        $('#save-shift-and-ot').hide()
        $('#edit-shift-and-ot').hide()
        $("input[type=text]").attr('readonly', "readonly")
        $("select").attr('readonly', "readonly")
        var date = $(this).attr('id')
        $('input[name=day_click_shift]').val(moment(date).format('DD/MM/YYYY'))
        if (new Date(date) < new Date()) {
            $("select").prop('disabled', true)
            $("input").prop('disabled', true)
            $("textarea").prop('disabled', true)
        }
        url = '{!! route('admin.calendar.loadWorkShiftOneDay') !!}'
        var idShiftAndOT = {};
        idShiftAndOT['id'] = id
        actionShiftAndOT(url, 'GET', idShiftAndOT).done(function (res) {
            var data = res.data
            data.start_date == data.end_date ? $('#delete-all-shift-and-ot').show() : $('#delete-all-shift-and-ot').show() && $('#delete-one-shift-and-ot').show()
            new Date(data.start_date) < new Date() ? $('#delete-all-shift-and-ot').attr('disabled', 'disabled') : ''
            $('select#first_shift_and_ot').val(JSON.parse(data.first_shift_and_ot)).trigger('change.select2')
            $('select#second_shift_and_ot').val(JSON.parse(data.second_shift_and_ot)).trigger('change.select2')
            $('input#start_date_shift_and_ot').datepicker('update', moment(data.start_date).format('DD/MM/YYYY'))
            $('input#end_date_shift_and_ot').datepicker('setStartDate', new Date(moment(data.start_date)))
            $('input#end_date_shift_and_ot').datepicker('update', moment(data.end_date).format('DD/MM/YYYY'))
            if ($('input#idShiftAndOT').val() != '') {
                if ($('select#first_shift_and_ot').val()) {
                    var oldUserId = JSON.parse(data.second_shift_and_ot);
                    let datas = {
                        departmentId: departmentId,
                        userId: $('select#first_shift_and_ot').val(),
                        id: $('input[name=idShift]').val()
                    }
                    url = '{!! route('admin.calendar.firstShift') !!}'
                    actionShiftAndOT(url, 'POST', datas).done(function (res) {
                        $('select#second_shift_and_ot option').remove()
                        $('select#second_shift_and_ot').append('<option>' + '' + '</option>')
                        $.each(res, function (index, value) {
                            let isSelected = jQuery.inArray(index, oldUserId) != -1 ? 'selected' : ''
                            $('.second_shift_and_ot').append('<option value="' + index + '"' + isSelected + '>' + value + '</option>')
                        })
                    }).fail(function (res) {
                        console.log(res)
                    })
                }

            }


        }).fail(function (data) {
            toastr.error(response.responseJSON.data[0], "{!! trans('system.have_an_error') !!}")
        })
    })

    $('body').on('click', '#update-shift-and-ot', function () {
        reset()
        $('#delete-all-shift-and-ot').hide()
        $('#delete-one-shift-and-ot').hide()
        $('#cancel-shift-and-ot').show()
        $('#update-shift-and-ot').hide()
        $('#save-shift-and-ot').hide()
        $('#edit-shift-and-ot').show()
        $('input#start_date_shift_and_ot').datepicker('setStartDate', new Date($('input#start_date_shift_and_ot').datepicker('getDate')));
        $('input#start_date_shift_and_ot').datepicker('setEndDate', new Date($('input#end_date_shift_and_ot').datepicker('getDate')));
        $('input#end_date_shift_and_ot').datepicker('setStartDate', new Date($('input#start_date_shift_and_ot').datepicker('getDate')));
        $('input#end_date_shift_and_ot').datepicker('setEndDate', new Date($('input#end_date_shift_and_ot').datepicker('getDate')));
        $('#first_shift_and_ot').change(setUserOption)

    })


    $('body').on('click', '#save-shift-and-ot', function () {
        if (validateFormShiftAndOT() !== false) {
            $.ajax({
                url: "{!! route('admin.calendar.checkingWorkingDay') !!}",
                data: $('#formShiftAndOT').serialize(),
                type: 'POST',
                headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                success: function (data) {
                    $.ajax({
                        url: "{!! route('admin.calendar.storeShift') !!}",
                        data: $('#formShiftAndOT').serialize(),
                        type: 'POST',
                        headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                        success: function (data) {
                            window.location.reload();
                        },
                        error: function (response) {
                            response.responseJSON.data.start_date ? toastr.error(response.responseJSON.data.start_date[0], "{!! trans('system.have_an_error') !!}") : ''
                            response.responseJSON.data.end_date ? toastr.error(response.responseJSON.data.end_date[0], "{!! trans('system.have_an_error') !!}") : ''
                        }
                    })
                },
                error: function (response) {
                    toastr.error(response.responseJSON.message, "{!! trans('system.have_an_error') !!}")
                }
            })
        }
    })
    $('body').on('click', '#edit-shift-and-ot', function () {
        swal({
            title: '{{ trans('shifts.confirm_edit') }}',
            icon: "warning",
            closeOnClickOutside: false,
            buttons: {
                edit_all: {
                    text: '{!! trans('shifts.edit_all') !!}',
                    value: 'edit_all',
                    className: "btn btn-info",
                },
                edit_one: {
                    text: "{!! trans('shifts.edit_one') !!}",
                    value: 'edit_one',
                    className: "btn btn-info",
                },
                cancel: {
                    text: "{!! trans('shifts.cancel') !!}",
                    value: null,
                    className: "btn",
                    visible: true,
                },
            },
        }).then((value)=>{
            var url = '';
            switch (value) {
                case "edit_all":
                    url = '{!! route('admin.calendar.updateShiftAll') !!}';
                    break;
                case "edit_one":
                    url = '{!! route('admin.calendar.updateShift') !!}';
                    break;
            }
            if (validateFormShiftAndOT() !== false) {
                $.ajax({
                    url: url,
                    data: $('#formShiftAndOT').serialize(),
                    type: 'POST',
                    headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                    success: function (data) {
                        // window.location.reload();
                    },
                    error: function (response) {
                        response.responseJSON.data.start_date ? toastr.error(response.responseJSON.data.start_date[0], "{!! trans('system.have_an_error') !!}") : ''
                        response.responseJSON.data.end_date ? toastr.error(response.responseJSON.data.end_date[0], "{!! trans('system.have_an_error') !!}") : ''
                    }
                })
            }
        })
    })
    $('body').on('click', '#delete-all-shift-and-ot', function () {
        swal({
            title: "{!! trans('shifts.confirm') !!}",
            text: "{!! trans('shifts.title_confirm_all') !!}",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
            .then((willDelete) => {
                if (willDelete) {
                    $.ajax({
                        url: "{!! route('admin.calendar.deleteAllShift') !!}",
                        data: $('#formShiftAndOT').serialize(),
                        type: 'POST',
                        headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                        success: function (data) {
                            window.location.reload();
                        },
                        error: function (response) {
                            toastr.error(response.responseJSON.data[0], "{!! trans('system.have_an_error') !!}")

                        }
                    })
                }
            });
    })
    $('body').on('click', '#delete-one-shift-and-ot', function () {
        var date = $('input[name=day_click_shift]').val()
        console.log(date)
        swal({
            title: "{!! trans('shifts.confirm') !!}",
            text: '{{ trans('shifts.title_confirm_one_1') }} ' + date + ' {{ trans('shifts.title_confirm_one_2') }}',
            icon: "warning",
            buttons: true,
            dangerMode: true,

        })
            .then((willDelete) => {
                if (willDelete) {
                    $.ajax({
                        url: "{!! route('admin.calendar.deleteOneShift') !!}",
                        data: $('#formShiftAndOT').serialize(),
                        type: 'POST',
                        headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                        success: function (data) {
                            window.location.reload();
                        },
                        error: function (response) {
                            toastr.error(response.responseJSON.data[0], "{!! trans('system.have_an_error') !!}")
                        }
                    })
                }
            });
    })
    $('#formWorkShiftAndOT').on('hidden.bs.modal', function (e) {
        $(this).find('form').trigger('reset');
        $('form').find('input[type=text], textarea').val(null)
        $('form').find('input[name=day_click_shift],input[name=idShift]').val(null)
        $('form').find('select').val(null).trigger('change.select2')
        $('form').find("input[type=text] ").datepicker('setStartDate','');
        $('form').find("input[type=text] ").datepicker('setEndDate','');
        reset()
    })

    function setUserOption() {
        let datas = {
            departmentId: departmentId,
            userId: $('select#first_shift_and_ot').val(),
        }
        url = '{!! route('admin.calendar.firstShift') !!}'
        actionShiftAndOT(url, 'POST', datas).done(function (res) {
            $('#second_shift_and_ot option').remove()
            $('#second_shift_and_ot').append('<option>' + '' + '</option>')
            $.each(res, function (index, value) {
                $('#second_shift_and_ot').append('<option value="' + index + '">' + value + '</option>')
            })
        }).fail(function (res) {
            console.log(res)
        })
    }

    function validateFormShiftAndOT() {
        if ($('input#start_date_shift_and_ot').val() == '') {
            toastr.error("{!! trans('shifts.error_start_date_required') !!}", "{!! trans('system.have_an_error') !!}")
            return false;
        } else if ($('input#start_date_shift_and_ot').datepicker('getDate') < new Date()) {
            toastr.error("{!! trans('shifts.error_out_date') !!}", "{!! trans('system.have_an_error') !!}")
            return false;
        } else if ($('input#end_date_shift_and_ot').val() == '') {
            toastr.error("{!! trans('shifts.error_end_date_required') !!}", "{!! trans('system.have_an_error') !!}")
            return false;
        } else if ($('select#first_shift_and_ot').val() == '' && $('select#second_shift_and_ot').val() == '') {
            toastr.error("{!! trans('shifts.cannot_required_shift_all') !!}", "{!! trans('system.have_an_error') !!}")
            return false;
        } else if ($('input#end_date_shift_and_ot').datepicker('getDate') < $('input#start_date_shift_and_ot').datepicker('getDate')) {
            toastr.error("{!! trans('shifts.error_end_date_bigger') !!}", "{!! trans('system.have_an_error') !!}")
            return false;
        }

        var firstShift = $('select.first_shift_and_ot').val()
        var secondShift = $('select.second_shift_and_ot').val()
        if (secondShift) {
            $.each(secondShift, function (index, value) {
                if (jQuery.inArray(value, firstShift) >= 0) {
                    toastr.error("{!! trans('shifts.must_one') !!}", "{!! trans('system.have_an_error') !!}")
                    return false;
                }
            })
        }
        return true
    }

    function actionShiftAndOT($url, $type, $params) {
        return $.ajax({
            url: $url,
            type: $type,
            data: $params,
            headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
        });
    }

    function reset() {
        $('input').each(function () {
            $(this).attr('disabled', false)
            $(this).removeAttr('readonly')
        })
        $('textarea').each(function () {
            $(this).attr('disabled', false)
            $(this).removeAttr('readonly')

        })
        $('select').each(function () {
            $(this).attr('disabled', false)
            $(this).removeAttr('readonly')
        })
    }

</script>