{!! Form::open(['method' => 'POST', 'id' => 'formShift']) !!}
<div class="col-md-12">
    <label>{!! trans('shifts.shift_one') !!} :</label>
</div>
<div class="col-md-12">
    <div class="form-group">
        {!! trans('shifts.people') !!}
        {!! Form::select('first_shift[]', App\Define\Shift::getPeopleInDepartment($department->id) , old('first_shift[]'), ['class' => 'form-control select2 first_shift','multiple','id'=>'first_shift']) !!}
    </div>
</div>
<div class="col-md-12">
    <label>{!! trans('shifts.shift_two') !!} :</label>
</div>
<div class="col-md-12">
    <div class="form-group">
        {!! trans('shifts.people') !!}
        {!! Form::select('second_shift[]',  App\Define\Shift::getPeopleInDepartment($department->id) , old('second_shift[]'), ['class' => 'form-control select2 second_shift','multiple','id'=>'second_shift']) !!}
    </div>
</div>
<div class="col-md-12">
    <label>{!! trans('shifts.shift_three') !!} :</label>
</div>
<div class="col-md-12">
    <div class="form-group">
        {!! trans('shifts.people') !!}
        {!! Form::select('third_shift[]',  App\Define\Shift::getPeopleInDepartment($department->id), old('third_shift[]'), ['class' => 'form-control select2 third_shift','multiple','id'=>'third_shift']) !!}
    </div>
</div>
<div class="col-md-12">
    <div class="form-group">
        <label>{!! trans('shifts.start_date') !!}<span style="color: red">*</span></label>

        <div class='input-group'>
            {!! Form::text('start_date', old('start_date'), ['class' => 'form-control start_date_shift','id'=>'start_date_shift' ,'placeholder'=>trans('shifts.start_date_placeholder'),'autocomplete'=>'off']) !!}
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
            {!! Form::text('end_date', old('end_date'), ['class' => 'form-control end_date_shift','id'=>'end_date_shift' ,'placeholder'=>trans('shifts.end_date_placeholder'),'autocomplete'=>'off']) !!}
            <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                </span>
        </div>
    </div>
</div>
<input type="hidden" name="department_id" value="{!! $department->id !!}">
<input type="hidden" name="day_click_shift" value="">
<input type="hidden" name="idShift" id="idShift" value="">
<div class="modal-footer">
    <input type="button" id="delete-all-shift" class="btn btn-danger btn-flat "
           value="{!! trans('calendar_departments.delete-all') !!}">
    <input type="button" id="delete-one-shift" class="btn btn-danger btn-flat "
           value="{!! trans('calendar_departments.delete-one') !!}">
    <button type="button" class="btn btn-default " id="cancel-shift"
            data-dismiss="modal">{!! trans('system.action.cancel') !!}</button>
    <button type="button" class="btn btn-primary "
            id="save-shift">{!! trans('system.action.save') !!}</button>
    <input type="button" class="btn btn-primary btn-flat " id="update-shift"
           value="{!! trans('calendar_departments.update') !!}">
    <button type="button" class="btn btn-primary "
            id="edit-shift">{!! trans('system.action.save') !!}</button>
</div>
{!! Form::close() !!}
<script>
    !function ($) {
        $(function () {
            $('#start_date_shift').datepicker({

                format: 'dd/mm/yyyy',
                useCurrent: false,
                autoclose: true,
                language: "vi",
            }).on('changeDate', function (e) {
                var minDate = new Date(e.date.valueOf());
                $('#end_date_shift').datepicker('setStartDate', minDate);
            });
            $('#end_date_shift').datepicker({
                format: 'dd/mm/yyyy',
                useCurrent: false,
                autoclose: true,
                language: "vi",
            }).on('changeDate', function (e) {
                var maxDate = new Date(e.date.valueOf());
                $('#start_date_shift').datepicker('setEndDate', maxDate);
            });
        });
    }(window.jQuery);
