<?php

namespace Encore\AliOssUpload\Http\Controllers;
use OSS\OssClient; //阿里云OSS SDK
use OSS\Core\OssException;
use Illuminate\Routing\Controller;

class AliOssUploadController extends Controller
{
    // 获取alioss参数
    public function index() {
        $config = config('admin.extensions.alioss-upload');
        $id= $config['OSS_ACCESS_ID'];
        $key= $config['OSS_ACCESS_KEY'];
        $host = $config['OSS_HOST'];
        $now = time();
        $expire = 180; //设置该policy超时时间，秒
        $end = $now + $expire;
        $expiration = $this->gmt_iso8601( $end );

        //前缀目录
        $dir = $uploadDir = "files/". date( "Ym" )."/".date( "d" )."/" ; //'user-dir/';

        //最大文件大小 20M
        $condition = array( 0=>'content-length-range', 1=>0, 2=>20480000 );
        $conditions[] = $condition;

        //表示用户上传的数据,必须是以$dir开始,不然上传会失败,这一步不是必须项,只是为了安全起见,防止用户通过policy上传到别人的目录
        $start = array( 0=>'starts-with', 1=>'$key', 2=>$dir );
        $conditions[] = $start;

        //根据自己的逻辑,设定expire 时间,让前端定时取signature
        $arr = array( 'expiration'=>$expiration, 'conditions'=>$conditions );
        $policy = json_encode( $arr );
        $base64_policy = base64_encode( $policy );
        $string_to_sign = $base64_policy;
        $signature = base64_encode( hash_hmac( 'sha1', $string_to_sign, $key, true ) );

        $response = array();
        $response['accessid'] = $id;
        $response['host'] = $host;
        $response['policy'] = $base64_policy;
        $response['signature'] = $signature;
        $response['expire'] = $end;
        //这个参数是设置用户上传指定的前缀
        $response['dir'] = $dir;
        $response['oss_url'] = $config['OSS_URL'];//仅用于方便前端组合图片路径
        return $response;
    }

    // 删除文件
    public function delete(){
        $path = request('path');
        //oss
        $config = config('admin.extensions.alioss-upload');

        $accessKeyId = $config['OSS_ACCESS_ID'];
        $accessKeySecret = $config['OSS_ACCESS_KEY'];
        $endpoint = $config['OSS_ENDPOINT'];
        $bucket = $config['OSS_BUCKET'];
        //连接oss
        try {
            $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
            $ossClient->deleteObject($bucket, $path);//删除单个文件
        } catch (OssException $e) {
            return json_encode(["data"=>"OssException: ".$e->getMessage()]);
        }
        return json_encode(["data"=>'ok']);
    }


    /**
     * gmt时间格式转换
     */
    public function gmt_iso8601( $time ) {
        $dtStr = date( "c", $time );
        $mydatetime = new \DateTime( $dtStr );
        $expiration = $mydatetime->format( \DateTime::ISO8601 );
        $pos = strpos( $expiration, '+' );
        $expiration = substr( $expiration, 0, $pos );
        return $expiration."Z";
    }

}