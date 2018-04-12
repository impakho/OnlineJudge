<?php
include 'config.php';
session_start();

OpenDB();

// Check User Login
if (!isset($_SESSION['user'])) {
    $_SESSION['message'] = '请登录后再操作！';
    $_SESSION['message_tag'] = 'error';
    header('Location: login.php');
    die('Login First');
}else{
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

// Get $p_oldpassword as $_POST['oldpassword']
@$p_oldpassword = $_POST['oldpassword'];
@$p_oldpassword = (string) $p_oldpassword;
@$p_oldpassword = trim($p_oldpassword);

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
if (isset($p_oldpassword) && strlen($p_oldpassword) > 0 && isset($p_password) && strlen($p_password) > 0 && isset($p_repassword) && strlen($p_repassword) > 0 && isset($p_vcode) && strlen($p_vcode) > 0) {
    if (!isset($_SESSION['vcode']) || strtolower($p_vcode) !== $_SESSION['vcode']) {
        $_SESSION['message'] = '验证码错误！';
        $_SESSION['message_tag'] = 'error';
    }else if ($p_password !== $p_repassword) {
        $_SESSION['message'] = '两次新密码不一致！';
        $_SESSION['message_tag'] = 'error';
    }else if ($p_oldpassword === $p_password) {
        $_SESSION['message'] = '新密码与旧密码相同！';
        $_SESSION['message_tag'] = 'error';
    }else if (!preg_match('/^[\x20-\x7E]{1,32}$/', $p_password)) {
        $_SESSION['message'] = '新密码不符合格式！';
        $_SESSION['message_tag'] = 'error';
    }else if (!preg_match('/^[\x20-\x7E]{1,32}$/', $p_oldpassword)) {
        $_SESSION['message'] = '旧密码不正确！';
        $_SESSION['message_tag'] = 'error';
    }else{
        $p_oldpassword = md5($password_prefix.$p_oldpassword);
        $p_password = md5($password_prefix.$p_password);
        $p_oldpassword = mysql_real_escape_string($p_oldpassword);
        $p_password = mysql_real_escape_string($p_password);
        $result = RunDB("SELECT `password` FROM `user` WHERE `id` = {$_SESSION['user']}");
        $row = mysql_fetch_assoc($result);
        if ($row['password'] !== $p_oldpassword) {
            $_SESSION['message'] = '旧密码不正确！';
            $_SESSION['message_tag'] = 'error';
        }else{
            RunDB("UPDATE `user` SET `password` = '{$p_password}' WHERE `id` = {$_SESSION['user']}");
            $_SESSION['message'] = '密码修改成功！';
            $_SESSION['message_tag'] = 'success';
        }
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
    <h2>修改密码</h2>
    <form action="" method="post">
        <p><input type="password" name="oldpassword" maxlength="32" placeholder="旧密码" /></p>
        <p><input type="password" name="password" maxlength="32" placeholder="新密码" /></p>
        <p><input type="password" name="repassword" maxlength="32" placeholder="重复新密码" /></p>
        <p>
            <input type="text" name="vcode" maxlength="4" placeholder="验证码" />
            <img id="vcode" src="vcode.php" onclick="this.src='vcode.php?'+Math.random();">
        </p>
        <p><input type="submit" id="submit" name="submit" value="修改" /></p>
    </form>
    <div class="empty" style="height:50px;"></div>
    </div>
</body>
</html>
