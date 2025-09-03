<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8" />
    <title><?php echo $infos['title']?></title>
    <meta name="keywords" content="<?php echo $infos['title']?>" />
    <meta name="description" content="<?php echo $infos['title']?>" />
    <?php
    foreach ($infos['css'] as $val) {
        echo <<<A
<link rel="stylesheet" type="text/css" href="{$val}" />
A;
    }

    ?>
</head>
<body>
<div class="header_wrap">
    <div id="header" class="auto">
        <div class="logo">Loe-BBS</div>
        <div class="nav">
            <a class="hover" href="<?php echo INDEX_PAGE?>">首页</a>
            <a href="/publish.php">新帖</a>
            <a>话题</a>
        </div>
        <div class="serarch">
            <form action="../serarch.php" method="get">
                <input class="keyword" type="text" name="keyword" placeholder="搜索其实很简单" />
                <input class="submit" type="submit" name="submit" value="default" />
            </form>
        </div>
        <div class="login">
        <?php
        $UN_LOGIN = <<<HTML
<a href="/login.php">请登录</a>&nbsp;<a href="/register.php">注册</a>
HTML;
        if ($member_uid = is_login(new mysql_tools())){
            $logout_link = <<<LINK
/confirm.php?confirm_info=用户 {$_COOKIE['loe_info']['name']} 确定要退出吗?&jump_url=/logout.php?uid={$member_uid}&return_url=/index.php&data={$_COOKIE['loe_info']['name']}
LINK;
            $logout_link .= urlencode("");
            echo <<<LOGIN
<span style="color: #e9e9e9">您好！ <a href="/member.php?member_id={$_COOKIE['loe_info']['uid']}">{$_COOKIE['loe_info']['name']}</a></span>&nbsp;<span style="color: #e9e9e9">|</span>&nbsp;<a style="color: #e9e9e9" href="{$logout_link}">退出</a>
LOGIN;
        }else{
            echo $UN_LOGIN;
        }
        ?>
        </div>
    </div>
    </div>

<div style="margin-top:55px;"></div>
<?php ?>
