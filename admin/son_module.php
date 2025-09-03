<?php
    //查询子板块信息
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
                skip("son_module.php",PIC_FAILED,"非法数据");
            }
            $sql[] = "update loe_son_module set sort = {$val} where module_id = {$key}";
        }

        /***
         * 没进行子板块存在验证,懒...
         */

        //链接数据库并执行多条语句
        $error = "";
        $link = new mysql_tools();
        if (!$link->multi_execute($sql,$error)){
            skip($_SERVER['HTTP_REFERER'],PIC_FAILED,$error);
        }
    }

//设置当前页面title
    $infos['info'] = "子板块列表";
    $infos['css'] = array("/admin/style/public.css");

    $link = new mysql_tools();

    //组织多表查询的SQL
    $sql = <<<SQL
select lsm.sort,lfm.module_name as f_module_name,lsm.module_name as s_module_name,lsm.module_id as s_module_id,lsm.info,lsm.manager_member_id from loe_son_module as lsm,loe_father_module as lfm where lsm.father_module_id = lfm.module_id order by f_module_name
SQL;

    //查询子板块的数量
    $son_mod_ResSet = $link->execute($sql);

?>

<?php include_once "inc/header.inc.php"; ?>
功能
<div id="main">
    <div class="title">子列表</div>
    <form method="post">
        <table class="list">
            <tr>
                <th>排序</th>
                <th>所属父板块</th>
                <th>子版块名称</th>
                <th>子版块信息</th>
                <th>版主UID</th>
                <th>操作</th>
            </tr>
            <?php
            //$_SERVER['REQUEST_URI'] 储存着当前页面的URL
            $return_url = urlencode($_SERVER['REQUEST_URI']);
            //设置删除的类别
            $delete_class = "子版块";
            while ($f_mod = mysqli_fetch_array($son_mod_ResSet,MYSQLI_ASSOC)){

                //设置删除、更新等链接
                $del = urlencode("son_module_delete.php?id={$f_mod['s_module_id']}");
                $info = "{$f_mod['s_module_name']}";
                $ensure_delete_url = "./confirm.php?url=$del&return_url=$return_url&del_info=$info&delete_class=$delete_class";
                $update_url = "/admin/son_module_update.php?update_id={$f_mod['s_module_id']}";
                echo  <<<A
                <tr>
                    <td><input class="sort" type="text" name="sort[{$f_mod['s_module_id']}]" value="{$f_mod['sort']}"/></td>
                    <td>{$f_mod['f_module_name']}</td>
                    <td>{$f_mod['s_module_name']} [id:{$f_mod['s_module_id']}]</td>
                    <td>{$f_mod['info']}</td>
                    <td>{$f_mod['manager_member_id']}</td>
                    <td><a href="/list_son.php?son_id={{$f_mod['s_module_id']}}">[访问]</a>&nbsp;&nbsp;<a href="{$update_url}">[编辑]</a>&nbsp;&nbsp;<a href="{$ensure_delete_url}">[删除]</a></td>
                </tr>
A;
            }
            ?>
        </table>
        <input style="margin-top: 20px; cursor: pointer; width: 56px" id="giveUp" type="submit" name="submit" class="btn" value="排序">
    </form>
</div>

<?php include_once "inc/bottom.inc.php"; ?>
