<?php
    include_once "inc/config.php";
    include_once "inc/mysql_tools.php";
    include_once "inc/tools.inc.php";

    include "inc/check_serarch.inc.php";

    $infos['title'] = "搜索页";
    $infos['css'] = array('/style/public.css','/style/list.css');
?>


<?php

    $link = new mysql_tools();

    $sql = <<<SQL
select * from loe_content  where title like '%{$_GET['keyword']}%';
SQL;

    $contentRes = $link->execute($sql);

    $resultNum = mysqli_num_rows($contentRes);

?>


<?php include_once "./inc/header.inc.php"; ?>

<div id="position" class="auto">
    <a href="/index.php">首页</a> &gt; 搜索
</div>
<div id="main" class="auto">
    <div id="left">
        <div class="box_wrap">
            <h3></h3>
            <div class="num">
                <span>共有 <?php echo $resultNum ?> 条结果</span>
            </div>
            <div class="notice"></div>

        </div>
        <div style="clear:both;"></div>
        <ul class="postsList">
            <?php




            while ($data = mysqli_fetch_array($contentRes)){
                $send_time = substr($data['time'],0,10);
                $data['title'] = htmlspecialchars($data['title']);

                //获取回复相关
                $sql = <<<SQL
select count(*) as reply_num from loe_reply where content_id = {$data['content_id']}
SQL;
                //回复的总数
                $reply_num = mysqli_fetch_array($link->execute($sql))['reply_num'];

                //获取发帖用户
                $sql = <<<SQL
select * from loe_member where member_uid = {$data['member_uid']}
SQL;
                $member_data = mysqli_fetch_array($link->execute($sql));

                //获取最后回复的时间
                $sql = <<<SQL
select `time` from loe_reply where content_id = {$data['content_id']} limit 0,1
SQL;
                if ($reply_num == 0){
                    $last_reply_time = "<i>暂无回复</i>";
                }else{
                    //获取最后回复的时间
                    $sql = <<<SQL
select `time` from loe_reply where content_id = {$data['content_id']} limit 0,1
SQL;
                    //最后回复的时间
                    $last_reply_time = mysqli_fetch_array($link->execute($sql))['time'];
                }

                $html = <<<HTML
<li>
                <div class="smallPic">
                    <a href="#">
                        <img width="45" height="45"src="imgs/user_HeadImages/{$member_data['image_url']}">
                    </a>
                </div>
                <div class="subject">
                    <div class="titleWrap"><a href="#">[*]</a>&nbsp;<h2><a href="/content.php?content_id={$data['content_id']}&page=1">{$data['title']}</a></h2></div>
                    <p>
                        楼主：<a style="color: #b1aeae" href="./member.php?member_id={$member_data['member_uid']}">{$member_data['member_name']}</a>&nbsp;{$send_time}&nbsp;&nbsp;&nbsp;&nbsp;最后回复：{$last_reply_time}
                    </p>
                </div>
                <div class="count">
                    <p>
                        回复<br /><span>{$reply_num}</span>
                    </p>
                    <p>
                        浏览<br /><span>{$data['look_times']}</span>
                    </p>
                </div>
                <div style="clear:both;"></div>
            </li>
HTML;
                echo $html;
            }


            ?>
        </ul>
    </div>
    <div style="clear:both;"></div>
</div>

<?php include_once "./inc/bottom.inc.php"; ?>

