<?php
    //验证添加管理员各字段
    if (!isset($_POST['manage_name']) || !isset($_POST['passwd']) ||
        !isset($_POST['level']) || !is_numeric($_POST['level'])){
        skip_error("参数错误");
    }

    if (!(intval($_POST['level']) == 0 || intval($_POST['level']) == 1)){
        skip_error("等级异常");
    }

    //检查管理员用户名规范
    if (strlen($_POST['manage_name']) == 0 || strlen($_POST['manage_name']) > 64 ){
        skip_error("用户名不规范！");
    }

    if (strlen($_POST['passwd']) < 8 ){
        skip_error("密码少于8位");
    }