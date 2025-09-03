<?php

    //验证码校验
    if (!(strcasecmp($_POST['checkcode'],$_SESSION['check_code']) == 0)){
        skip($_SERVER['REQUEST_URI'],PIC_FAILED,"验证码错误！");
    }

    //数据验证
    if (empty($_POST['username']) || empty($_POST['password'])){
        skip($_SERVER['REQUEST_URI'],PIC_FAILED,"数据为空！！！");
    }

    if (strlen($_POST['manage_name']) > 64){
        skip($_SERVER['REQUEST_URI'],PIC_FAILED,"用户名最大长度为64位！");
    }


    if (intval($_POST['level']) < 0 || intval($_POST['level']) > 1){
        skip($_SERVER['REQUEST_URI'],PIC_FAILED,"等级异常！");
    }