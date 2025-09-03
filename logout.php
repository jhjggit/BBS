<?php
    include_once "inc/config.php";
    include_once "inc/mysql_tools.php";
    include_once "inc/tools.inc.php";

    if (!(isset($_GET['uid']) && isset($_GET['return_url']))){
        exit("非法的请求！");
    }

    $link = new mysql_tools();

    if (!(mysqli_num_rows($link->execute("select * from loe_member where member_uid = {$_GET['uid']}")) ==  1)){
        skip($_GET['return_url'],PIC_FAILED,"用户不存在！");
    }

    if (!is_login($link)){
        skip($_GET['return_url'],PIC_FAILED,"你尚未登录！");
    }

    logout();

    skip("/index.php",PIC_SUCCESS,"退出成功！");

