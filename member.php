<?php

include_once "inc/config.php";
include_once "inc/mysql_tools.php";
include_once "inc/tools.inc.php";

$link = new mysql_tools();
$infos['title'] = "用户主页";
$infos['css'] = array('/style/public.css','/style/list.css','/style/member.css');
?>

<?php
//引入检查脚本
include_once "inc/check_member.inc.php"
?>

<?php
//经过检查,查询用户,并展示
$link = new mysql_tools();

$sql = <<<SQL
select * from loe_member where member_uid = {$_GET['member_id']}
SQL;

if (mysqli_num_rows(($member_res = $link->execute($sql))) == 0){
    skip("/index.php",PIC_FAILED,"没有该用户！");
}

/*
 * 查询出 $member_data
 */
$member_data = mysqli_fetch_array($member_res);
$member_img_url = $member_data['image_url'];
$member_id = $_GET['member_id'];
$member_name = $member_data['member_name'];
?>

<?php include_once "inc/header.inc.php"?>
<div id="position" class="auto">
    <a href="/index.php">首页</a> &gt; <?php echo $member_data['member_name']?>
</div>
<div id="main" class="auto">
    <div id="left">
        <ul class="postsList">
            <?php
                //查询出用户发过的帖子,并输出
                $sql = <<<SQL
select * from loe_content where member_uid = {$member_id}
SQL;
            $memberContentRes = $link->execute($sql);

            while (($contentData = mysqli_fetch_array($memberContentRes))){
                echo "<li>";
                //输出头像
                echo <<<HTML
<div class="smallPic">
    <a href="#">
    <img width="45" height="45" src="imgs/user_HeadImages/{$member_img_url}" />
    </a>
</div>
HTML;
                //输出帖子内容
                $sql = <<<SQL
select count(*) as replyNum from loe_reply where content_id = {$contentData['content_id']}
SQL;
                //该帖子的浏览次数
                $contentLookTimes = $contentData['look_times'];
                //帖子的标题
                $contentTitle = $contentData['title'];
                //帖子的id
                $content_id = $contentData['content_id'];
                //该帖子回复的数量
                $replyNum = mysqli_fetch_array($link->execute($sql))['replyNum'];
                $sql = <<<SQL
select `time` from loe_reply where content_id = {$contentData['content_id']} order by `time` desc limit 0,1
SQL;
                //帖子最后回复
                $lastReplyTime = $replyNum == 0 ? '<i>暂无回复</i>':mysqli_fetch_array($link->execute($sql))['time'];

                echo <<<HTML
<div class="subject">
    <div class="titleWrap"><h2><a target="_blank" href="/content.php?content_id={$content_id}">{$contentTitle}</a></h2></div>
    <p>
HTML;
                //这是是否输出修改头像HTML标签依据,默认false
                $isUpdateFlag = false;

                //如果通过某种方式恶意修改了cookie
                if (!is_numeric($_COOKIE['loe_info']['uid'])){
                    skip("/index.php",PIC_FAILED,"本地COOKIE值异常");
                }

                //判断本地存储的uid与密码是否与数据库一样
                $sql = <<<SQL
select * from loe_member where member_uid = {$_COOKIE['loe_info']['uid']}
SQL;
                if (mysqli_num_rows(($m_res = $link->execute($sql))) == 0){
                    skip_error("无此用户");
                }

                //取出该用户的密码
                $m_data = mysqli_fetch_array($m_res);

                if (($_COOKIE['loe_info']['uid'] && $_COOKIE['loe_info']['uid'] == $member_id
                && strcasecmp($_COOKIE['loe_info']['passwd'],$m_data['passwd']) == 0)
                || isset($_SESSION['manage']) ){
                    //置flag为true
                    $isUpdateFlag = true;
                    echo <<<Y
<a  target='_blank' style="color: #333" href='/contentUpdate.php?content_id={$content_id}'>编辑</a> | <a style="color: #333" href='/contentDelete.php?content_id={$content_id}'>删除</a>
Y;
                }
    echo <<<HTML
    最后回复：{$lastReplyTime}
    </p>
    </div>
    <div class="count">
    <p>
    回复<br /><span>{$replyNum}</span>
    </p>
    <p>
    浏览<br /><span>{$contentLookTimes}</span>
    </p>
</div>
<div style="clear:both;"></div>
HTML;

                echo "</li>";
            }
            $sql = <<<SQL
select count(*) as contentNum from loe_content where member_uid = {$member_id}
SQL;
            //用户发布的帖子
            $contentNum = mysqli_fetch_array($link->execute($sql))['contentNum'];

            ?>
        </ul>
        <div class="pages"></div>
    </div>
    <div id="right">
        <div class="member_big">
            <dl>
                <dt>
                    <img width="180" height="180" src="imgs/user_HeadImages/<?php echo $member_img_url?>" />
                </dt>
                <dd class="name"><?php echo $member_name?></dd>
                <dd>帖子总计：<?php echo $contentNum?></dd>
                <?php
                if ($isUpdateFlag){
                    $randomStrKey = randomkeys(rand(200,500));
                    $randomStrVal = randomkeys(rand(200,500));
                    echo <<<Y
<dd>操作：<a target="_blank" href="/memberUpdateImg.php?{$randomStrKey}={$randomStrVal}&member_id={$member_id}">修改头像</a> | <a target="_blank" href="href=/memberUpdatePwd.php?{$randomStrKey}={$randomStrVal}&member_id={$member_id}">修改密码></a></dd>
Y;
                }
                ?>

            </dl>
            <div style="clear:both;"></div>
        </div>
    </div>
    <div style="clear:both;"></div>
</div>

<?php include_once "inc/bottom.inc.php"?>
