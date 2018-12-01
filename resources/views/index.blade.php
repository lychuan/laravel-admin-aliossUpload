<div class="{{$viewClass['form-group']}} {!! !$errors->has($errorKey) ? '' : 'has-error' !!}">
    <label for="{{$id}}" class="{{$viewClass['label']}} control-label">{{$label}}</label>
    <div class="{{$viewClass['field']}}">
        @include('admin::form.error')
        @if(strpos($attributes, 'images') === false)
            <?php
                $all_path =  old($id, $value) ? config('admin.extensions.alioss-upload.OSS_URL') . '/' . old($id, $value) : '';
                $resize_path = $all_path ? $all_path . '?x-oss-process=image/resize,m_fill,h_100,w_100' : '';
            ?>
            <div class="show_upload_pic_item">
                <img data-multi="" id="{{$id}}_alioss_upload" class="Js_alioss_btn" src="{{$resize_path ? $resize_path : '/vendor/laravel-admin-ext/alioss-upload/pic_add.png'}}">
                <div class="operat_warp" @if(old($id, $value)) style="display: inline-block;" @endif>
                    <input type="hidden" name="{{$id}}" value="{{old($id, $value)}}">
                    <a href="{{$all_path}}" target="_blank">预览</a> /
                    @if(old($id, $value))
                        <a href="javascript:void(0);"  data-filename="{{old($id, $value)}}" onclick="alioss_del_file(this,0,true)">删除</a>
                    @else
                        <a href="javascript:void(0);"  data-filename="" onclick="alioss_del_file(this,0)">删除</a>
                    @endif
                </div>
                <div id="{{$id}}_container"></div>
            </div>
        @else
            <button data-multi="true" id="{{$id}}_alioss_upload" type="button" class="btn btn-success btn-flat Js_alioss_btn">选择文件</button>
            <div class="box box-widget">
                <!-- /.box-header -->
                <div id="{{$id}}_container" class="box-body show_upload_pic">
                    @if(old($id, $value))
                    @foreach(explode(',', old($id, $value)) as $p)
                        <?php
                            $all_path =  config('admin.extensions.alioss-upload.OSS_URL') . '/' . $p;
                            $resize_path = $all_path . '?x-oss-process=image/resize,m_fill,h_100,w_100';
                        ?>
                        <div class="show_upload_pic_item">
                            <img src="{{$resize_path}}" style="margin-bottom: 3px">
                            <div class="operat_warp" style="display: inline-block">
                                <input type="hidden" name="{{$id}}[]" value="{{$p}}">
                                <a href="{{$all_path}}" target="_blank">预览</a> / <a href="javascript:void(0);" onclick="alioss_del_file(this,1,true)" data-filename="{{$p}}">删除</a>
                            </div>
                        </div>
                        @endforeach
                    @endif
                </div>
                <!-- /.box-body -->
            </div>
        @endif
        @include('admin::form.help-block')
    </div>
</div>