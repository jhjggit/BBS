<?php

    include_once "../inc/config.php";
    include_once "../inc/mysql_tools.php";
    include_once "../inc/tools.inc.php";

    //退出管理员

    if (!(isset($_SESSION['manage']))){
        skip("/admin/login.php",PIC_FAILED,"请先登录！");
    }

    unset($_SESSION['manage']);

    skip_success("退出成功！！！");