{!! Form::open(['method' => 'POST', 'id' => 'formDayOff']) !!}
<span>{!! trans('calendar_departments.categories') !!}<span style="color: red">*</span></span>
{!! Form::select('categories', ['' => trans('system.dropdown_choice'),
                          'normal'=>trans('calendar_departments.normal'),
                          'holiday'=>trans('calendar_departments.holiday'),
                          ], old('categories'), ['class' => 'form-control select2 categories']) !!}
<br>
<br>
<span>{!! trans('calendar_departments.type') !!}<span style="color: red">*</span></span>
{!! Form::select('type', ['' => trans('system.dropdown_choice'),
                          'one'=>trans('calendar_departments.one'),
                          'multiple'=>trans('calendar_departments.multiple'),
                          'everyweek'=>trans('calendar_departments.everyweek')], old('type'), ['class' => 'form-control select2 type']) !!}
<br>
<br>
<span class="start_date">{!! trans('calendar_departments.start_date') !!}<span style="color: red">*</span></span>
<div class="row start_date">
    <div class="col-lg-8">
        <div class='input-group'>
            {!! Form::text('start_date', old('start_date'), ['class' => 'form-control datepicker start_date','id'=>'start_date' ,'placeholder'=>trans('shifts.start_date_placeholder'),'autocomplete'=>'off']) !!}
            <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                </span>
        </div>
    </div>
    <div class="col-lg-4">
        {!! Form::select('from_type', \App\Define\CalendarDepartments::getTimeForOptions(), old('from_type'), ['class' => 'form-control select2 from_type','id'=>'from_type']) !!}
    </div>
</div>
<br>
<span class="end_date">{!! trans('calendar_departments.end_date') !!}<span style="color: red">*</span></span>
<div class="row end_date">
    <div class="col-lg-8">
        <div class='input-group'>
            {!! Form::text('end_date', old('end_date'), ['class' => 'form-control datepicker end_date','id'=>'end_date' ,'placeholder'=>trans('shifts.end_date_placeholder'),'autocomplete'=>'off']) !!}
            <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                </span>
        </div>
    </div>
    <div class="col-lg-4">
        {!! Form::select('to_type', \App\Define\CalendarDepartments::getTimeForOptions(), old('to_type'), ['class' => 'form-control select2 to_type','id'=>'to_type']) !!}
    </div>
</div>
<br>
<span> {!! trans('calendar_departments.reason') !!}<span style="color: red">*</span></span>
{!! Form::textarea('reason', old('reason'), ['class' => 'form-control reason','rows'=>'5']) !!}
{{--<br>--}}
{{--@foreach($departmentGroups as $groupDepartment)--}}
{{--    @if($department->id == $groupDepartment->department_id )--}}
{{--        <label>--}}
{{--            {!! Form::checkbox('status', 1, old('status', 0), ['class' => 'minimal status_off']) !!}--}}
{{--            <span class="text-danger">{!! trans('calendar_departments.for_all') !!} {!! App\Models\DepartmentGroup::find($groupDepartment->group_id)->name !!}</span>--}}
{{--        </label>--}}
{{--    @endif--}}
{{--@endforeach--}}
{{--<br>--}}
<input type="hidden" name="department_id" value="{!! $department->id !!}">
<input type="hidden" name="day_click" value="">
<input type="hidden" name="id" value="">
<div class="modal-footer">
    <input type="button" id="delete" class="btn btn-danger btn-flat  delete-all"
           value="{!! trans('calendar_departments.delete-all') !!}">
    <input type="button" id="delete-one" class="btn btn-danger btn-flat  delete-one"
           value="{!! trans('calendar_departments.delete-one') !!}">
    {!! HTML::link(request()->fullUrl(), trans('system.action.cancel'), ['class' => 'btn btn-danger btn-flat cancel']) !!}
    <input type="button" id="submitForm" class="btn btn-primary btn-flat submit"
           value="{!! trans('system.action.save') !!}">
    <input type="button" class="btn btn-primary btn-flat update" id="updateFormOff"
           value="{!! trans('calendar_departments.update') !!}">
    <input type="button" id="editFormOff" class="btn btn-primary btn-flat edit "
           value="{!! trans('system.action.save') !!}">
