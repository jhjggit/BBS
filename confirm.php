<?php
include_once "inc/config.php";
include_once "inc/mysql_tools.php";
include_once "inc/tools.inc.php";

    //如果用户是直接跳转到该页面,则GET必定为空
    if (!(isset($_GET['confirm_info']) && isset($_GET['jump_url']) && isset($_GET['return_url']) && isset($_GET['data']))){
        exit("非法的请求! 请返回!");
    }
    $url = $_GET['return_url'];

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8" />
    <title>确认退出 <?php echo $_GET['data']?> 用户?</title>
    <meta name="keywords" content="确认页面" />
    <meta name="description" content="确认页面" />
    <link rel="stylesheet" type="text/css" href="/style/remind.css" />
</head>
<body>
<div class="notice">
    <br>
    <?php echo $_GET['confirm_info']?>
    <br>
    <a style="color: red" href="<?php echo $_GET['jump_url'] . "&return_url=" .$url?>">确定</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a style="color: green" href="<?php echo $_GET['return_url']?>">取消</a>
</div>
</body>
</html>