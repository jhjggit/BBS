<?php

    //检查管理员用户id是否设置
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])){
        skip_error("非法访问！");
    }

    //检查是否有此管理员
    $sql = <<<SQL
select * from loe_manage where manager_id = {$_GET['id']}
SQL;

    $manage_res = $link->execute($sql);

    if (mysqli_num_rows($manage_res) == 0){
        skip_error("没有此管理员！");
    }

    $manageData = mysqli_fetch_array($manage_res);

    //验证用户密码与本地存储的cookie是否相同..