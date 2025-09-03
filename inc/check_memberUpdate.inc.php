<?php
    if (!isset($_GET['member_id']) || !is_numeric($_GET['member_id'])){
        skip_error("错误！");
    }

    $link = new mysql_tools();

    $sql = <<<SQL
select * from loe_member where member_uid = {$_GET['member_id']}
SQL;

    $memberRes = $link->execute($sql);

    if (mysqli_num_rows($memberRes) == 0){
        skip_error("没有此用户！");
    }

    $memberData = mysqli_fetch_array($memberRes);

    //通过初步验证, 开始确保是否真的为用户本人操作
    //日后添加管理员特权
    if (!($memberData['member_uid'] == $_COOKIE['loe_info']['uid'] && strcasecmp($memberData['password'],$_COOKIE['loe_info']['passwd']))){
        skip_error($flag);
    }

