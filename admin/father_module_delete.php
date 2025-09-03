<?php
    include_once "../inc/config.php";
    include_once "../inc/mysql_tools.php";
    include_once "../inc/tools.inc.php";

    include_once "inc/check_isLogin.inc.php";

    //对GET传来的数据进行安全验证
    delete_check();


    //链接数据库
    $link = new mysql_tools();

    //获取要删除父板块的唯一id GET
    $delete_id = $_GET['id'];

    //验证该父板块有没有子版块
    $SON = SON_MODULE;
    if ($link->execute("select * from {$SON} where father_module_id = {$delete_id}")->num_rows != 0){
        skip("./father_module.php",PIC_FAILED,"此父板块有子版块,请先删除子版块！",3);
    }

    //组织sql
    $sql = <<<SQL
delete from loe_father_module where module_id = {$_GET['id']}
SQL;
    //执行
    if ($link->execute_bool($sql) && mysqli_affected_rows($link->connect) == 1) {
        $jump_info = "ID 为: {$delete_id}的条目删除成功! ! ";
        skip("./father_module.php",PIC_SUCCESS,$jump_info,3);
    }else{
        $jump_info = "ID 为: {$delete_id}的条目删除失败!";
        skip("./father_module.php",PIC_FAILED,$jump_info,3);
    }

?>



