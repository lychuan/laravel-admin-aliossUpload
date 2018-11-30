<div class="{{$viewClass['form-group']}} {!! !$errors->has($errorKey) ? '' : 'has-error' !!}">
    <label for="{{$id}}" class="{{$viewClass['label']}} control-label">{{$label}}</label>
    <div class="{{$viewClass['field']}}">
        @include('admin::form.error')

        @if(strpos($attributes, 'images') === false)
            <div class="show_upload_pic_item">
                <img data-multi="" id="{{$id}}_alioss_upload" class="Js_alioss_btn" src="/vendor/laravel-admin-ext/alioss-upload/pic_add.png">
                <div class="operat_warp">
                    <input type="hidden" name="{{$id}}" value="">
                    <a href="" target="_blank">预览</a> / <a href="javascript:void(0);" onclick="alioss_del_file(this,0)">删除</a>
                </div>
                <div id="{{$id}}_container"></div>
            </div>
        @else
            <button data-multi="true" id="{{$id}}_alioss_upload" type="button" class="btn btn-success btn-flat Js_alioss_btn">选择文件</button>
            <div class="box box-widget">
                <!-- /.box-header -->
                <div id="{{$id}}_container" class="box-body show_upload_pic"></div>
                <!-- /.box-body -->
            </div>
        @endif
        @include('admin::form.help-block')
    </div>
</div>