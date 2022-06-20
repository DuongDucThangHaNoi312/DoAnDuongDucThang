$(document).ready(function () {
    $(document).on('click', '.show-log', function (e) {
        let modelName = $(".model_log").val(),
            lang = $(".lang").val(),
            objectId = $(this).data('id');
        showLoading()
        $.ajax({
            url: _URL_LOG,
            type: 'GET',
            headers: {
                'X-CSRF-Token': csrfGlobal
            },
            dataType: "json",
            data: {
                modelName: modelName,
                id: objectId,
                lang: lang,
            },
            success: function(res) {
                $('#show-log-detail').modal('show');
                $('#show-log-detail').html(res.data);
            },
            error: function(err) {
                let error = $.parseJSON(err.responseText);
                toastr.warning(error.message);
            },
        }).always(function() {
            hideLoading()
        });
    });
});