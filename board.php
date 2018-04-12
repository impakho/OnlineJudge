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

// Get $user
$user = array();
$result = RunDB("SELECT `user`.`username`, `user`.`logtime`, `user`.`regtime`, `rank`.`score` FROM `user` JOIN `rank` ON `user`.`id` = `rank`.`id` AND `user`.`id` = {$g_id}");
if (mysql_num_rows($result) > 0) {
    $user = mysql_fetch_assoc($result);
}else{
    header('Location: ./');
    die('Invalid Board User');
}

// Get $catalog
$catalog = array();
$result = RunDB("SELECT `id`, `name` FROM `catalog`");
if (mysql_num_rows($result) > 0) {
    while ($row = mysql_fetch_assoc($result)) {
        $result_1 = RunDB("SELECT SUM(`score`) FROM `problem` WHERE `type` = {$row['id']}");
        $result_2 = RunDB("SELECT SUM(`problem`.`score`) FROM `achieve` JOIN `problem` ON `achieve`.`problemid` = `problem`.`id` AND `achieve`.`userid` = {$g_id} AND `problem`.`type` = {$row['id']}");
        $catalog_total_score = mysql_fetch_row($result_1);
        $catalog_total_score = (int) $catalog_total_score[0];
        if ($catalog_total_score < 0) $catalog_total_score = 0;
        $catalog_score = mysql_fetch_row($result_2);
        $catalog_score = (int) $catalog_score[0];
        if ($catalog_score < 0) $catalog_score = 0;
        $row['total_score'] = $catalog_total_score;
        $row['score'] = $catalog_score;
        array_push($catalog, $row);
    }
}

// Get $board
$board = array();
$result = RunDB("SELECT `achieve`.`problemid`, `achieve`.`time`, `problem`.`type`, `problem`.`title`, `problem`.`score` FROM `achieve` JOIN `problem` ON `achieve`.`problemid` = `problem`.`id` AND `achieve`.`userid` = {$g_id} ORDER BY time DESC");
if (mysql_num_rows($result) > 0) {
    while ($row = mysql_fetch_assoc($result)) {
        array_push($board, $row);
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
    <link rel="stylesheet" type="text/css" href="static/hint.min.css" />
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
    <h1><?php echo $user['username']; ?></h1>
    <h3>得分：<?php echo $user['score']; ?><div class="empty" style="width:30px;"></div>解答数：<?php echo count($board); ?></h3>
    <h3>注册时间：<?php echo format_date_utc($user['regtime']); ?><div class="empty" style="width:30px;"></div>
    上次登录时间：<?php echo format_date_time_utc($user['logtime']); ?>（<?php echo format_date($user['logtime']); ?>）</h3>
    <ol id="board">
        <?php foreach ($catalog as $row) { ?>
        <li>
            <span id="catalog"><?php echo $row['name']; ?>: <?php echo $row['score']; ?>/<?php echo $row['total_score']; ?> (<?php echo @round($row['score'] * 100 / $row['total_score'], 2); ?>%)</span>
            <div class="progress progress-striped" style="margin: 5px 5px;">
                <div class="progress-bar color-<?php echo $row['id'] % 10; ?>" style="width:<?php echo round($row['score'] * 100 / $row['total_score'], 2); ?>%"></div>
            </div>
        </li>
        <?php } ?>
    </ol>
    <h3>已解答题目：</h3>
    <?php if (count($board) > 0) { ?>
    <div id="solved">
        <?php foreach ($board as $row) { ?>
        <a target="_blank" href="problem.php?id=<?php echo $row['problemid']; ?>">
        <div class="hint--bottom" aria-label="<?php echo format_date_time_utc($row['time']).'（'.format_date($row['time']).'）'; ?>"><div class="out-box color-<?php echo $row['type'] % 10; ?>">
        <div class="left-box"><?php echo $row['score']; ?></div><span class="right-box"><?php echo $row['title']; ?></span>
        </div></div>
        </a>
        <?php } ?>
    </div>
    <?php } ?>
    <div class="empty" style="height:50px;"></div>
    </div>
</body>
</html>
