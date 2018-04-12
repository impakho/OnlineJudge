<?php
// Website Setting
error_reporting(0);
$GLOBALS['db_addr'] = 'localhost';
$GLOBALS['db_user'] = 'onlinejudge';
$GLOBALS['db_pass'] = '123456';
$GLOBALS['db_name'] = 'onlinejudge';
$GLOBALS['site_name'] = 'XXXXX 安全平台';
$GLOBALS['domain'] = 'OnlineJudge.Org';
$GLOBALS['password_prefix'] = 'oj_';
$GLOBALS['announce_index'] = '<h1>OJ实训平台<div style="float:right;"><a href="rank.php">排行榜</a></div></h1>公告：2018/01/01 平台测试版上线<br />如无特殊说明，答案格式：flag{xxx}';
$GLOBALS['announce_login'] = '登录后可以答题~';
$GLOBALS['announce_register'] = '同学们，用户名只允许 A-Z / a-z / 0-9 / _ / - 。<br /><br />不要使用常用密码注册！';
$GLOBALS['footer'] = '
    <p><a target="_blank" href="http://www.cert.org.cn/">国家互联网应急中心</a></p>
    <p><a target="_blank" href="http://www.cnnic.net.cn/">中国互联网络信息中心</a></p>
    <p><a target="_blank" href="http://www.miitbeian.gov.cn/">工业和信息化部</a></p>
';
$GLOBALS['index_page_count'] = 15;
$GLOBALS['rank_page_count'] = 15;

// Pre-defined Function
$GLOBALS["sql_con"]=NULL;

function OpenDB(){
    @$GLOBALS['sql_con']=mysql_connect($GLOBALS['db_addr'],$GLOBALS['db_user'],$GLOBALS['db_pass']);
    if (!$GLOBALS['sql_con']) die("Could not connect: ".mysql_error());
    mysql_select_db($GLOBALS['db_name'],$GLOBALS['sql_con']);
}

function RunDB($sql_query){
    mysql_query("SET NAMES 'utf8'");
    $result=mysql_query($sql_query);
    return $result;
}

function CloseDB(){
    mysql_close($GLOBALS['sql_con']);
}

function format_date($time){
    if (!is_numeric($time)){
        if (strpos($time,"-")===false) return '未知';
        $time=strtotime($time);
    }
    $t=time()-$time;
    $f=array(
        '31536000'=>'年',
        '2592000'=>'个月',
        '604800'=>'星期',
        '86400'=>'天',
        '3600'=>'小时',
        '60'=>'分钟',
        '1'=>'秒'
    );
    foreach ($f as $k=>$v){
        if (0 !=$c=floor($t/(int)$k)) {
            return $c.$v.'前';
        }
    }
}

function format_date_utc($time){
    if (!is_numeric($time)){
        if (strpos($time,"-")===false) return '未知';
        $time=strtotime($time);
    }
    return date('Y-m-d',$time);
}

function format_date_time_utc($time){
    if (!is_numeric($time)){
        if (strpos($time,"-")===false) return '未知';
        $time=strtotime($time);
    }
    return date('Y-m-d H:i:s',$time);
}
?>
