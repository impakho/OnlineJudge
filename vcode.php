<?php
error_reporting(0);

session_start();

$imgwidth = 120; // 图片宽度
$imgheight = 50; // 图片高度
$codelen = 4; // 验证码长度
$fontsize = 20; // 字体大小
$charset = 'abcdefghkmnprstuvwxyzABCDEFGHKMNPRSTUVWXYZ23456789';
$font = 'static/segoescb.ttf';

$im = imagecreatetruecolor($imgwidth, $imgheight);

$while = imageColorAllocate($im, 255, 255, 255);
imagefill($im, 0, 0, $while); // 填充图像

// 取得字符串
$authstr = '';
$_len = strlen($charset) - 1;
for ($i = 0; $i < $codelen; $i++) {
    $authstr .= $charset[mt_rand(0, $_len)];
}

$_SESSION['vcode'] = strtolower($authstr); // 整个转为小写，主要是为了不区分大小写

// 随机画点，已经改为划星星了
for ($i = 0; $i < $imgwidth; $i++) {
    $randcolor = imageColorallocate($im, mt_rand(200, 255), mt_rand(200, 255), mt_rand(200, 255));
    imagestring($im, mt_rand(1,5), mt_rand(0, $imgwidth), mt_rand(0, $imgheight), '*', $randcolor);
    // imagesetpixel($im, mt_rand(0, $imgwidth), mt_rand(0, $imgheight), $randcolor);
}
// 随机画线,线条数量=字符数量（随便）
for ($i = 0; $i < $codelen; $i++) { 
    $randcolor = imagecolorallocate($im, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
    imageline($im, 0, mt_rand(0, $imgheight), $imgwidth, mt_rand(0, $imgheight), $randcolor);
}

$_x = intval($imgwidth / $codelen); // 计算字符距离
$_y = intval($imgheight * 0.7); // 字符显示在图片70%的位置
for ($i = 0; $i < strlen($authstr); $i++) {
    $randcolor = imagecolorallocate($im, mt_rand(0, 150), mt_rand(0, 150), mt_rand(0, 150));
    // imagestring($im,5,$j,5,$imgstr[$i],$color3);
    imagettftext($im, $fontsize, mt_rand(-30, 30), $i * $_x + 3, $_y, $randcolor, $font, $authstr[$i]);
}

// 生成图像
header('content-type:image/PNG');
imagePNG($im);
imageDestroy($im);
?>