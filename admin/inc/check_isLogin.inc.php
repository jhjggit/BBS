<?php
    if (!(isset($_SESSION['manage']))){
        skip("/admin/login.php",PIC_FAILED,"请先登录！");
    }

    //检查用户的等级
    if (intval($_SESSION['manage']['level']) != 0){
        skip("/admin/login.php",PIC_FAILED,"您的权限不足！");
    }