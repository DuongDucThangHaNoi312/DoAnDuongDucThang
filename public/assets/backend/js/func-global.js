function setDepartment($routeSetDept, csrfToken, $currentRoute, departmentOld = null) {
    let companyId = $('.companySelect').val();
    let tagDept = $('.departmentSelect')
    if (companyId) {
        tagDept.attr('disabled', false)
        $.ajax({
            url: routeSetDept,
            data: {companyId: companyId, route: $currentRoute},
            type: 'POST',
            headers: {'X-CSRF-Token': csrfToken},
            success: function (res) {
                $('.departmentSelect option').remove()
                tagDept.append('<option value="">' + 'Chọn phòng ban' + '</option>')
                $.each(res, function (index, value) {
                    let isSelected = departmentOld == index ? 'selected' : ''
                    tagDept.append('<option value="' + index + '"' + isSelected + '>' + value + '</option>')
                })
            },
            error: function (err) {
                let error = $.parseJSON(err.responseText);
                toastr.warning(error.message, "{!! trans('system.have_error') !!}")
            }
        })
    } else {
        $('.departmentSelect option').remove()
        tagDept.append('<option value="">' + 'Chon phòng ban' + '</option>')
        tagDept.attr('disabled', true)
    }
}