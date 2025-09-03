<?php
    include_once "../inc/config.php";
    include_once "../inc/mysql_tools.php";
    include_once "../inc/tools.inc.php";

    include_once "inc/check_isLogin.inc.php";

    if (!isset($_GET['update_id']) || !is_numeric($_GET['update_id'])){
        skip($_SERVER['HTTP_REFERER'],PIC_FAILED,"参数错误！或非法数值!");
    }

    //链接数据库进行查询
    $link = new mysql_tools();
    //组织sql
    $sql = <<<SQL
    SELECT * FROM loe_son_module where module_id = {$_GET['update_id']}
SQL;

    //获取数据库中的条目,用于数据比较
    $res = $link->execute($sql);
    if ($res->num_rows == 0){
        skip($_SERVER['REQUEST_URI'],PIC_FAILED,"这条板块信息不存在！");
    }

    //获取父板块的信息
    $father_res = $link->execute("select * from loe_father_module");

    //解析结果集
    $son_data = mysqli_fetch_array($res);

    //处理POST请求
    if (isset($_POST['submit'])){
        //设置检查flag
         $check_flag = "update";
        //引入检查脚本
        include_once "inc/check_son_module.inc.php";

        //将数据转义
        $_POST = $link->escape($_POST);

        //组织sql,准备执行
        $sql = <<<SQL
update loe_son_module set module_name = "{$_POST['update_name']}",sort = "{$_POST['update_sort']}",father_module_id = {$_POST['update_father_id']},
                          info = "{$_POST['update_info']}",manager_member_id = {$_POST['update_manager_id']} where module_id = {$son_data[0]}
SQL;

        //执行SQL
        if ($link->execute_bool($sql) && mysqli_affected_rows($link->connect) == 1){
            skip($_SERVER['REQUEST_URI'],PIC_SUCCESS,"子板块修改成功！");
        }else{
            skip($_SERVER['REQUEST_URI'],PIC_FAILED,"子板块修改失败！");
        }
    }

    //准备信息
    $infos['info'] = "修改子板块";
    $infos['css'] = array("/admin/style/public.css","/admin/style/father_module_add.css");
?>
<?php include_once "./inc/header.inc.php"?>
<div id="main">
    <div class="title">修改子板块 - <?php echo $son_data["module_name"]?></div>
    <form method="post">
        <table class="au">
            <tr>
                <td>板块排序</td>
                <td><input name="update_sort" type="text" value="<?php echo $son_data["sort"]?>"/></td>
                <td>
                    填入数值 [默认填入0即可]
                </td>
            </tr>
            <tr>
                <td>选择父板块</td>
                <td>
                    <select name="update_father_id">
                        <?php
                            while (($f_data = mysqli_fetch_array($father_res))){
                                if ($son_data['father_module_id'] == $f_data['module_id']){
                                    echo "<option selected='selected' value='{$f_data['module_id']}'>{$f_data['module_name']}</option>>";
                                }else{
                                    echo "<option value='{$f_data['module_id']}'>{$f_data['module_name']}</option>>";
                                }
                            }
                        ?>
                    </select>
                </td>
                <td>
                    填入数值 [默认填入0即可]
                </td>
            </tr>
            <tr>
                <td>板块名称 </td>
                <td><input name="update_name" type="text"  value="<?php echo $son_data["module_name"]?>"/></td>
                <td>
                    版块名称不得为空, 最大字符为66个
                </td>
            </tr>
            <tr>
                <td>子版块信息</td>
                <td><input name="update_info" type="text" value="<?php echo $son_data["info"]?>"/></td>
                <td>
                    填入数值 [默认填入0即可]
                </td>
            </tr>
            <tr>
                <td>版主UID</td>
                <td><input name="update_manager_id" type="text" value="<?php echo $son_data["manager_member_id"]?>"/></td>
                <td>
                    填入数值 [默认填入0即可]
                </td>
            </tr>

        </table>
        <input style="margin-top: 20px; cursor: pointer;" id="giveUp" name="submit" class="btn" type="submit" value="修改">
    </form>
</div>
<?php include_once "./inc/bottom.inc.php"?>
