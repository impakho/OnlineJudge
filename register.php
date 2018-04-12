<?php
include 'config.php';
session_start();

// Check User Login
if (isset($_SESSION['user'])) {
    header('Location: ./');
    die('Login Redirect');
}

// Get $p_username as $_POST['username']
@$p_username = $_POST['username'];
@$p_username = (string) $p_username;
@$p_username = trim($p_username);

// Get $p_password as $_POST['password']
@$p_password = $_POST['password'];
@$p_password = (string) $p_password;
@$p_password = trim($p_password);

// Get $p_repassword as $_POST['repassword']
@$p_repassword = $_POST['repassword'];
@$p_repassword = (string) $p_repassword;
@$p_repassword = trim($p_repassword);

// Get $p_vcode as $_POST['vcode']
@$p_vcode = $_POST['vcode'];
@$p_vcode = (string) $p_vcode;
@$p_vcode = trim($p_vcode);

// Check Login Form
if (isset($p_username) && strlen($p_username) > 0 && isset($p_password) && strlen($p_password) > 0 && isset($p_repassword) && strlen($p_repassword) > 0 && isset($p_vcode) && strlen($p_vcode) > 0) {
    if (!isset($_SESSION['vcode']) || strtolower($p_vcode) !== $_SESSION['vcode']) {
        $_SESSION['message'] = '验证码错误！';
        $_SESSION['message_tag'] = 'error';
    }else if ($p_password !== $p_repassword) {
        $_SESSION['message'] = '两次密码不一致！';
        $_SESSION['message_tag'] = 'error';
    }else if (!preg_match('/^[-_A-Za-z0-9]{1,16}$/', $p_username)) {
        $_SESSION['message'] = '用户名不符合格式！';
        $_SESSION['message_tag'] = 'error';
    }else if (!preg_match('/^[\x20-\x7E]{1,32}$/', $p_password)) {
        $_SESSION['message'] = '密码不符合格式！';
        $_SESSION['message_tag'] = 'error';
    }else{
        OpenDB();
        $p_password = md5($password_prefix.$p_password);
        $p_username = mysql_real_escape_string($p_username);
        $p_password = mysql_real_escape_string($p_password);
        $local_time = time();
        $result = RunDB("SELECT COUNT(*) FROM `user` WHERE `username` = '{$p_username}'");
        $result_1 = mysql_fetch_row($result);
        if ((int) $result_1[0] > 0) {
            $_SESSION['message'] = '用户名已被注册！';
            $_SESSION['message_tag'] = 'error';
        }else{
            RunDB("INSERT INTO `user` (`username`, `password`, `regtime`, `logtime`) VALUES ('{$p_username}', '{$p_password}', {$local_time}, {$local_time})");
            $insert_id = mysql_insert_id();
            if ($insert_id <= 0) {
                $_SESSION['message'] = '数据库错误！';
                $_SESSION['message_tag'] = 'fail';
            }else{
                RunDB("INSERT INTO `rank` (`id`, `score`) VALUES ({$insert_id}, 0)");
                $_SESSION['message'] = '注册成功！';
                $_SESSION['message_tag'] = 'success';
                header('Location: login.php');
                die('Register Success');
            }
        }
        CloseDB();
    }
}
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
    <div class="msg-info"><?php echo $GLOBALS['announce_register']; ?></div>
    <form action="" method="post">
        <p><input type="text" name="username" maxlength="16" placeholder="用户名" /></p>
        <p><input type="password" name="password" maxlength="32" placeholder="密码" /></p>
        <p><input type="password" name="repassword" maxlength="32" placeholder="重复密码" /></p>
        <p>
            <input type="text" name="vcode" maxlength="4" placeholder="验证码" />
            <img id="vcode" src="vcode.php" onclick="this.src='vcode.php?'+Math.random();">
        </p>
        <p><input type="submit" id="submit" name="submit" value="注册" /></p>
    </form>
    <div class="empty" style="height:50px;"></div>
    </div>
</body>
</html>
