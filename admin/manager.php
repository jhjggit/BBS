<?php
    include_once "../inc/config.php";
    include_once "../inc/mysql_tools.php";
    include_once "../inc/tools.inc.php";


    include_once "inc/check_isLogin.inc.php";


    //设置当前页面title
    $infos['info'] = "管理员列表";
    $infos['css'] = array("/admin/style/public.css");

    $link = new mysql_tools();
    //查询管理员的数量
    $mod_ResSet = $link->execute("select * from loe_manage");
?>

<?php include "./inc/header.inc.php"; ?>
<div id="main">
    <div class="title">管理员列表</div>
    <form method="post">
        <table class="list">
            <tr>
                <th>名称</th>
                <th>等级</th>
                <th>创建日期</th>
                <th>操作</th>
            </tr>
            <?php
            //$_SERVER['REQUEST_URI'] 储存着当前页面的URL
            $return_url = urlencode($_SERVER['REQUEST_URI']);
            while ($manager_data = mysqli_fetch_array($mod_ResSet,MYSQLI_ASSOC)){
                $del = urlencode("manager_delete.php?id={$manager_data['manager_id']}");
                $delete_class = "管理员";
                $info = "{$manager_data['manager_name']}";
                $ensure_delete_url = "./confirm.php?url=$del&return_url=$return_url&del_info=$info&delete_class=$delete_class";

                $level = "";

                if ($manager_data['level'] == 0){
                    $level = "超级管理员";
                }else{
                    $level = "普通管理员";
                }

                echo  <<<A
                <tr>
                    <td>{$manager_data['manager_name']}</td>
                    <td>{$level}</td>
                    <td>{$manager_data['create_time']}</td>
                    <td><a href="{$ensure_delete_url}">[删除]</a></td>
                </tr>
A;
            }
            ?>
        </table>

    </form>
</div>
<?php include "./inc/bottom.inc.php"; ?>
