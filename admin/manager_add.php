<?php
    include_once "../inc/config.php";
    include_once "../inc/mysql_tools.php";
    include_once "../inc/tools.inc.php";

    include_once "inc/check_isLogin.inc.php";

    //准备信息
    $infos['info'] = "管理员添加页";
    $infos['css'] = array("/admin/style/public.css","/admin/style/father_module_add.css");

?>


<?php
    if (isset($_POST['submit'])){
        //检查脚本
        include_once "./inc/check_manage_add.inc.php";

        $link = new mysql_tools();

        //将POST转义
        $_POST = $link->escape($_POST);
        //若含有特殊的html代码，进行转义
        $_POST['manage_name'] = htmlspecialchars($_POST['manage_name']);

        $pwd = $_POST['passwd'];

        //如果用户已存在
        $sql = <<<SQL
select * from loe_manage where manager_name = "{$_POST['manage_name']}"
SQL;

        if (mysqli_fetch_array($link->execute($sql)) == 1){
            skip_error("用户已经存在！");
        }

        //经过验证,组织sql 创建用户
        $sql = <<<SQL
insert into loe_manage(`manager_name`,`password`,`level`) value("{$_POST['manage_name']}",md5("{$_POST['passwd']}"),{$_POST['level']})
SQL;

        if ($link->execute_bool($sql)) {
            skip("/admin/manager_add.php",PIC_SUCCESS,"添加成功！");
        }else{
            skip("/admin/manager_add.php",PIC_SUCCESS,"添加失败！");
        }
    }

?>

<?php include_once "./inc/header.inc.php";?>
<div id="main">
    <div class="title">添加管理员</div>
    <form method="post">
        <table class="au">
            <tr>
                <td>管理员名称</td>
                <td><input name="manage_name" type="text" /></td>
                <td>
                    名称不得为空, 最大字符为64个
                </td>
            </tr>
            <tr>
                <td>密码</td>
                <td><input name="passwd" type="password" value=""/></td>
                <td>
                    填入密码 [最少8位]
                </td>
            </tr>
            <tr>
                <td>等级</td>
                <td>
                    <select name="level">
                        <option value="1">普通管理员 [版主]</option>
                        <option value="0">超级管理员 [管理所有]</option>
                    </select>
                </td>
                <td>
                    填入密码 [最少8位]
                </td>
            </tr>
        </table>
        <input style="margin-top: 20px; cursor: pointer;" id="giveUp" name="submit" class="btn" type="submit" value="添加">
    </form>
</div>

<?php include_once "./inc/bottom.inc.php";?>
