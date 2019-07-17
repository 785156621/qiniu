<?php
//视频水印回调方法1获取方式
$result = file_get_contents('php://input');
$result = json_decode($result,1);

//视频水印回调方法2获取方式
//$json_ret = base64_decode($_GET['upload_ret']);
//$result = json_decode($json_ret,1);
error_log("\n当前打印时间".date('Ymd H:i:s')."打印内容：".print_r($result,1),3,'gd.txt');
echo json_encode(array('ret' => 'success'));
