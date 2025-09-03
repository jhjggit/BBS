<?php
/**
 * 引用此脚本前请先定义flag,这是当用户没有权力对帖子进行操作显示的信息
 */

    if (!isset($_GET['content_id']) ||  !is_numeric($_GET['content_id'])){
        skip_error("参数错误！");
    }

    $link = new mysql_tools();
    $sql = <<<SQL
select * from loe_content where content_id = {$_GET['content_id']}
SQL;
    if (mysqli_num_rows(($content_res = $link->execute($sql))) == 0){
        skip_error("帖子不存在！");
    }

    $contentData = mysqli_fetch_array($content_res);

    $member_uid = $contentData['member_uid'];

    $sql = <<<SQL
select * from loe_member where member_uid = {$member_uid}
SQL;

    //查询到该帖子发布者的信息
    $member_data = mysqli_fetch_array($link->execute($sql));



//    检查用户是否可以修改此帖子
    if (!(strcasecmp($_COOKIE['loe_info']['passwd'],$member_data['password']) == 0 && $_COOKIE['loe_info']['uid'] == $member_data['member_uid'])){
        //检查用户是不是管理员
        if (!isset($_SESSION['manage'])){
            skip_error($flag);
        }

    }

