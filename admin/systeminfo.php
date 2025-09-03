<?php
    include_once "../inc/config.php";
    include_once "../inc/mysql_tools.php";
    include_once "../inc/tools.inc.php";

    //设置当前页面title
    $infos['info'] = "系统信息";
    $infos['css'] = array("/admin/style/public.css");
?>

<?php
    //获取系统信息
    $sql = <<<SQL
select * from loe_manage where manager_name = "{$_SESSION['manage']['name']}"
SQL;

    $link = new mysql_tools();

    $manageData = mysqli_fetch_array($link->execute($sql));


    //获取父板块数量
    $sql = <<<SQL
select count(*) as father_num from loe_father_module
SQL;

    //父板块数量
    $fatherModuleNum = mysqli_fetch_array($link->execute($sql))['father_num'];

    //获取帖子数量
    $sql = <<<SQL
select count(*) as content_num from loe_content
SQL;

    //帖子数量
    $contentNum =  mysqli_fetch_array($link->execute($sql))['content_num'];

    //获取回复数量
    $sql = <<<SQL
select count(*) as replyNum from loe_reply
SQL;

    $replyNum = mysqli_fetch_array($link->execute($sql))['replyNum'];

    //获取会员数量
    $sql = <<<SQL
select count(*) as memberNum from loe_member
SQL;

    $memberNum = mysqli_fetch_array($link->execute($sql))['memberNum'];


    //获取管理员数量
    $sql = <<<SQL
select count(*) as manageNum from loe_manage
SQL;

    $manageNum = mysqli_fetch_array($link->execute($sql))['manageNum'];

    //获取服务器操作系统
    $os = PHP_OS;
    //获取web软件
    $software = $_SERVER['SERVER_SOFTWARE'];
    //获取mysql版本
    $mysqlversion = mysqli_get_server_info($link->connect);
    //最大上传文件
    $max_upload = ini_get("upload_max_filesize");
    //内存限制
    $memory = ini_get("memory_limit");
    //当前绝对路径
    $abs_path = dirname(dirname(__FILE__));
?>

<?php include_once "./inc/header.inc.php"?>
    <div id="main">
        <div class="title">系统信息</div>
        <div class="explain">
            <ul>
                <li>|- 您好，<?php echo $manageData['manager_name']?></li>
                <li>|- 所属角色：<?php if ($manageData['level'] == 0) {
                    echo "超级管理员";
                }else{
                    echo "普通管理员";
                    }
                    ?> </li>
                <li>|- 创建时间：<?php echo $manageData['create_time']?></li>
            </ul>
        </div>
        <div class="explain">
            <ul>
                <li>|- 版块(<?php echo $fatherModuleNum?>) 帖子(<?php echo $contentNum?>) 回复(<?php echo $replyNum?>) 会员(<?php echo $memberNum?>) 管理员(<?php echo $manageNum?>)</li>
            </ul>
        </div>
        <div class="explain">
            <ul>
                <li>|- 服务器操作系统：<?php echo $os ?> </li>
                <li>|- 服务器软件：<?php echo $software ?> </li>
                <li>|- MySQL 版本：<?php echo $mysqlversion ?></li>
                <li>|- 最大上传文件：<?php echo $max_upload ?></li>
                <li>|- 内存限制：<?php echo $memory ?></li>
                <li>|- <a target="_blank" href="./phpinfo.php">PHP 配置信息</a></li>
            </ul>
        </div>

        <div class="explain">
            <ul>
                <li>|- 程序安装位置(绝对路径)：<?php echo $abs_path ?></li>
                <li>|- 程序版本：loe-bbs V1.0 <a target="_blank" href="http://www.loe-bbs.com">[查看最新版本]</a></li>
                <li>|- 程序作者：Loe :))</li>
                <li>|- 网站：<a target="_blank" href="http://www.loe-bbs.com">www.loe-bbs.com</a></li>
            </ul>
        </div>
    </div>
<?php include_once "./inc/bottom.inc.php"?>