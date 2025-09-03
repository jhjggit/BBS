<?php
    include_once "inc/config.php";
    include_once "inc/mysql_tools.php";
    include_once "inc/tools.inc.php";

    //验证引用回复
    //是否登录
    if (!($member_uid = is_login(new mysql_tools()))){
        skip_error("请先登录","/login.php");
    }

    //参数是否存在
    if (!isset($_GET['content_id']) || !isset($_GET['reply_id']) || !isset($_GET['member_id']) || !isset($_GET['floor'])){
        skip_error("参数错误！请重试~！");
    }

    //帖子是否存在
    $link = new mysql_tools();
    $sql = <<<SQL
select * from loe_content where content_id = {$_GET['content_id']}
SQL;

    if (!(mysqli_num_rows($link->execute($sql)) == 1)){
        skip_error("帖子不存在！");
    }

    //回复是否存在
    $sql = <<<SQL
select * from loe_reply where reply_id = {$_GET['reply_id']}
SQL;

    if (!(mysqli_num_rows($link->execute($sql)) == 1)){
        skip_error("回复不存在！");
    }

    //用户是否存在,res返回到$member_res
    $sql = <<<SQL
select * from loe_member where member_uid = {$_GET['member_id']}
SQL;
    $member_res = "eqw";
    if (!(mysqli_num_rows(($member_res = $link->execute($sql))) == 1)){
        skip_error("用户不存在！");
    }
