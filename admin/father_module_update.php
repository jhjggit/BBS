<?php
    include_once "../inc/config.php";
    include_once "../inc/mysql_tools.php";
    include_once "../inc/tools.inc.php";

    include_once "inc/check_isLogin.inc.php";


    if (!isset($_GET['update_id']) || !is_numeric($_GET['update_id'])){
        skip("/admin/father_module.php",PIC_FAILED,"参数错误！或非法数值!");
    }


    //链接数据库进行查询
    $link = new mysql_tools();
    //组织sql
    $sql = <<<SQL
SELECT * FROM loe_father_module where module_id = {$_GET['update_id']}
SQL;

    //获取结果集
    $res = $link->execute($sql);
    if ($res->num_rows == 0){
        skip($_SERVER['REQUEST_URI'],PIC_FAILED,"这条板块信息不存在！");
    }

    //解析结果集
    $data = mysqli_fetch_array($res);

    //处理POST请求
    if (isset($_POST['submit'])){
        //判断数据是否有变化
        if ($_POST['update_name'] == $data[1] && $_POST['update_sort'] == $data[2]){
            skip($_SERVER['REQUEST_URI'],PIC_FAILED,"数据无变化！");
        }

        //验证数据
        if ((strlen($_POST['update_name']) / 3 == 0 || strlen($_POST['update_sort']) == 0 || !is_numeric($_POST['update_sort']) || strlen($_POST['update_name']) / 3 > 66)){
            skip($_SERVER['REQUEST_URI'],PIC_FAILED,"数据填写有误！");
        }

        //将数据转义
        $_POST = $link->escape($_POST);

        //组织sql,准备执行
        $sql = <<<SQL
update loe_father_module set module_name = "{$_POST['update_name']}",sort = "{$_POST['update_sort']}" where module_id = {$data[0]}
SQL;

        //执行SQL
        if ($link->execute_bool($sql) && mysqli_affected_rows($link->connect) == 1){
            skip($_SERVER['REQUEST_URI'],PIC_SUCCESS,"父板块修改成功！");
        }else{
            skip($_SERVER['REQUEST_URI'],PIC_FAILED,"父板块修改失败！");
        }
    }

    //准备信息
    $infos['info'] = "修改父板块";
    $infos['css'] = array("/admin/style/public.css","/admin/style/father_module_add.css");


?>
<?php include_once "./inc/header.inc.php"?>
<div id="main">
    <div class="title">修改父板块 - <?php echo $data[1]?></div>
    <form method="post">
        <table class="au">
            <tr>
                <td>板块名称 </td>
                <td><input name="update_name" type="text"  value="<?php echo $data[1]?>"/></td>
                <td>
                    版块名称不得为空, 最大字符为66个
                </td>
            </tr>
            <tr>
                <td>板块排序</td>
                <td><input name="update_sort" type="text" value="<?php echo $data[2]?>"/></td>
                <td>
                    填入数值 [默认填入0即可]
                </td>
            </tr>
        </table>
        <input style="margin-top: 20px; cursor: pointer;" id="giveUp" name="submit" class="btn" type="submit" value="修改">
    </form>
</div>
<?php include_once "./inc/bottom.inc.php"?>
