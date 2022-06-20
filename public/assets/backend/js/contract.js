function setDateFromTo(six, one, three, unlimited) {
    let startFrom = $('.datepicker-from').val()
    let type = $('.type').val()
    let new_date = '';
    if (startFrom) {
        if (type == six) {
            new_date = moment(startFrom, "DD-MM-YYYY").add(6, 'M').subtract(1, 'days').format('DD-MM-YYYY')
        } else if (type == one) {
            new_date = moment(startFrom, "DD-MM-YYYY").add(1, 'Y').subtract(1, 'days').format('DD-MM-YYYY');
        } else if (type == three) {
            new_date = moment(startFrom, "DD-MM-YYYY").add(3, 'Y').subtract(1, 'days').format('DD-MM-YYYY');
        } else if (type == unlimited) {
            new_date = ''
            $('.datepicker-to').attr('disabled', true)
        }
    }
    $('.datepicker-to').datepicker('update', new_date)
}

function setDisabledTypeFromIsMain(isMain, partTime) {
    if (isMain == partTime) {
        $('.type').val('').change()
        $('.type').attr('disabled', true)
        $('.datepicker-to').attr('disabled', false)
    } else {
        $('.type').attr('disabled', false)
        $('.datepicker-to').val('')
        $('.datepicker-to').attr('disabled', true)
    }
}

function inputMarkSalary() {
    $("input[name='basic_salary']").inputmask({
        'alias': 'decimal',
        'groupSeparator': ',',
        'autoGroup': true,
        'min': 0,
        'max': 999999999.99,
        'digits': 0,
        'removeMaskOnSubmit': true
    });
}

function setDepartmentOption(url, oldDepartmentId, csrf, title, $currentRoute = null) {
    let companyId = $('.companySelect').val();
    if (companyId) {
        $('#departmentSelect').attr('disabled', false)
        $.ajax({
            url: url,
            data: {companyId: companyId, route: $currentRoute},
            type: 'POST',
            headers: csrf,
            success: function (res) {
                $('#departmentSelect option').remove()
                $('#departmentSelect').append('<option value="">' + 'Chọn 1 mục' + '</option>')
                $.each(res, function (index, value) {
                    let isSelected = oldDepartmentId == index ? 'selected' : ''
                    $('#departmentSelect').append('<option value="' + index + '"' + isSelected + '>' + value + '</option>')
                })
            },
            error: function (err) {
                let error = $.parseJSON(err.responseText);
                toastr.warning(error.message)
            }
        })
        $('#department-tooltip').attr('title', '').tooltip('show')

    } else {
        $('#departmentSelect').val('').change()
        $('#departmentSelect').attr('disabled', true)
        $('#department-tooltip').attr('title', title).tooltip('show')
    }
}

function setCodeContract(users, company, isMain, partTime, official, userFromCreatStaff) {
    let isMainType = isMain == partTime ? 'HDTV' : (isMain == official ? 'HDLD' : '')
    let userSelected = $('#userSelect').val()
    let validFrom = $('.datepicker-from').val()
    if (company && company.id && userSelected && users && isMainType && validFrom) {
        let d1 = moment(validFrom, 'DD/MM/YYYY')
        let codeU = sessionStorage.getItem('codeUser')
        let validFromF = `${d1.format('DD')}${d1.format('MM')}${d1.format('YY')}`
        let code = `${validFromF}-${codeU}-${company.text}/${isMainType}`
        $('input[name="code"]').val(code)
    } else if (!users && userFromCreatStaff && isMainType && validFrom) {
        let codeUser = userFromCreatStaff[2]
        let staffS = userFromCreatStaff[3]
        let code = `${staffS}-${codeUser}-${company.text}/${isMainType}`
        $('input[name="code"]').val(code)
    } else if (!users && !userFromCreatStaff && isMainType && validFrom && userSelected) {
        let codeU = sessionStorage.getItem('codeUser')
        let d1 = moment(validFrom, 'DD/MM/YYYY')
        let validFromF = `${d1.format('DD')}${d1.format('MM')}${d1.format('YY')}`
        let code = `${validFromF}-${codeU}-${company.text}/${isMainType}`
        $('input[name="code"]').val(code)
    } else {
        $('input[name="code"]').val('')
    }
}

function showSelectCurrencyByDept(urlCheckMultiCurrency) {
    let deptId = $('#departmentSelect').val()
    if (deptId) {
        $.ajax({
            url: urlCheckMultiCurrency,
            data: {deptId: deptId},
            type: 'POST',
            headers: {'X-CSRF-Token': csrfGlobal},
            success: function (res) {
                if (res.is_multi_currency) {
                    $('.div_select_currency').show()
                } else {
                    $('.div_select_currency').hide()
                    $('.currency_code').val(_VND_CODE).change()
                }
                callSelect2()
                callInputMask({digit: getCurrencyDigit($('.currency_code').val())})
            },
            error: function (err) {
                let error = $.parseJSON(err.responseText);
                toastr.warning(error.message)
            }
        })
    } else $('.div_select_currency').hide()
}

function getCurrencyDigit(_VND) {
    return _VND != _VND_CODE ? 2 : 0;
}
callInputMask({digit: getCurrencyDigit($('.currency_code').val())})
function formatAmountByCurrency() {
    $('.currency_code').change(function () {
        let currencyCode = this.value
        callInputMask({digit: getCurrencyDigit(currencyCode)})
        console.log('a', $('.total-allowance-cost').text().replace(/,/g, ""))
        $('.total-allowance-cost').html(formatInputMaskDigits($('.total-allowance-cost').text().replace(/,/g, ""), getCurrencyDigit(currencyCode)))
        $('.total-amount').html( formatInputMaskDigits($('.total-amount').text().replace(/,/g, ""), getCurrencyDigit(currencyCode)) )
    })
}
formatAmountByCurrency()
