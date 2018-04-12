<?php
include 'config.php';
session_start();

OpenDB();

// Check User Login
if (isset($_SESSION['user'])) {
    // Get $user_name
    $result = RunDB("SELECT `username` FROM `user` WHERE `id` = {$_SESSION['user']}");
    if (mysql_num_rows($result) <= 0) {
        header('Location: logout.php');
        die('Invalid User');
    }else{
        $row = mysql_fetch_assoc($result);
        $user_name = $row['username'];
    }
}

// Get $g_id as $_GET['id']
@$g_id = $_GET['id'];
@$g_id = (int) $g_id;
if (!isset($g_id) || !is_numeric($g_id) || !is_int((int) $g_id) || $g_id < 0) $g_id = 0;

// Get $problem by $g_id
$result = RunDB("SELECT * FROM `problem` WHERE `id` = {$g_id}");
if (mysql_num_rows($result) <= 0) {
    header('Location: ./');
    die('Invalid Problem');
}else{
    $row = mysql_fetch_assoc($result);
    $problem = $row;
    $problem['content'] = str_replace('SITE_DOMAIN', strtolower($GLOBALS['domain']), $problem['content']);
    if (strlen($problem['url']) <= 0) $problem['url'] = '#';
    $problem['url'] = str_replace('SITE_DOMAIN', strtolower($GLOBALS['domain']), $problem['url']);
    $problem['flag'] = (string) $problem['flag'];
    $problem['flag'] = trim($problem['flag']);
    $result = RunDB("SELECT `name` FROM `catalog` WHERE `id` = {$row['type']}");
    if (mysql_num_rows($result) <= 0) {
        $problem['catalog'] = '未分类';
    }else{
        $problem['catalog'] = mysql_fetch_row($result);
        $problem['catalog'] = $problem['catalog'][0];
    }
    if (isset($_SESSION['user'])) {
        $result = RunDB("SELECT COUNT(*) FROM `achieve` WHERE `userid` = {$_SESSION['user']} AND `problemid` = {$g_id}");
        $problem['achieve'] = mysql_fetch_row($result);
        $problem['achieve'] = (int) $problem['achieve'][0];
    }else{
        $problem['achieve'] = 0;
    }
}

// Get $p_flag as $_POST['flag']
@$p_flag = $_POST['flag'];
@$p_flag = (string) $p_flag;
@$p_flag = trim($p_flag);

// Check Flag
if (isset($p_flag) && strlen($p_flag) > 0 && $problem['achieve'] == 0) {
    if (isset($_SESSION['user'])) {
        if ($p_flag === $problem['flag'] && strlen($problem['flag']) > 0) {
            RunDB("UPDATE `rank` SET `score` = `score` + {$problem['score']} WHERE `id` = {$_SESSION['user']}");
            $local_time = time();
            RunDB("INSERT INTO `achieve` (`userid`, `problemid`, `time`) VALUES ({$_SESSION['user']}, {$g_id}, {$local_time})");
            $problem['achieve'] = 1;
            $_SESSION['message'] = '回答正确！';
            $_SESSION['message_tag'] = 'success';
        }else{
            $_SESSION['message'] = '回答错误！';
            $_SESSION['message_tag'] = 'error';
        }
    }else{
        $_SESSION['message'] = '请登录后再操作！';
        $_SESSION['message_tag'] = 'error';
    }
}

CloseDB();
?>
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?php echo $GLOBALS['site_name']; ?></title>
    <link rel="stylesheet" type="text/css" href="static/style.min.css" />
</head>
<body>
    <div id="header">
        <header>
            <a id="title" href="./"><?php echo $GLOBALS['site_name']; ?></a>
            <?php if (isset($_SESSION['user'])) { ?>
                <a id="logout" href="logout.php">注销 <?php echo $user_name; ?></a>
                <a id="ucenter" href="ucenter.php">用户中心</a>
            <?php }else{ ?>
                <a id="register" href="register.php">注册</a>
                <a id="login" href="login.php">登录</a>
            <?php } ?>
        </header>
    </div>
    <?php if (isset($_SESSION['message'])) { ?>
        <div class="msg-<?php echo $_SESSION['message_tag']; ?>"><?php echo $_SESSION['message']; ?></div>
    <?php unset($_SESSION['message']); unset($_SESSION['message_tag']); } ?>
    <div id="container">
    <h1><?php echo $problem['title']; ?></h1>
    <h4>类型：<?php echo $problem['catalog']; ?><div class="empty" style="width:50px;"></div>分数：<?php echo $problem['score']; ?></h4>
    <div style="margin-bottom:30px;"><?php echo $problem['content']; ?></div>
    <form action="" method="post">
        <?php if ($problem['url'] != '#') { ?>
        <a id="link" target="_blank" href="<?php echo $problem['url']; ?>">打开题目</a>
        <?php } ?>
        <?php if ($problem['achieve'] == 0) { ?>
        <input type="text" autocomplete="off" name="flag" placeholder="flag{...}" />
        <input type="submit" id="submit" name="submit" value="提交" />
        <?php }else{ ?>
        <input type="text" autocomplete="off" name="flag" value="<?php echo $problem['flag']; ?>" />
        <span style="color:green;margin-left:5px;">你已经回答过了！</span>
        <?php } ?>
    </form>
    </nav>
    <div class="empty" style="height:50px;"></div>
    </div>
</body>
</html>