</div>
{!! Form::close() !!}
<script>
    !function ($) {
        $(function () {
            $('.datepicker').datepicker({
                format: 'dd/mm/yyyy',
                useCurrent: false,
                autoclose: true,
                language: "vi",
                orientation: "bottom auto"
            })
            $(".select2").select2({
                width: '100%',
                placeholder: '{!! trans('system.dropdown_choice') !!} '
            });
            $('input[type=checkbox].minimal').iCheck({
                checkboxClass: 'icheckbox_minimal-red   '
            });
        });

    }(window.jQuery);
</script>
<script>
    /*
    * DayOff là class của 1 ngày trong lịch
    * id ở đây là id của ngày nghỉ trong db:
    * */
    $('.DayOff').click(function () {
        var id = $(this).find('span.id').text();
        $('input[name=id]').val(id)
        var date = $(this).attr('id')
        let dateArr = date.split('-')
        dateArr = new Date(dateArr.toString());
        $('input[name=day_click]').val(moment(dateArr).format('DD/MM/YYYY'))
        $('span#day-off').html(moment(dateArr).format('DD/MM/YYYY'))
        // if (new Date(date) < new Date()) {
        //     $("select").prop('disabled', true)
        //     $("input").prop('disabled', true)
        //     $("textarea").prop('disabled', true)
        // }
        $('#end_date').attr('readonly', 'readonly')
        $("select.from_type").attr('readonly', "readonly")
        $("select.to_type").attr('readonly', "readonly")

        if (id) {
            $('.delete-all').hide()
            $('.delete-one').hide()
            $('.edit').hide()
            $('.cancel').hide()
            $('.submit').hide()
            $('.update').hide()
            showLoading()
            $.ajax({
                url: '{!! route('admin.calendar.loadDataOneDay') !!}',
                type: 'GET',
                data: {
                    id: id,
                },
                headers: {
                    'X-CSRF-Token': "{!! csrf_token() !!}"
                },
                success: function (res) {
                    //console.log(res)
                    $(res).each(function (index, value) {
                        $('.update').show();
                        value.start_date == value.end_date ? $('.delete-all').show() : $('.delete-all').show() && $('.delete-one').show()
                        // new Date(value.start_date) < new Date() ? $('.delete-all').attr('disabled', 'disabled') : ''
                        value.categories == 'holiday' ? $('select.type').select2({width: '100%'}).find("option[value='everyweek']").prop('disabled', true) : $('select.type').select2({width: '100%'}).find("option[value='everyweek']").prop('disabled', false);
                        $('select.categories ').val(value.categories).change()
                        $('select.type').val(value.type).change()
                        $('#end_date').datepicker('setStartDate', new Date(moment(value.start_date).format('YYYY-MM-DD')));
                        $('#start_date').datepicker('setStartDate', new Date(moment(value.start_date).format('YYYY-MM-DD')));
                        $('#start_date').datepicker('setEndDate', new Date(moment(value.end_date).format('YYYY-MM-DD')));
                        $('#end_date').datepicker('setEndDate', new Date(moment(value.end_date).format('YYYY-MM-DD')));
                        $('input.start_date').datepicker('update', moment(value.start_date).format('DD/MM/YYYY'))
                        $('input.end_date').datepicker('update', moment(value.end_date).format('DD/MM/YYYY')).attr('readonly', 'readonly')
                        $('select.from_type').val(value.from_type).change().attr('readonly', 'readonly')
                        $('select.to_type').val(value.to_type).change().attr('readonly', 'readonly')
                        $('textarea.reason ').val(value.reason)
                        value.status == 1 ? $('input.status_off').iCheck('check') : $('input.status_off').iCheck('uncheck')

                    });
                    hideLoading()
                },
            })
            $("input[type=text]").attr('readonly', "readonly")
            $("select").attr('readonly', "readonly")
            $("textarea").prop('readonly', true)
        }
        else {
            $('.cancel').show()
            $('.submit').show()
            $('.delete-all').hide()
            $('.delete-one').hide()
            $('.edit').hide()
            $('.update').hide()
            $('select.categories').on('change', function () {
                var selected = $('select.categories').val()
                selected == 'holiday' ? $('select.type').select2({width: '100%'}).find("option[value='everyweek']").prop('disabled', true) : $('select.type').select2({width: '100%'}).find("option[value='everyweek']").prop('disabled', false);
            })
            $('input#start_date').datepicker('update', moment(dateArr).format('DD/MM/YYYY')).attr('readonly', 'readonly')
            $('select.type').on('change', function () {
                var selected = $('select.type').val()
                $("select.from_type").removeAttr('readonly')
                $("select.to_type").removeAttr('readonly')
                selected == 'one' ? $('#end_date').datepicker('update', moment($('input.start_date').datepicker('getDate')).format('DD/MM/YYYY')).attr('readonly', 'readonly') : '';
                selected == 'multiple' || selected == 'everyweek' ? $('#end_date').datepicker('setStartDate', $('input.start_date').datepicker('getDate')) && $('#end_date').removeAttr('readonly') && $('#end_date').val('') : '';

                if (selected == 'everyweek' || selected == 'one') {
                    if ($('select.from_type').find("option:selected").val() == 'AFTERNOON') {
                        $('select.to_type').val('AFTERNOON').change()
                        $('select.to_type').select2({width: '100%'}).find("option[value='MORNING']").prop('disabled', true);
                    } else {
                        $('select.to_type').select2({width: '100%'}).find("option[value='MORNING']").prop('disabled', false);
                    }
                    $('select.from_type').change(function () {
                        if ($('select.from_type').val() == 'AFTERNOON') {
                            $('select.to_type').val('AFTERNOON').change()
                            $('select.to_type').select2({width: '100%'}).find("option[value='MORNING']").prop('disabled', true);
                        } else {
                            $('select.to_type').select2({width: '100%'}).find("option[value='MORNING']").prop('disabled', false);
                        }
                    })
                } else {
                    $('select.to_type').select2({width: '100%'}).find("option[value='MORNING']").prop('disabled', false);
                }
            })
        }
    })

    $('body').on('click', '#submitForm', function (e) {
        if (validateFormOff() !== false) {
            showLoading()
            $.ajax({
                url: '{!! route('admin.calendar.checkIsDayOff') !!}',
                type: 'POST',
                data: $("#formDayOff").serialize(),
                headers: {
                    'X-CSRF-Token': "{!! csrf_token() !!}"
                },
                success: function (data) {
                    if (data.errors) {
                        toastr.error(data.errors, "{!! trans('system.have_an_error') !!}")
                        hideLoading()
                    }
                    if (data.success) {
                        $.ajax({
                            url: '{!! route('admin.calendar.store') !!}',
                            type: 'POST',
                            data: $("#formDayOff").serialize(),
                            headers: {
                                'X-CSRF-Token': "{!! csrf_token() !!}"
                            },
                            success: function (data) {
                                if (data.errors) {
                                    data.errors.type ? toastr.error(data.errors.type[0], "{!! trans('system.have_an_error') !!}") : ''
                                    data.errors.reason ? toastr.error(data.errors.reason[0], "{!! trans('system.have_an_error') !!}") : ''
                                    data.errors.end_date ? toastr.error(data.errors.end_date[0], "{!! trans('system.have_an_error') !!}") : ''
                                    hideLoading()
                                }
                                if (data.success) {
                                    window.location.reload()
                                }
                            },
                            error: function (err) {
                                let errorE = $.parseJSON(err.responseText);
                                toastr.warning(errorE.message)
                                hideLoading()
                            },
                        });
                    }
                }
            })
        }
    })
    $('body').on('click', '#editFormOff', function () {
        var id = $('input[name=id]').val();
        var date = moment($(this).attr('id')).format('DD/MM/YYYY')
        $('input[name=day_click]').val(date)


        swal({
            title: '{{ trans('calendar_departments.confirm_edit') }}',
            icon: "warning",
            closeOnClickOutside: false,
            buttons: {
                edit_all: {
                    text: '{!! trans('calendar_departments.edit_all') !!}',
                    value: 'edit_all',
                    className: "btn btn-info",
                },
                edit_one: {
                    text: "{!! trans('calendar_departments.edit_one') !!}",
                    value: 'edit_one',
                    className: "btn btn-info",
                },
                cancel: {
                    text: "{!! trans('calendar_departments.cancel') !!}",
                    value: null,
                    className: "btn",
                    visible: true,
                },
            },
        }).then((value) => {
            var url = '';
            switch (value) {
                case "edit_all":
                    url = '{{ url('admin/departments/calendar/updateAll') }}' + '/' + id;
                    break;
                case "edit_one":
                    url = '{{ url('admin/departments/calendar/update') }}' + '/' + id;
                    break;
            }
            if (validateFormOff() !== false) {
                showLoading()
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: $("#formDayOff").serialize(),
                    headers: {
                        'X-CSRF-Token': "{!! csrf_token() !!}"
                    },
                    success: function (data) {
                        if (data.errors) {
                            data.errors.type ? toastr.error(data.errors.type[0], "{!! trans('system.have_an_error') !!}") : ''
                            data.errors.reason ? toastr.error(data.errors.reason[0], "{!! trans('system.have_an_error') !!}") : ''
                            data.errors.end_date ? toastr.error(data.errors.end_date[0], "{!! trans('system.have_an_error') !!}") : ''
                            hideLoading()
                        }
                        if (data.success) {
                            window.location.reload()
                        }
                    },
                    error: function (err) {
                        let errorE = $.parseJSON(err.responseText);
                        toastr.warning(errorE.message)
                        hideLoading()
                    }
                }).always(function () {
                });
            }
        })

    })
    $('body').on('click', '#delete-one', function () {
        var id = $('input[name=id]').val();

        var date = $('input[name=day_click]').val()
        swal({
            title: '{{ trans('calendar_departments.confirm_delete') }}',
            text: '{{ trans('calendar_departments.title_delete_one1') }} ' + date + ' {{ trans('calendar_departments.title_delete_one2') }}',
            icon: "warning",
            closeOnClickOutside: false,
            buttons: {
                delete_one: {
                    text: '{!! trans('calendar_departments.delete_one_company') !!}',
                    value: 'delete_one',
                    className: "btn btn-danger",
                },
                delete_all: {
                    text: "{!! trans('calendar_departments.delete_all_company') !!}",
                    value: 'delete_all',
                    className: "btn btn-danger",
                },
                cancel: {
                    text: "{!! trans('calendar_departments.cancel') !!}",
                    value: null,
                    className: "btn",
                    visible: true,
                },
            },
        }).then((value) => {
            var url = '';
            switch (value) {
                case "delete_one":
                    url = '{{ url('admin/departments/calendar/delete-one')}}' + '/' + id;
                    break;
                case "delete_all":
                    url = '{{ url('admin/departments/calendar/delete-one-multi')}}' + '/' + id;
                    break;
            }
            $.ajax({
                url: url,
                type: 'POST',
                data: $("#formDayOff").serialize(),
                headers: {
                    'X-CSRF-Token': "{!! csrf_token() !!}"
                },
                success: function (data) {
                    if (data.errors) {
                        toastr.error(data.errors, "{!! trans('system.have_an_error') !!}")
                    }
                    if (data.success) {
                        window.location.reload()
                    }
                }
            });
        });
    })
    $('body').on('click', '#delete', function () {
        var id = $('input[name=id]').val();

        swal({
            title: '{{ trans('calendar_departments.confirm_delete') }}',
            text: '{{ trans('calendar_departments.title_delete_all') }}',
            icon: "warning",
            closeOnClickOutside: false,
            buttons: {
                delete_one: {
                    text: '{!! trans('calendar_departments.delete_one_company') !!}',
                    value: 'delete_one',
                    className: "btn btn-danger",
                },
                delete_all: {
                    text: "{!! trans('calendar_departments.delete_all_company') !!}",
                    value: 'delete_all',
                    className: "btn btn-danger",
                },
                cancel: {
                    text: "{!! trans('calendar_departments.cancel') !!}",
                    value: null,
                    className: "btn",
                    visible: true,
                },
            },
        }).then((value) => {
            var url = '';
            switch (value) {
                case "delete_one":
                    url = '{{ route('admin.calendar.delete') }}'
                    break;
                case "delete_all":
                    url = '{{ route('admin.calendar.deleteMulti') }}'
                    break;
            }
            $.ajax({
                url: url,
                type: 'POST',
                data: $("#formDayOff").serialize(),
                headers: {
                    'X-CSRF-Token': "{!! csrf_token() !!}"
                },
                success: function (data) {
                    if (data.errors) {
                        toastr.error(data.errors, "{!! trans('system.have_an_error') !!}")
                    }
                    if (data.success) {
                        window.location.reload()
                    }
                }
            });
        });
    });
    $('body').on('click', '#updateFormOff', function () {
        $('.cancel').show()
        $('.submit').hide()
        $('.delete-all').hide()
        $('.delete-one').hide()
        $('.edit').show()
        $('.update').hide()
        reset()

        $('select.categories').on('change', function () {
            var selected = $('select.categories').val()
            selected == 'holiday' ? $('select.type').select2({width: '100%'}).find("option[value='everyweek']").prop('disabled', true) : $('select.type').select2({width: '100%'}).find("option[value='everyweek']").prop('disabled', false);
        })
        $('select.type').on('change', function () {
            var selected = $('select.type').val()
            selected == 'one' ? $('#end_date').datepicker('update', moment($('input.start_date').datepicker('getDate')).format('DD/MM/YYYY')).attr('readonly', 'readonly') : '';
            selected == 'multiple' || selected == 'everyweek' ? $('#end_date').datepicker('setStartDate', $('input.start_date').datepicker('getDate')) && $('#end_date').removeAttr('readonly') && $('#end_date').val('') : '';

            if (selected == 'everyweek' || selected == 'one') {
                if ($('select.from_type').find("option:selected").val() == 'AFTERNOON') {
                    $('select.to_type').val('AFTERNOON').change()
                    $('select.to_type').select2({width: '100%'}).find("option[value='MORNING']").prop('disabled', true);
                } else {
                    $('select.to_type').select2({width: '100%'}).find("option[value='MORNING']").prop('disabled', false);
                }
                $('select.from_type').change(function () {
                    if ($('select.from_type').val() == 'AFTERNOON') {
                        $('select.to_type').val('AFTERNOON').change()
                        $('select.to_type').select2({width: '100%'}).find("option[value='MORNING']").prop('disabled', true);
                    } else {
                        $('select.to_type').select2({width: '100%'}).find("option[value='MORNING']").prop('disabled', false);
                    }
                })
            } else {
                $('select.to_type').select2({width: '100%'}).find("option[value='MORNING']").prop('disabled', false);
            }
        })
        $('input.start_date').on('change', function () {
            var selected = $('select.type').val()
            selected == 'one' ? $('#end_date').datepicker('update', moment($('input.start_date').datepicker('getDate')).format('DD/MM/YYYY')).attr('readonly', 'readonly') : '';
        })
    })
    $('#formOff').on('hidden.bs.modal', function () {
        $('#formOff').find("input[type=text], textarea ").val("");
        $('#formOff').find("input[type=text] ").datepicker('setStartDate','');
        $('#formOff').find("input[type=text] ").datepicker('setEndDate','');
        $('#formOff').find("input[type=checkbox] ").iCheck('uncheck');
        $('#formOff').find('select.from_type').prop('selectedIndex', 0).change();
        $('#formOff').find('select.to_type').prop('selectedIndex', 0).change();
        $('#formOff').find('select.categories').val('').change();
        $('#formOff').find('select.type').val('').change();
        $(this).find('#formOff').trigger('reset');
        reset()
    })

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

    function validateFormOff() {
        if ($('select.categories ').val() == '') {
            toastr.error("{!! trans('calendar_departments.error_categories') !!}", "{!! trans('system.have_an_error') !!}")
            return false;
        } else if ($('select.type ').val() == '') {
            toastr.error("{!! trans('calendar_departments.error-type') !!}", "{!! trans('system.have_an_error') !!}")
            return false;
        } else if ($('input.end_date').val() == '') {
            toastr.error("{!! trans('calendar_departments.error-end') !!}", "{!! trans('system.have_an_error') !!}")
            return false
        } 
        // else if ($('input.start_date').datepicker('getDate') < new Date()) {
        //     toastr.error("{!! trans('shifts.error_out_date') !!}", "{!! trans('system.have_an_error') !!}")
        //     return false;
        // } 
        
        else if ($('textarea.reason').val() == '') {
            toastr.error("{!! trans('calendar_departments.error-reason') !!}", "{!! trans('system.have_an_error') !!}")
            return false
        }
        if ($('select.type').val() == 'multiple' || $('select.type').val() == 'everyweek') {
            if ($('input.end_date').datepicker('getDate') <= $('input.start_date').datepicker('getDate')) {
                toastr.error("{!! trans('calendar_departments.error-end-date') !!}", "{!! trans('system.have_an_error') !!}")
                return false;
            }
        }

    }
</script>
