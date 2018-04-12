<?php
include 'config.php';
session_start();

$page_count = $GLOBALS['rank_page_count'];

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

// Get $user_count
$result = RunDB("SELECT COUNT(*) FROM `user`");
$user_count = mysql_fetch_row($result);
$user_count = (int) $user_count[0];

// Get $g_p as $_GET['p']
@$g_p = $_GET['p'];
@$g_p = (int) $g_p;
if (!isset($g_p) || !is_numeric($g_p) || !is_int((int) $g_p) || $g_p <= 0) $g_p = 1;
$page_total = $user_count / $page_count;
$page_total = (int) $page_total == $page_total ? (int) $page_total : (int) $page_total + 1;
if ($page_total <= 0) $page_total = 1;
if ($g_p > $page_total) $g_p = $page_total;
$page_offset = ($g_p - 1) * $page_count;

// Get $score_total
$result = RunDB("SELECT SUM(`score`) FROM `problem`");
$score_total = mysql_fetch_row($result);
$score_total = (int) $score_total[0];
if ($score_total < 0) $score_total = 0;

// Get $rank
$rank = array();
$result = RunDB("SELECT `user`.`id`, `user`.`username`, `rank`.`score` FROM `user` JOIN `rank` ON `user`.`id` = `rank`.`id` ORDER BY `rank`.`score` DESC LIMIT {$page_offset}, {$page_count}");
if (mysql_num_rows($result) > 0) {
    $i = 1;
    while ($row = mysql_fetch_assoc($result)) {
        $row['score'] = (int) $row['score'];
        if ($row['score'] == 0) $row['score'] = '';
        $row['rank'] = $page_offset + $i;
        array_push($rank, $row);
        $i++;
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
    <h1>排行榜</h1>
    <ol id="rank">
        <?php foreach ($rank as $row) { ?>
        <li>
            <span id="username"><?php echo $row['rank']; ?>.<div class="empty" style="width:10px;"></div>
            <a target="_blank" href="board.php?id=<?php echo $row['id']; ?>"><?php echo $row['username']; ?></a></span>
            <div class="progress" style="margin: 5px 5px;">
                <div class="progress-bar progress-bar-success" style="width:<?php echo @round($row['score'] * 100 / $score_total, 2); ?>%"><?php echo $row['score']; ?></div>
            </div>
        </li>
        <?php } ?>
    </ol>
    <?php if ($page_total > 1) { ?>
    <nav style="text-align: center">
    <ul id="pagination">
        <?php if ($g_p > 1) { ?>
        <li><a href="?p=<?php echo $g_p - 1; ?>">&laquo;</a></li>
        <?php } ?>
        <?php for ($page = 1; $page <= $page_total; $page++) { ?>
        <li<?php if ($page == $g_p) echo ' class="active"'; ?>><a href="?p=<?php echo $page; ?>"><?php echo $page; ?></a></li>
        <?php } ?>
        <?php if ($g_p < $page_total) { ?>
        <li><a href="?p=<?php echo $g_p + 1; ?>">&raquo;</a></li>
        <?php } ?>
    </ul>
    </nav>
    <?php } ?>
    <div class="empty" style="height:50px;"></div>
    </div>
</body>
</html>
