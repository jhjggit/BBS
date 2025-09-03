<?php
    include_once "../inc/config.php";
    include_once "../inc/mysql_tools.php";
    include_once "../inc/tools.inc.php";

    include_once "inc/check_isLogin.inc.php";

    //设置当前页面title
    $infos['info'] = "用户列表";
    $infos['css'] = array("/admin/style/public.css");

?>

<?php
    $link = new mysql_tools();

    $sql = <<<SQL
select * from loe_member
SQL;

    $res = ($link->execute($sql));
?>

<?php include_once "./inc/header.inc.php"?>
<div id="main">
    <div class="title">用户列表</div>

        <table class="list">
            <tr>
                <th>UID</th>
                <th>用户名</th>
                <th>主页</th>
            </tr>
            <?php

            while ($f_mod = mysqli_fetch_array($res,MYSQLI_ASSOC)){

                //设置删除、更新等链接
                echo  <<<A
                <tr>
                    <td>{$f_mod['member_uid']}</td>
                    <td>{$f_mod['member_name']}</td>
                    <td><a href="/member.php?member_id={$f_mod['member_uid']}">Jump to user's page</a></td>
                </tr>
A;
            }
            ?>
        </table>
</div>
<?php include_once "./inc/bottom.inc.php"?>
