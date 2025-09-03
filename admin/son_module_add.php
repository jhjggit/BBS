<?php
    include_once "../inc/config.php";
    include_once "../inc/mysql_tools.php";
    include_once "../inc/tools.inc.php";

    include_once "inc/check_isLogin.inc.php";

    //处理POST
    if (isset($_POST['submit'])){

        //标识为添加
        $check_flag =  "add";
        //引入检查脚本
        include_once "inc/check_son_module.inc.php";

        //链接数据库
        $link = new mysql_tools();

        //将数据进行转义
        $_POST = $link->escape($_POST);

        //获取数据
        $module_name = $_POST['module_name'];
        $f_module_id= $_POST['father_module_id'];
        $manager_id = $_POST['manager_id'];
        $module_info = $_POST['module_info'];
        $module_sort = $_POST['sort'];
        //经过验证,组织SQL
        $sql = <<<SQL
INSERT INTO loe_son_module(father_module_id,info,module_name,manager_member_id,sort) value({$f_module_id},'{$module_info}','{$module_name}',{$manager_id},{$module_sort});
SQL;

        if ($link->execute_bool($sql)){
            skip($_SERVER['REQUEST_URI'],PIC_SUCCESS,"恭喜您, 添加子板块成功!");
        }else{
            skip($_SERVER['REQUEST_URI'],PIC_FAILED,"添加子板块失败! 请检查SQL语句");
        }

    }

    //连接数据库,进行父板块查询
    $sql = <<<SQL
select module_id,module_name from loe_father_module
SQL;
    //获取到父板块的结果集
    $res_m = execute_sql($sql);

    //准备信息
    $infos['info'] = "添加子板块";
    $infos['css'] = array("/admin/style/public.css","/admin/style/father_module_add.css");


?>
<?php include_once "./inc/header.inc.php"?>

<div id="main">
    <div class="title">添加子板块</div>
    <form method="post">
        <table class="au">
            <tr>
                <td>所属父版块</td>
                <td>
                    <select name="father_module_id">
                        <option value="0">====请选择一个父板块====</option>
                        <?php
                            while(($f_res = mysqli_fetch_array($res_m))){
                                $HTML = <<<HTML
<option value="{$f_res['module_id']}">{$f_res['module_name']}</option>
HTML;
                                echo $HTML;
                            }
                        ?>
                    </select>
                </td>
                <td>
                    请选择一个父板块!
                </td>
            </tr>
            <tr>
                <td>版块名称</td>
                <td><input name="module_name" type="text" /></td>
                <td>
                    版块名称不得为空, 最大字符为66个
                </td>
            </tr>
            <tr>
                <td>版块信息</td>
                <td><textarea name="module_info" cols="30" rows="10"></textarea></td>
                <td>
                    板块信息最大字符为255个
                </td>
            </tr>
            <tr>
                <td>版主ID</td>
                <td><input name="manager_id" type="text" /></td>
                <td>
                    请确保指定的版主ID存在！
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

<?php include_once "./inc/bottom.inc.php"; ?>
