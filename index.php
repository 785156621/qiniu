<?php
require 'vendor/autoload.php';

use Qiniu\Storage\UploadManager;
use Qiniu\Auth;


demo1();
/**
 * 视频水印方法1
 * 上传策略
 * $pipeline 创建私有队列 https://developer.qiniu.com/dora/kb/2500/streaming-media-queue-about-seven-cows
 * persistentPipeline 指定私有队列 进行数据处理， 避免在公有队列排队等候较长时间，提升效率；
 */
function demo1()
{
//    $res = file_get_contents('http://api.qiniu.com/status/get/prefop?id=z0.5d2da1b238b9f31ea6a6bfeb');
//    $res = json_decode($res,1);
//    var_dump($res);die;

//    print_r($_FILES);die;


    $accessKey = 'q-b0wVILLYvgp1Xxd6GjD3bGhezHqdgvxhi2vIic';
    $secretKey = 'IQzKDpmBctthROET8thNUR0s6SSSx2Jtr5DZANb4';
    $bucket = 'test';


// 构建鉴权对象
    $auth = new Auth($accessKey, $secretKey);

//上传视频，上传完成后进行m3u8的转码， 并给视频打水印
    $wmImg = Qiniu\base64_urlSafeEncode('http://pupunqnie.bkt.clouddn.com/shuiyin.png');
//$pfop = "avthumb/m3u8/noDomain/1/wmImage/$wmImg";


// 文字水印
    $name = Qiniu\base64_urlSafeEncode('文字水印');
// 这里是随机生成的Key
    $key = date("YmdHis").mt_rand(0,1000);
// 这里是加过水印的视频名称
    $video_name = Qiniu\base64_urlSafeEncode($bucket.':new_'.$key);

    // 访问接口。拼接图片水印地址。后面是接口地址的各种参数和值wmFontColor/颜色/wmFontSize/文字大小/wmGravityText/显示位置|saveas/加过水印的视屏名称
    $pfop = "avthumb/mp4/wmImage/".$wmImg."/wmText/".$name."/wmFontColor/cmVk/wmFontSize/30/wmGravityText/North|saveas/".$video_name;

    $pipeline = 'test-pipeline';
    $policy = array(
        'persistentOps' => $pfop,
        'persistentNotifyUrl' => 'http://qgkzp2.natappfree.cc/notify.php',
        'persistentPipeline' => $pipeline
    );

    $token = $auth->uploadToken($bucket, null, 3600, $policy);

// // 要上传文件的本地路径
    $filePath = './shipin.mp4';
    $filePath = $_FILES['file']['tmp_name'];
// // 上传到七牛后保存的文件名
// $key = 'shipin.mp4';
// // 初始化 UploadManager 对象并进行文件的上传。
    $uploadMgr = new UploadManager();
    // 调用 UploadManager 的 putFile 方法进行文件的上传。
    list($ret, $err) = $uploadMgr->putFile($token, null, $filePath);
    echo "\n====> putFile result: \n";
    if ($err !== null) {
        var_dump($err);
    } else {
        var_dump($ret);
    }

    $res = file_get_contents('http://api.qiniu.com/status/get/prefop?id='.$ret['persistentId']);
    $res = json_decode($res,1);
    var_dump($res);
}


/**
 * 视频水印方法2
 * 前端上传
 * 返回token key
 * returnUrl 回调获取图片地址
 * 参考链接：https://blog.csdn.net/baidu_37895884/article/details/83271831
 */
function demo2()
{
    $accessKey = 'q-b0wVILLYvgp1Xxd6GjD3bGhezHqdgvxhi2vIic';
    $secretKey = 'IQzKDpmBctthROET8thNUR0s6SSSx2Jtr5DZANb4';
    $bucket = 'test';
    // $upManager = new UploadManager();
// $auth = new Auth($accessKey, $secretKey);
// $token = $auth->uploadToken($bucket);
// list($ret, $error) = $upManager->put($token, 'formput', 'hello world');
// print_r($ret);
// print_r($error);die;

// 引入上传类
// use Qiniu\Storage\UploadManager;
// 需要填写你的 Access Key 和 Secret Key

// 构建鉴权对象
    $auth = new Auth($accessKey, $secretKey);
// 生成上传 Token
// $token = $auth->uploadToken($bucket);
// // 要上传文件的本地路径
    $filePath = './shipin.mp4';
// // 上传到七牛后保存的文件名
// $key = 'shipin.mp4';
// // 初始化 UploadManager 对象并进行文件的上传。
    $uploadMgr = new UploadManager();
// // 调用 UploadManager 的 putFile 方法进行文件的上传。
// list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
// echo "\n====> putFile result: \n";
// if ($err !== null) {
//     var_dump($err);
// } else {
//     var_dump($ret);
// }

    $pipeline = 'sdktest';

//上传视频，上传完成后进行m3u8的转码， 并给视频打水印
    $wmImg = Qiniu\base64_urlSafeEncode('http://pupunqnie.bkt.clouddn.com/shuiyin.png');
//$pfop = "avthumb/m3u8/noDomain/1/wmImage/$wmImg";


// 文字水印
    $name = Qiniu\base64_urlSafeEncode('文字水印');
// 这里是随机生成的Key
    $key = date("YmdHis").mt_rand(0,10000000);
// 这里是加过水印的视频名称
    $video_name = Qiniu\base64_urlSafeEncode($bucket.':new_'.$key);

    // 访问接口。拼接图片水印地址。后面是接口地址的各种参数和值wmFontColor/颜色/wmFontSize/文字大小/wmGravityText/显示位置|saveas/加过水印的视屏名称
    $pfop = "avthumb/mp4/wmImage/".$wmImg."/wmText/".$name."/wmFontColor/cmVk/wmFontSize/30/wmGravityText/North|saveas/".$video_name;


//转码完成后回调到业务服务器。（公网可以访问，并相应200 OK）
    $notifyUrl = 'http://127.0.0.1/qiniu/';
//独立的转码队列：https://portal.qiniu.com/mps/pipeline
// $policy = array(
//     'persistentOps' => $pfop,
//     'persistentNotifyUrl' => $notifyUrl,
//     // 'persistentPipeline' => $pipeline
// );
    $policy = array(
        // 回调地址
        'returnUrl' => 'http://127.0.0.1/qiniu/notify',
        'persistentOps' => $pfop,
    );

    $token = $auth->uploadToken($bucket, null, 3600, $policy);
    print_r(['token' => $token,'key' => $key]);
//list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
//echo "\n====> putFile result: \n";
//if ($err !== null) {
//    var_dump($err);
//} else {
//    var_dump($ret);
//}
}

