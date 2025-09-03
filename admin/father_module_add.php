<?php
    include_once "../inc/config.php";
    include_once "../inc/mysql_tools.php";
    include_once "../inc/tools.inc.php";

    include_once "inc/check_isLogin.inc.php";


    if (isset($_POST['submit'])){
        //获取POST传递的信息


        //验证数据
        if ((strlen($_POST['module_name']) / 3 == 0 || strlen($_POST['sort']) == 0 || !is_numeric($_POST['sort']) || strlen($_POST['module_name']) / 3 > 66)){
            skip("./father_module_add.php",PIC_FAILED,"填入数据出错！",3);
        }


        //链接数据库
        $link = new mysql_tools();

        //将数据进行转义
        $_POST = $link->escape($_POST);

        $module_name = $_POST['module_name'];
        $module_sort = $_POST['sort'];
        //经过验证,组织SQL
        $sql = <<<SQL
insert into loe_father_module(module_name,sort) value('{$module_name}',{$module_sort});
SQL;


        if ($link->execute_bool($sql)){
            skip($_SERVER['REQUEST_URI'],PIC_SUCCESS,"恭喜您, 添加父板块成功!");
        }else{
            skip($_SERVER['REQUEST_URI'],PIC_FAILED,"添加父板块失败! 请检查SQL语句");
        }
    }

    //准备信息
    $infos['info'] = "添加父板块";
    $infos['css'] = array("/admin/style/public.css","/admin/style/father_module_add.css");


?>

<?php include_once "./inc/header.inc.php";?>
<div id="main">
    <div class="title">添加父板块</div>
    <form method="post">
        <table class="au">
            <tr>
                <td>版块名称</td>
                <td><input name="module_name" type="text" /></td>
                <td>
                    版块名称不得为空, 最大字符为66个
                </td>
            </tr>
            <tr>
                <td>排序</td>
                <td><input name="sort" type="text" value=""/></td>
                <td>
                    填入数值 [默认填入0即可]
                </td>
            </tr>
        </table>
        <input style="margin-top: 20px; cursor: pointer;" id="giveUp" name="submit" class="btn" type="submit" value="添加">
    </form>
</div>

<?php include_once "./inc/bottom.inc.php";?>

