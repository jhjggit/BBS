<?php
include_once "../inc/config.php";
include_once "../inc/mysql_tools.php";
include_once "../inc/tools.inc.php";

include_once "inc/check_isLogin.inc.php";

//准备信息
$infos['info'] = "管理员删除页";
$infos['css'] = array("/admin/style/public.css","/admin/style/father_module_add.css");

?>

<?php
    $link = new mysql_tools();

    //为了可拓展性,需要引入此脚本
    include_once "./inc/check_manage_delete.inc.php";

    //通过验证,删除用户
    $sql = <<<SQL
delete from loe_manage where manager_id = {$_GET['id']}
SQL;

    if ($link->execute_bool($sql)){
        skip("/admin/manager.php",PIC_SUCCESS,"删除成功！");
    }else{
        skip("/admin/manager.php",PIC_SUCCESS,"删除失败！");
    }

?>
