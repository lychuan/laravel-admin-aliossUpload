<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>文件上传</title>
    <link rel="stylesheet" href="/vendor/laravel-admin/AdminLTE/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="/vendor/laravel-admin/AdminLTE/dist/css/AdminLTE.min.css">
    <link rel="stylesheet" href="/vendor/laravel-admin-ext/alioss-upload/style.css">
</head>
<body>
    <?php $id = request('element');?>
    <div class="box box-widget">
        <div class="box-header">
            <button data-multi="true" id="{{$id}}_alioss_upload" type="button" class="btn btn-success btn-flat Js_alioss_btn">选择文件</button>
        </div>
                <!-- /.box-header -->
        <div id="{{$id}}_container" class="box-body show_upload_pic" style="min-height: 180px">
            
        </div>
        <div class="box-footer">
            <div style="float: right;">
                <button type="button" class="btn btn-success btn-flat Js_set_html_ckeditor">确定</button>
                <button type="button" class="btn btn-danger btn-flat Js_close">取消</button>
            </div>
        </div>
        <!-- /.box-body -->
    </div>
</body>
</html>
<script src="/vendor/laravel-admin/AdminLTE/plugins/jQuery/jQuery-2.1.4.min.js"></script>
<script src="/vendor/laravel-admin-ext/alioss-upload/plupload/plupload.full.min.js"></script>
<script src="/vendor/laravel-admin-ext/alioss-upload/plupload/i18n/zh_CN.js"></script>
<script src="/vendor/laravel-admin-ext/alioss-upload/Sortable.min.js"></script>
<script src="/vendor/laravel-admin-ext/alioss-upload/alioss.js"></script>
<script>
    (function(){
        // 初始化上传
        alioss_upload('{{$id}}','{{csrf_token()}}');

        $(".Js_set_html_ckeditor").click(function(){
            var html='';
            $("#{{$id}}_container").find('.show_upload_pic_item').each(function(){
                var file_path = $(this).find('input').val();
                if(file_path) {
                    html += '<img src="{{config("admin.extensions.alioss-upload.OSS_URL")}}/'+file_path+'" />'
                }
            });
            parent.setHtml_ckeditor(html, '{{$id}}');
            parent.layer.closeAll();//关闭
        });

        $('.Js_close').click(function(){parent.layer.closeAll()});
    })();
</script>