<?php
    //获取到当前的文件名
    $current_flie = basename($_SERVER['SCRIPT_NAME']);

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8" />
    <title><?php echo $infos['info']?></title>
    <meta name="keywords" content="<?php echo $infos['info']?>" />
    <meta name="description" content="<?php echo $infos['info']?>" />
    <?php
        foreach ($infos['css'] as $val) {
            echo <<<A
<link rel="stylesheet" type="text/css" href="{$val}" />
A;
        }
        
    ?>
</head>
<body>
<!--顶部-->
<div id="top">
    <div class="logo">
        管理中心
    </div>
    <ul class="nav">
        <li><a href="https://www.bilibili.com/" target="_blank">bilibili</a></li>
        <li><a href="http://binary-converter.bchrt.com/" target="_blank">进制转换</a></li>
        <li><a href="https://www.runoob.com/" target="_blank">菜鸟</a></li>
        <li><a href="https://www.runoob.com/php/php-functions.html" target="_blank">PHP函数库</a></li>
    </ul>
    <div class="login_info">
        <a href="/index.php" style="color:#fff;">网站首页</a>&nbsp;|&nbsp;
        管理员： <?php echo $_SESSION['manage']['name']?> <a href="/admin/confirm_manage.php?url=logout.php&return_url=<?php echo $_SERVER['REQUEST_URI']?>">[注销]</a>
    </div>
</div>

<!--侧边栏-->
<div id="sidebar">
    <ul>
        <li>
            <div class="small_title">系统</div>
            <ul class="child">
                <li><a <?php if ($current_flie == "systeminfo.php") echo 'class="current"'; ?> href="/admin/systeminfo.php">系统信息</a></li>
                <li><a <?php if ($current_flie == "manager.php") echo 'class="current"'; ?> href="/admin/manager.php">管理员</a></li>
                <li><a <?php if ($current_flie == "manager_add.php") {echo 'class="current"';} ?> href="/admin/manager_add.php">添加管理员</a></li>
            </ul>
        </li>
        <li><!--  class="current" -->
            <div class="small_title">内容管理</div>
            <ul class="child">
                <li><a <?php if ($current_flie == "father_module.php") {echo 'class="current"';} ?> href="/admin/father_module.php">父板块列表</a></li>
                <li><a <?php if ($current_flie == "father_module_add.php") {echo 'class="current"';} ?> href="/admin/father_module_add.php">添加父板块</a></li>
                <?php
                if (basename($_SERVER['SCRIPT_NAME']) == "father_module_update.php"){
                    echo <<<HTML
<li><a class="current"  href="#">编辑父板块</a></li>
HTML;
                }
                ?>
                <li><a <?php if ($current_flie == "son_module.php") {echo 'class="current"';} ?> href="/admin/son_module.php">子板块列表</a></li>
                <li><a <?php if ($current_flie == "son_module_add.php") {echo 'class="current"';} ?> href="/admin/son_module_add.php">添加子板块</a></li>
                <?php
                if (basename($_SERVER['SCRIPT_NAME']) == "son_module_update.php"){
                    echo <<<HTML
<li><a class="current"  href="#">编辑子板块</a></li>
HTML;
                }
                ?>
            </ul>
        </li>
        <li>
            <div class="small_title">用户管理</div>
            <ul class="child">
                <li> <a <?php if ($current_flie == "userslist.php") {echo 'class="current"';} ?>  href="/admin/userslist.php">用户列表</a></li>
            </ul>
        </li>
    </ul>
</div>