</script>
<script>

    var departmentId = {!! $department->id !!};
    var url = ''
    $('body').on('click', 'a.work-shift', function () {
        $('#cancel-shift').show()
        $('#save-shift').show()
        $('#delete-all-shift').hide()
        $('#delete-one-shift').hide()
        $('#edit-shift').hide()
        $('#update-shift').hide()
        if ($('input#idShift').val() == '') {
            $('body').on('change', '#first_shift', setUserOption2)
            $('body').on('change', '#second_shift',setUserOption3)
        }


    })
    $('body').on('click', '.Shift', function () {
        var id = $(this).find('span.idShift').text();
        $('input[name=idShift]').val(id)
        $('#cancel-shift').hide()
        $('#save-shift').hide()
        $('#delete-all-shift').hide()
        $('#delete-one-shift').hide()
        $('#edit-shift').hide()
        $('#update-shift').show()

        $('#formWorkShift').modal({
            backdrop: 'static',
            keyboard: false
        })
        $('#formWorkShift').modal('show')
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
        var idShift = {};
        idShift['id'] = id
        actionShift(url, 'GET', idShift)
            .done(function (res) {
                var data = res.data
                data.start_date == data.end_date ? $('#delete-all-shift').show() : $('#delete-all-shift').show() && $('#delete-one-shift').show()
                new Date(data.start_date) < new Date() ? $('#delete-all-shift').attr('disabled', 'disabled') : ''
                $('select#first_shift').val(JSON.parse(data.first_shift)).trigger('change.select2')
                $('select#second_shift').val(JSON.parse(data.second_shift)).trigger('change.select2')
                $('select#third_shift').val(JSON.parse(data.third_shift)).trigger('change.select2')
                $('input#start_date_shift').datepicker('update', moment(data.start_date).format('DD/MM/YYYY'))
                $('input#end_date_shift').datepicker('setStartDate', new Date(moment(data.start_date)))
                $('input#end_date_shift').datepicker('update', moment(data.end_date).format('DD/MM/YYYY'))
                if ($('input#idShift').val() != '') {
                    if ($('#first_shift').val()) {
                        let oldUserId = JSON.parse(data.second_shift);
                        url = "{!! route('admin.calendar.firstShift') !!}"
                        let datas = {
                            departmentId: departmentId,
                            userId: $('select#first_shift').val(),
                        }
                        actionShift(url, 'POST', datas)
                            .done(function (res) {
                                $('#second_shift option').remove()
                                $('#second_shift').append('<option>' + '' + '</option>')
                                $.each(res, function (index, value) {
                                    let isSelected = jQuery.inArray(index, oldUserId) != -1 ? 'selected' : ''
                                    $('#second_shift').append('<option value="' + index + '"' + isSelected + '>' + value + '</option>')
                                })
                            })
                            .fail(function (response) {
                                toastr.error(response.responseJSON.data[0], "{!! trans('system.have_an_error') !!}")
                            })
                    }
                    if ($('#second_shift').val() || $('#first_shift').val()) {
                        let oldUserId = JSON.parse(data.third_shift);
                        url = "{!! route('admin.calendar.secondShift') !!}"
                        let datas = {
                            departmentId: departmentId,
                            userId1: $('#first_shift').val() ? $('#first_shift').val() : '',
                            userId2: $('#second_shift').val() ? $('#second_shift').val() : '',
                        }
                        actionShift(url, 'POST', datas)
                            .done(function (res) {
                                $('#third_shift option').remove()
                                $('#third_shift').append('<option>' + '' + '</option>')
                                $.each(res, function (index, value) {
                                    let isSelected = jQuery.inArray(index, oldUserId) != -1 ? 'selected' : ''
                                    $('#third_shift').append('<option value="' + index + '"' + isSelected + '>' + value + '</option>')
                                })
                            })
                            .fail(function (data) {
                                toastr.error(response.responseJSON.data[0], "{!! trans('system.have_an_error') !!}")
                            })
                    }
                }
            })
            .fail(function (response) {
                toastr.error(response.responseJSON.res[0], "{!! trans('system.have_an_error') !!}")
            })
    })
    $('body').on('click', '#update-shift', function () {
        reset()
        $('#cancel-shift').show()
        $('#save-shift').hide()
        $('#delete-all-shift').hide()
        $('#delete-one-shift').hide()
        $('#edit-shift').show()
        $('#update-shift').hide()
        $('input#start_date_shift').datepicker('setStartDate', new Date($('input#start_date_shift').datepicker('getDate')));
        $('input#start_date_shift').datepicker('setEndDate', new Date($('input#end_date_shift').datepicker('getDate')));
        $('input#end_date_shift').datepicker('setStartDate', new Date($('input#start_date_shift').datepicker('getDate')));
        $('input#end_date_shift').datepicker('setEndDate', new Date($('input#end_date_shift').datepicker('getDate')));
        $('#first_shift').change(setUserOption2)
        $('#second_shift').change(setUserOption3)
    })
    $('body').on('click', '#save-shift', function () {
        if (validateFormShift() !== false) {
            $.ajax({
                url: "{!! route('admin.calendar.checkingWorkingDay') !!}",
                data: $('#formShift').serialize(),
                type: 'POST',
                headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                success: function (data) {
                    $.ajax({
                        url: "{!! route('admin.calendar.storeShift') !!}",
                        data: $('#formShift').serialize(),
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
    $('body').on('click', '#edit-shift', function () {

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
            if (validateFormShift() !== false) {
                $.ajax({
                    url: url,
                    data: $('#formShift').serialize(),
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
            }
        })
    })
    $('body').on('click', '#delete-all-shift', function () {
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
                        data: $('#formShift').serialize(),
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
    $('body').on('click', '#delete-one-shift', function () {
        var date = $('input[name=day_click_shift]').val()
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
                        data: $('#formShift').serialize(),
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
    $('#formWorkShift').on('hidden.bs.modal', function () {
        $(this).find('form').trigger('reset');
        $('form').find('input[type=text], textarea').val(null)
        $('form').find('input[name=day_click_shift],input[name=idShift]').val(null)
        $('form').find('select').val(null).trigger('change.select2')
        reset()
    })

    function setUserOption2() {
        url = "{!! route('admin.calendar.firstShift') !!}"
        let datas = {
            departmentId: departmentId,
            userId: $('select#first_shift').val(),
        }
        actionShift(url, 'POST', datas)
            .done(function (res) {
                $('#second_shift option').remove()
                $('#second_shift').append('<option>' + '' + '</option>')
                $('#third_shift option').remove()
                $('#third_shift').append('<option>' + '' + '</option>')
                $.each(res, function (index, value) {
                    $('#second_shift').append('<option value="' + index + '">' + value + '</option>')
                })
            })
            .fail(function (response) {
                toastr.error(response.responseJSON.data[0], "{!! trans('system.have_an_error') !!}")
            })
    }

    function setUserOption3() {
        url = "{!! route('admin.calendar.secondShift') !!}"
        let datas = {
            departmentId: departmentId,
            userId1: $('select.first_shift').val() ? $('select.first_shift').val() : '',
            userId2: $('select.second_shift').val() ? $('select.second_shift').val() : '',
        }
        actionShift(url, 'POST', datas)
            .done(function (res) {
                $('#third_shift option').remove()
                $('#third_shift').append('<option>' + '' + '</option>')
                $.each(res, function (index, value) {
                    $('#third_shift').append('<option value="' + index + '">' + value + '</option>')
                })
            })
            .fail(function (response) {
                toastr.error(response.responseJSON.data[0], "{!! trans('system.have_an_error') !!}")

            })
    }

    function validateFormShift() {
        if ($('input#start_date_shift').val() == '') {
            toastr.error("{!! trans('shifts.error_start_date_required') !!}", "{!! trans('system.have_an_error') !!}")
            return false;
        } else if ($('input.start_date_shift').datepicker('getDate') < new Date()) {
            toastr.error("{!! trans('shifts.error_out_date') !!}", "{!! trans('system.have_an_error') !!}")
            return false;
        } else if ($('input#end_date_shift').val() == '') {
            toastr.error("{!! trans('shifts.error_end_date_required') !!}", "{!! trans('system.have_an_error') !!}")
            return false;
        } else if ($('select.first_shift').val() == '' && $('select.second_shift').val() == '' && $('select.third_shift').val() == '') {
            toastr.error("{!! trans('shifts.cannot_required_all') !!}", "{!! trans('system.have_an_error') !!}")
            return false;
        } else if ($('input.end_date_shift').datepicker('getDate') < $('input.start_date_shift').datepicker('getDate')) {
            toastr.error("{!! trans('shifts.error_end_date_bigger') !!}", "{!! trans('system.have_an_error') !!}")
            return false;
        }
        var firstShift = $('select.first_shift').val()
        var secondShift = $('select.second_shift').val()
        var thirdShift = $('select.third_shift').val()
        if (secondShift) {
            $.each(secondShift, function (index, value) {
                if (jQuery.inArray(value, firstShift) >= 0) {
                    toastr.error("{!! trans('shifts.must_one') !!}", "{!! trans('system.have_an_error') !!}")
                    return false;
                }
            })
        }
        if (thirdShift) {
            $.each(thirdShift, function (index, value) {
                if (jQuery.inArray(value, firstShift) >= 0) {
                    toastr.error("{!! trans('shifts.must_one') !!}", "{!! trans('system.have_an_error') !!}")
                    return false;
                } else if (jQuery.inArray(value, secondShift) >= 0) {
                    toastr.error("{!! trans('shifts.must_one') !!}", "{!! trans('system.have_an_error') !!}")
                    return false;
                }
            })
        }

        return true
    }

    function actionShift($url, $type, $params) {
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