<?php
    include_once "../inc/config.php";
    include_once "../inc/mysql_tools.php";
    include_once "../inc/tools.inc.php";

    include_once "inc/check_isLogin.inc.php";


    //处理POST请求
    if (isset($_POST['submit'])){

        $sql = array();

        //取出每个记录对应的id
        foreach ($_POST['sort'] as $key=>$val){
            if (!is_numeric($val) || !is_numeric($key)){
                skip("father_module.php",PIC_FAILED,"非法数据");
            }
            $sql[] = "update loe_father_module set sort = {$val} where module_id = {$key}";
        }

        /***
         * 没进行父板块存在验证,懒...
         */

        //链接数据库并执行多条语句
        $error = "";
        $link = new mysql_tools();
        if (!$link->multi_execute($sql,$error)){
            skip($_SERVER['HTTP_REFERER'],PIC_FAILED,$error);
        }
    }

    //设置当前页面title
    $infos['info'] = "父板块列表";
    $infos['css'] = array("/admin/style/public.css");

    $link = new mysql_tools();
    //查询父板块的数量
    $mod_ResSet = $link->execute("select * from loe_father_module");
?>


<?php
//将html页面中同样的代码封装到一个文件中,需要时直接引入即可
include "./inc/header.inc.php"; ?>
    <!--功能-->
    <div id="main">
        <div class="title">父板块列表</div>
        <form method="post">
            <table class="list">
                <tr>
                    <th>排序</th>
                    <th>版块名称</th>
                    <th>操作</th>
                </tr>
                <?php
                //$_SERVER['REQUEST_URI'] 储存着当前页面的URL
                $return_url = urlencode($_SERVER['REQUEST_URI']);
                //设置删除的类别
                $delete_class = "父板块";
                while ($f_mod = mysqli_fetch_array($mod_ResSet,MYSQLI_ASSOC)){
                    $del = urlencode("father_module_delete.php?id={$f_mod['module_id']}");
                    $info = "{$f_mod['module_name']}";
                    $ensure_delete_url = "./confirm.php?url=$del&return_url=$return_url&del_info=$info&delete_class=$delete_class";
                    $update_url = "/admin/father_module_update.php?update_id={$f_mod['module_id']}";
                    echo  <<<A
                <tr>
                    <td><input class="sort" type="text" name="sort[{$f_mod['module_id']}]" value="{$f_mod['sort']}"/></td>
                    <td>{$f_mod['module_name']} [id:{$f_mod['module_id']}]</td>
                    <td><a href="/list_father.php?father_id={$f_mod['module_id']}">[访问]</a>&nbsp;&nbsp;<a href="{$update_url}">[编辑]</a>&nbsp;&nbsp;<a href="{$ensure_delete_url}">[删除]</a></td>
                </tr>
A;
                }
                ?>
            </table>
            <input type="submit" style="margin-top: 20px; cursor: pointer;" id="giveUp" class="btn" name="submit" value="排序">
        </form>
    </div>

<?php include "./inc/bottom.inc.php"; ?>



