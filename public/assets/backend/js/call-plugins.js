function callInputMaskDecimal() {
    $(".currency").inputmask({
        'alias': 'decimal',
        'groupSeparator': ',',
        'autoGroup': true,
        'min': 0,
        'max': 999999999.99,
        'digits': 0,
        'removeMaskOnSubmit': true
    });
}

function callInputMaskInteger() {
    $(".currency").inputmask('currency', {
        'alias': 'integer',
        'groupSeparator': ',',
        'autoGroup': true,
        'digits': 0,
        'min': 0,
        'max': 999999999999,
        'removeMaskOnSubmit': true
    });
}

function callInputMask(options = {}) {
    let $element = options.element || ".currency", $digit = options.digit || 0, $separator = options.separator || ",", $allowMinus = options.allowMinus || false;
    $($element).inputmask('currency', {
        'alias': 'integer',
        'radixPoint': ($separator == "." ? "," : "."),
        'rightAlign': true,
        'groupSeparator': $separator,
        'autoGroup': true,
        'min': ($allowMinus ? -9999999999999 : 0),
        'digits': $digit,
        'allowMinus': $allowMinus,
        'max': 9999999999999,
        'removeMaskOnSubmit': true,
        // autoUnmask: true
    });
}

function callDatePicker() {
    $('.datepicker').datepicker({
        format: 'dd/mm/yyyy',
        autoclose: true,
        todayHighlight: true,
        language: "vi",
    });
}

function callDatePickerDown() {
    $('.datepicker').datepicker({
        format: 'dd/mm/yyyy',
        autoclose: true,
        todayHighlight: true,
        language: "vi",
        orientation: "bottom auto"
    });
}

function callSelect2() {
    $(".select2").select2({
        width: '100%',
    });
}

function callSelect2AutoWidth(el) {
    $el = !el ? $(".select2") : $(el)
    $el.select2({
        width: '100%',
        dropdownAutoWidth : true
    });
}

function callICheck() {
    $('input[type="checkbox"].minimal').iCheck({
        checkboxClass: 'icheckbox_minimal-blue'
    });
}

function callDateRangePicker($tagClass) {
    let months = moment.months(),
        dayOffWeeks =  ["CN", "T2", "T3", "T4", "T5", "T6", "T7"]
   $tagClass.daterangepicker({
        autoUpdateInput: false,
        opens: 'left',
        "locale": {
            "format": "DD/MM/YYYY",
            "separator": " - ",
            "applyLabel": "Áp dụng",
            "cancelLabel": "Huỷ bỏ",
            "fromLabel": "Từ ngày",
            "toLabel": "Tới ngày",
            "customRangeLabel": "Tuỳ chọn",
            "weekLabel": "W",
            "daysOfWeek": dayOffWeeks,
            "monthNames": months,
            "firstDay": 1
        },
        ranges: {
            'Tháng này': [moment().startOf('month'), moment()],
            'Tháng trước': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
            'Năm này': [moment().startOf('year'), moment()],
            'Năm trước': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')],
        },
        "alwaysShowCalendars": true,
        maxDate: moment(),
       // startDate: moment().startOf('year'),
    }, function(start, end, label) {
        $tagClass.val(start.format('DD/MM/YYYY') + " - " + end.format('DD/MM/YYYY'));
    });
}

function formatInputMask0Digits(number) {
    return Number(number).toFixed().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
}

function formatInputMaskDigits(number, digit) {
    if (!digit) return formatInputMask0Digits(number)
    return Number(number).toFixed(digit).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
}