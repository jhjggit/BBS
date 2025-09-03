<?php
    include_once "../inc/config.php";
    include_once "../inc/mysql_tools.php";
    include_once "../inc/tools.inc.php";

    include_once "inc/check_isLogin.inc.php";

    //验证GET数据
    delete_check();

    $link = new mysql_tools();
    //获取要删除子板块的唯一id GET
    $delete_id = $_GET['id'];
    //组织sql
    $sql = detele_sql(SON_MODULE, "module_id", $delete_id);
    //执行
    if ($link->execute_bool($sql) && mysqli_affected_rows($link->connect) == 1) {
        $jump_info = "ID 为: {$delete_id}的条目删除成功! ! ";
        skip($_GET['return_url'],PIC_SUCCESS,$jump_info,3);
    }else{
        $jump_info = "ID 为: {$delete_id}的条目删除失败!";
        skip($_GET['return_url'],PIC_FAILED,$jump_info,3);
    }
