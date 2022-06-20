{!! Form::open(['method' => 'POST', 'id' => 'formCopy']) !!}
<div class="col-md-12">
    <label>{!! trans('calendar_departments.company') !!} :</label>
</div>
<div class="col-md-12">
    <div class="form-group">
        {!! Form::select('company_id', ['' => trans('system.dropdown_choice')] + \App\Helpers\GetOption::getCompaniesForOption(),old('company_id'), ['class' => 'form-control select2 companySelect', 'required']) !!}
    </div>
</div>
<div class="col-md-12">
    <label>{!! trans('calendar_departments.department') !!} :</label>
</div>
<div class="col-md-12">
    <div class="form-group">
        {!! Form::select('department_id', ['' => trans('system.dropdown_choice')], old('department_id'), ['disabled' => true, 'id' => 'departmentSelect', 'class' => 'form-control select2', 'required']) !!}
    </div>
</div>

<input type="hidden" name="department_id_current" value="{!! $department->id !!}">
<input type="hidden" name="year" value="{!! $year !!}">
<div class="modal-footer">
    @if(\App\Models\Department::getDeptOffGroup($department->id))
    <span style="float: left">
        <input type="checkbox" class="minimal" name="copy_group" id="" value="1" >
        <label>Sao chép sang phòng ban cùng nhóm.</label>
    </span>
    @endif
    <button type="button" class="btn btn-default " id="cancel-shift"
            data-dismiss="modal">{!! trans('system.action.cancel') !!}</button>
    <button type="button" class="btn btn-primary "
            id="copy">{!! trans('calendar_departments.action') !!}</button>
</div>
{!! Form::close() !!}
<script>
    function setDepartmentOption() {
        let companyId = $('.companySelect'). val();
        if (companyId) {
            $('#departmentSelect').attr('disabled', false)
            $.ajax({
                url: "{!! route('admin.contracts.setDepartmentOption') !!}",
                data: {companyId: companyId},
                type: 'POST',
                headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                success: function (res) {
                    console.log(res)
                    $('#departmentSelect option').remove()
                    $('#departmentSelect').append('<option value="">'+ '{!! trans('system.dropdown_choice') !!}'  + '</option>')
                    $.each(res, function (index, value) {
                        if (index == departmentId) return
                        $('#departmentSelect').append('<option value="' + index + '">' + value + '</option>')
                    })
                },
                error: function (err) {
                    let error = $.parseJSON(err.responseText);
                    toastr.warning(error.message, "{!! trans('system.have_error') !!}")
                }
            })
        }
    }
    $(document).on('change', '.companySelect', setDepartmentOption)

    $('body').on('click','#copy',function () {
        if ($('.companySelect').val() && !$('#departmentSelect').val()) {
            toastr.error("{!! trans('calendar_departments.dept_required_from_company') !!}", "{!! trans('system.have_an_error') !!}")
            return false;
        } else{
            var departmentName =  $('#departmentSelect').find(":selected").text();
            swal({
                title: '{{ trans('calendar_departments.confirm_copy') }}',
                text: '{{ trans('calendar_departments.copy_text1') }} ' + 'đích' +' {{ trans('calendar_departments.copy_text2') }} \n' +'Hãy đảm bảo lịch không bị trùng nhau',
                icon: "warning",
                closeOnClickOutside: false,
                buttons: {
                    copy: {
                        text: '{!! trans('calendar_departments.access_copy') !!}',
                        value: 'copy',
                        className: "btn btn-danger",
                    },
                    cancel: {
                        text: "{!! trans('shifts.cancel') !!}",
                        value: null,
                        className: "btn",
                        visible: true,
                    },
                },
            }).then((value) => {
                if (value == 'copy') {
                    $.ajax({
                        url: "{!! route('admin.calendar.copy') !!}",
                        data: $('#formCopy').serialize(),
                        type: 'POST',
                        headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                        success: function (res) {
                            toastr.info(res.message, "{!! trans('system.info') !!}")
                            window.location.reload()
                        },
                        error: function (err) {
                            let error = $.parseJSON(err.responseText);
                            toastr.error(error.message, "{!! trans('system.have_error') !!}")
                        }
                    })
                }
            })
        }
    })
    $('#formCopys').on('hidden.bs.modal', function () {
        $(this).find('form').trigger('reset');
        $('form').find('select').val(null).trigger('change.select2')
        $('#departmentSelect').attr('disabled','disabled')
    })
</script>
