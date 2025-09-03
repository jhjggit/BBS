<?php
    include_once "../inc/config.php";
    include_once "../inc/mysql_tools.php";
    include_once "../inc/tools.inc.php";


    if (is_manage_login(($link = new mysql_tools()))){
        skip("/admin/father_module.php",PIC_SUCCESS,"您已登录！",0);
    }else{
        skip("/admin/login.php",PIC_FAILED,"请先登录！",);
    }