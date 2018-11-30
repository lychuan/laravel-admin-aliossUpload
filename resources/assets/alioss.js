(function(){
    // 多图可拖动排序
    var els = document.querySelectorAll('.show_upload_pic');
    for(var i = 0, len = els.length; i < len; i++) {
        Sortable.create(els[i],{
            animation: 150,
            group: {
                pull: false,
                put: false,
            },
            handle: 'img'
        });
    }
})();

// 文件上传
(function(){
    //------------ 阿里云OSS start ------------
    var accessid = '', host = '', policyBase64 = '', signature = '', key = '', expire = 0, filename_new = '', file_ext = '', oss_url= '';

    //获取签名函数
    function get_signature(data_time,data_sig) {
        var now_timestamp = Date.parse(new Date()) / 1000;
        if (expire < now_timestamp + 180) {//180s缓冲
            //ajax
            $.ajax({
                url:'/admin/alioss-upload_policy',
                data:{ date: data_time, sig: data_sig },
                type:'get',
                dataType:'json',
                async: false,//同步
                success:function(obj) {
                    accessid = obj['accessid'];
                    host = obj['host'];
                    oss_url = obj['oss_url'];
                    policyBase64 = obj['policy'];
                    signature = obj['signature'];
                    expire = parseInt(obj['expire']);
                    key = obj['dir'];
                },
                error:function() {
                    alert("抱歉！获取签名错误！");
                }
            });
        }
    }
    //重设plupload参数
    function set_upload_param(up, filename) {
        get_signature();//获取签名
        var new_multipart_params = {
            'key': key + filename,//+ '${filename}',
            'policy': policyBase64,
            'OSSAccessKeyId': accessid,
            'success_action_status': '200',//让服务端返回200, 默认204
            'signature': signature,
        };
        up.setOption({
            'url': host,
            'multipart_params': new_multipart_params
        });
    }
    //指定长度的随机字符串
    function random_string(len) {
        len = len || 32;
        var chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        var maxPos = chars.length;
        var pwd = '';
        for (i = 0; i < len; i++) {
            pwd += chars.charAt(Math.floor(Math.random() * maxPos));
        }
        return pwd;
    }
    //获取文件的后缀名
    function get_suffix(filename) {
        var pos = filename.lastIndexOf('.');
        var suffix = '';
        if (pos !== -1) {
            suffix = filename.substring(pos)
        }
        return suffix.toLowerCase();
    }

    // 初始化上传
    function init_upload(id, csrf_token){
        var browse_button = $('#'+id+'_alioss_upload');
        var multi = Boolean(browse_button.attr('data-multi'));
        var container = document.getElementById(id + '_container');
        var uploader = new plupload.Uploader({
            runtimes : 'html5,flash,silverlight,html4',
            browse_button : browse_button.attr('id'),//'pickfiles',
            container: container,
            url : '',
            flash_swf_url : '/vendor/laravel-admin-ext/alioss-upload/plupload/Moxie.swf',
            silverlight_xap_url : '/vendor/laravel-admin-ext/alioss-upload/plupload/Moxie.xap',
            multi_selection: multi,//false单选，true多选
            multipart_params: { '_token' : csrf_token },
            //过滤
            filters : {
                max_file_size : '10mb',
                mime_types: [
                    {title : "Image files", extensions : "jpg,jpeg,gif,png"}
                ]
            },
            init: {
                FilesAdded: function(up, files) {
                    uploader.start();//选择文件后立即上传
                },
                BeforeUpload: function(up, file) {
                    file_ext = get_suffix(file.name); //后缀名
                    filename_new = Date.parse(new Date()) / 1000 + '_' + random_string(10) + file_ext;
                    set_upload_param(up, filename_new); //重设参数
                },
                UploadProgress: function(up, file) {
                    //$('#'+file.id).find('span').html(file.percent+'%');
                },
                FileUploaded: function(up, file, info) {
                    var path = key+filename_new;
                    var src = oss_url + '/' + path +'?x-oss-process=image/resize,m_fill,h_100,w_100';
                    if(multi) {
                        $('#' + id + '_container').append([
                            '<div class="show_upload_pic_item">',
                            '<img src="'+src+'" style="margin-bottom: 3px">',
                            '<div class="operat_warp" style="display: inline-block">',
                            '<input type="hidden" name="'+id+'[]" value="'+path+'" />',
                            '<a href="'+oss_url + '/' + path+'" target="_blank">预览</a> / ',
                            '<a href="javascript:void(0);" onclick="alioss_del_file(this,1)" data-filename="'+path+'">删除</a>',
                            '</div></div>'
                        ].join(''));
                    }else{
                        browse_button.attr('src',src);
                        var operat_warp = browse_button.parents('.show_upload_pic_item').find('.operat_warp');
                        var a_s = operat_warp.find('a');
                        a_s.eq(0).attr('href',oss_url + '/' + path);
                        a_s.eq(1).attr('data-filename',path);
                        operat_warp.show().find('input').val(path);
                    }
                },
                UploadComplete: function(up, files) {

                },
                Error: function(up, err) {
                    alert("抱歉！出错了：" + err.message);
                }
            }
        });
        //初始化上传
        uploader.init();
    }
    window.alioss_upload = init_upload;


    // 删除文件
    window.alioss_del_file = function(obj,type) {
        var path = $(obj).attr('data-filename');
        if(type){ // 多图
            $(obj).parents('.show_upload_pic_item').remove();
        }else{
            // 单图
            var show_upload_pic_item = $(obj).attr('data-filename','').parents('.show_upload_pic_item');
            show_upload_pic_item.find('img.Js_alioss_btn').attr('src','/vendor/laravel-admin-ext/alioss-upload/pic_add.png');
            show_upload_pic_item.find('.operat_warp').hide();
        }
        $.get('/admin/alioss-upload_del/?path='+path,function(re){
            console.log(re);
        },'json');
    };
})();