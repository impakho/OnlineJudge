<?php
include 'config.php';
session_start();

$page_count = $GLOBALS['index_page_count'];

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

    // Get $user_score
    $result = RunDB("SELECT `score` FROM `rank` WHERE `id` = {$_SESSION['user']}");
    if (mysql_num_rows($result) <= 0) {
        RunDB("INSERT INTO `rank` (`id`, `score`) VALUES ({$_SESSION['user']}, 0)");
        $user_score = '0';
    }else{
        $row = mysql_fetch_assoc($result);
        $user_score = $row['score'];
    }
}

// Get $catalog
$catalog = array();
// All Type $catalog
$row = array();
$row['id'] = 0;
$row['name'] = '所有 All';
array_push($catalog, $row);
$result = RunDB("SELECT `id`, `name` FROM `catalog`");
if (mysql_num_rows($result) > 0) {
    while ($row = mysql_fetch_assoc($result)) {
        array_push($catalog, $row);
    }
}

// Get $g_id as $_GET['id']
@$g_id = $_GET['id'];
@$g_id = (int) $g_id;
if (!isset($g_id) || !is_numeric($g_id) || !is_int((int) $g_id) || $g_id < 0) $g_id = 0;

// Get $problem_count by $g_id
if ($g_id > 0) {
    $result = RunDB("SELECT COUNT(*) FROM `problem` WHERE `type` = {$g_id}");
}else{
    // Get All $problem_count
    $result = RunDB("SELECT COUNT(*) FROM `problem`");
}
$problem_count = mysql_fetch_row($result);
$problem_count = (int) $problem_count[0];

// Get $g_p as $_GET['p']
@$g_p = $_GET['p'];
@$g_p = (int) $g_p;
if (!isset($g_p) || !is_numeric($g_p) || !is_int((int) $g_p) || $g_p <= 0) $g_p = 1;
$page_total = $problem_count / $page_count;
$page_total = (int) $page_total == $page_total ? (int) $page_total : (int) $page_total + 1;
if ($page_total <= 0) $page_total = 1;
if ($g_p > $page_total) $g_p = $page_total;
$page_offset = ($g_p - 1) * $page_count;

// Get $problem by $g_id
$problem = array();
$result = RunDB("SELECT `id`, `title`, `score` FROM `problem` WHERE `type` = {$g_id} ORDER BY `time` DESC LIMIT {$page_offset}, {$page_count}");
if (mysql_num_rows($result) > 0) {
    while ($row = mysql_fetch_assoc($result)) {
        $result_1 = RunDB("SELECT COUNT(*) FROM `achieve` WHERE `problemid` = {$row['id']}");
        $row['solved'] = mysql_fetch_row($result_1);
        $row['solved'] = (int) $row['solved'][0];
        if (isset($_SESSION['user'])) {
            $result_2 = RunDB("SELECT COUNT(*) FROM `achieve` WHERE `userid` = {$_SESSION['user']} AND `problemid` = {$row['id']}");
            $row['achieve'] = mysql_fetch_row($result_2);
            $row['achieve'] = (int) $row['achieve'][0];
        }else{
            $row['achieve'] = 0;
        }
        array_push($problem, $row);
    }
}else{
    // Get All $problem
    $result = RunDB("SELECT `id`, `title`, `score` FROM `problem` ORDER BY `time` DESC LIMIT {$page_offset}, {$page_count}");
    while ($row = mysql_fetch_assoc($result)) {
        $result_1 = RunDB("SELECT COUNT(*) FROM `achieve` WHERE `problemid` = {$row['id']}");
        $row['solved'] = mysql_fetch_row($result_1);
        $row['solved'] = (int) $row['solved'][0];
        if (isset($_SESSION['user'])) {
            $result_2 = RunDB("SELECT COUNT(*) FROM `achieve` WHERE `userid` = {$_SESSION['user']} AND `problemid` = {$row['id']}");
            $row['achieve'] = mysql_fetch_row($result_2);
            $row['achieve'] = (int) $row['achieve'][0];
        }else{
            $row['achieve'] = 0;
        }
        array_push($problem, $row);
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
    <?php if (isset($_SESSION['user'])) { ?>
        <p><div class="msg-info">您目前得分：<?php echo $user_score; ?></div></p>
    <?php } ?>
    <p><?php echo $GLOBALS['announce_index']; ?></p>
    <?php foreach ($catalog as $row) { ?>
    <a id="tag" href="?id=<?php echo $row['id']; ?>"><?php echo $row['name']; ?></a>
    <?php } ?>
    <ul id="problems">
        <li class="heading">题目<span>完成人数</span><span>分数</span></li>
        <?php if (count($problem) == 0) { ?>
            <li>
                <div style="text-align:center;">暂无题目</div>
            </li>
        <?php } ?>
        <?php foreach ($problem as $row) { ?>
            <li class="problem-<?php if ($row['achieve'] == 0) echo 'un'; ?>solved">
                <a class="problem-title" href="problem.php?id=<?php echo $row['id']; ?>"><?php echo $row['title']; ?></a>
                <span class="problem-user-solved"><?php echo $row['solved']; ?></span>
                <span class="problem-score"><?php echo $row['score']; ?></span>
            </li>
        <?php } ?>
    </ul>
    <?php if ($page_total > 1) { ?>
    <nav style="text-align: center">
    <ul id="pagination">
        <?php if ($g_p > 1) { ?>
        <li><a href="?id=<?php echo $g_id; ?>&p=<?php echo $g_p - 1; ?>">&laquo;</a></li>
        <?php } ?>
        <?php for ($page = 1; $page <= $page_total; $page++) { ?>
        <li<?php if ($page == $g_p) echo ' class="active"'; ?>><a href="?id=<?php echo $g_id; ?>&p=<?php echo $page; ?>"><?php echo $page; ?></a></li>
        <?php } ?>
        <?php if ($g_p < $page_total) { ?>
        <li><a href="?id=<?php echo $g_id; ?>&p=<?php echo $g_p + 1; ?>">&raquo;</a></li>
        <?php } ?>
    </ul>
    </nav>
    <?php } ?>
    <div id="footer"><?php echo $GLOBALS['footer']; ?></div>
    <div class="empty" style="height:50px;"></div>
    </div>
</body>
</html>
