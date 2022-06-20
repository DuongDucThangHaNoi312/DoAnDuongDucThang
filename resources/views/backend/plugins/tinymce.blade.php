<script src="{!! asset('assets/backend/plugins/tinymce/tinymce.min.js') !!}" type="text/javascript"></script>
<script>
    !function ($) {
        $(function(){
            function elFinderBrowser (field_name, url, type, win) {
                tinymce.activeEditor.windowManager.open(
                    {
                        file: '{!! route('elfinder.tinymce4') !!}',
                        title: "BCTech.,JSC's File Management",
                        width: 900,
                        height: 450,
                        resizable: 'yes',
                        commandsOptions : {
                             getfile: {
                                multiple: true
                             }
                        }
                    },
                    {
                        setUrl: function (url) {
                            win.document.getElementById(field_name).value = url;
                        }
                    },
                );
                return false;
            }
            tinymce.init({
                selector:'textarea.tinymce',
                plugins: 'code autolink directionality visualblocks visualchars fullscreen image link media table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists textcolor colorpicker textpattern paste', //imagetools wordcount
                toolbar: 'code formatselect | bold italic underline forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist outdent indent | link image | numlist  | removeformat | fullscreen',
                image_advtab: true,
                template_cdate_format: '[CDATE: %d/%m/%Y : %H:%M:%S]',
                template_mdate_format: '[MDATE: %d/%m/%Y : %H:%M:%S]',
                image_caption: true,
                file_browser_callback : elFinderBrowser,
                forced_root_block : "",
                force_br_newlines : true,
                force_p_newlines : false,
                language: "vi_VN",
                relative_urls : false,
                remove_script_host : false,
                convert_urls : true,
                rel_list: [
                    {title: 'follow', value: 'follow'},
                    {title: 'nofollow', value: 'nofollow'}
                ],
                paste_data_images: true,
                images_upload_handler: function (blobInfo, success, failure) {
                    var myfile = new FormData();
                    myfile.append('image', blobInfo.blob());
                    box1 = new ajaxLoader('body', {classOveride: 'blue-loader', bgColor: '#000', opacity: '0.3', type_image: 'ajax_loader_circle'});
                    $.ajax({
                        url: "{!! route('admin.images.upload') !!}",
                        data: myfile,
                        type: 'POST',
                        contentType: false,
                        processData: false,
                        headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                        success: function(res) {
                            toastr.success(res.message, '{!! trans('system.success') !!}');
                            console.log(res.url);
                            success(res.url);
                        },
                        error: function(obj, status, err) {
                            var error = $.parseJSON(obj.responseText);
                            toastr.error(error.message, '{!! trans('system.have_an_error') !!}');
                            failure(error.message, {remove: true});
                        }
                    }).always(function() {
                        if(box1) box1.remove();
                    });
                },
            });
        });
}(window.jQuery);
</script>
