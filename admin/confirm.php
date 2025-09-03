<?php
    //用来确定是否删除某个条目
    include_once "../inc/config.php";
    //如果用户是直接跳转到该页面,则GET必定为空

    if (!(isset($_GET['del_info']) && isset($_GET['url']) && isset($_GET['return_url']))){
        exit("非法的请求! 请返回!");
    }
    $url = "{$_GET['return_url']}";
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8" />
    <title>后台界面</title>
    <meta name="keywords" content="确认页面" />
    <meta name="description" content="确认页面" />
    <link rel="stylesheet" type="text/css" href="style/remind.css" />
</head>
<body>
<div class="notice">
    <br>
    你确定要删除<?php echo $_GET['delete_class']?> "<?php echo $_GET['del_info']?>" ?
    <br>
    <a style="color: red" href="<?php echo $_GET['url'] . "&return_url=" .urlencode($url)?>">确定</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a style="color: green" href="<?php echo $_GET['return_url']?>">取消</a>
</div>
</body>
</html>
