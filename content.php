<?php
    include_once "inc/config.php";
    include_once "inc/mysql_tools.php";
    include_once "inc/tools.inc.php";
    if (!isset($_GET['content_id']) || !is_numeric($_GET['content_id'])){
        skip("/index.php",PIC_FAILED,"非法访问！");
    }

    if (!isset($_GET['page']) || !is_numeric($_GET['page']) || $_GET['page'] < 1){
        $_GET['page'] = 1;
    }

    $content_id = $_GET['content_id'];
    $link = new mysql_tools();

    if (mysqli_num_rows($link->execute("select * from loe_content where content_id = {$content_id}")) == 0){;
        skip("/index.php",PIC_FAILED,"帖子不存在！请联系管理员");
    }

    $infos['title'] = "帖子详细";
    $infos['css'] = array('/style/public.css','/style/show.css');
?>

<?php
/**
 * 从数据库查询帖子信息与其子版块、父板块信息、发帖人信息
 */
    $sql = <<<SQL
select * from loe_content where content_id = {$content_id}
SQL;

    if (mysqli_num_rows(($content_res = $link->execute($sql))) == 0){
        skip("/index.php",PIC_SUCCESS,"该帖子不存在！");
    }

    //帖子数据
    $content_data = mysqli_fetch_array($content_res);
    $content_data['title'] = htmlspecialchars($content_data['title']);
    $content_data['content'] = nl2br(htmlspecialchars($content_data['content']));

    //获取该帖子的回复数量
    $sql = <<<SQL
SELECT count(*) as reply_num FROM loe_reply where content_id = {$content_data['content_id']}
SQL;
    $reply_num = mysqli_fetch_array($link->execute($sql))['reply_num'];

    //增加一次帖子的浏览次数
    $look_times = $content_data['look_times'] + 1;
    $sql = <<<SQL
update loe_content set look_times = {$look_times} where content_id = {$content_data['content_id']}
SQL;
    if (!($link->execute_bool($sql))){
        skip('/index.php',PIC_FAILED,"错误！Error-id = 1");
    }

    $sql = <<<SQL
select *  from loe_son_module where module_id = {$content_data['module_id']}
SQL;

    //帖子对应的子版块数据
    $son_data = mysqli_fetch_array($link->execute($sql));

    $sql = <<<SQL
select * from loe_father_module where module_id = {$son_data['father_module_id']}
SQL;

    //对应的父板块数据
    $father_data = mysqli_fetch_array($link->execute($sql));

    //发帖人信息
    $sql = <<<SQL
select * from loe_member where member_uid = {$content_data['member_uid']}
SQL;
    $member_data = mysqli_fetch_array($link->execute($sql));

?>

<?php include_once "./inc/header.inc.php"?>
<div id="position" class="auto">
    <a href="/index.php">首页</a> &gt; <a href="/list_father.php?father_id=<?php echo $father_data['module_id']?>"><?php echo $father_data['module_name']?></a> &gt; <a href="/list_son.php?son_id=<?php echo $son_data['module_id']?>"><?php echo $son_data['module_name']?></a> &gt; <?php echo $content_data['title']?>
</div>
<div id="main" class="auto">
    <br>
    <?php
        if ($_GET['page'] == 1){
           echo <<<HTML
<div class="wrapContent">
        <div class="left">
            <div class="face">
                <a target="_blank" href="">
                    <img style="width: 120px; height: 120px" src="imgs/user_HeadImages/{$member_data['image_url']}" />
                </a>
            </div>
            <div class="name">
                <a href="">{$member_data['member_name']}</a>
            </div>
        </div>
        <div class="right">
            <div class="title">
                <h2>{$content_data['title']}</h2>
                <span>阅读：{$content_data['look_times']}&nbsp;|&nbsp;回复：{$reply_num}</span>
                <div style="clear:both;"></div>
            </div>
            <div class="pubdate">
                <span class="date">发布于：{$content_data['time']} </span>
                <span class="floor" style="color:red;font-size:14px;font-weight:bold;">楼主</span>
            </div>
            <div class="content">
                {$content_data['content']}
            </div>
        </div>
        <div style="clear:both;"></div>
    </div>
HTML;
        }
    ?>

    <?php
        //分页限制
        //每页几条数据
        $page_nums = 5;
        //计算从哪里开始
        $start = ($_GET['page'] - 1) * $page_nums;
        //获取该帖子一共有多少回复
        $sql = <<<SQL
select count(*) as content_reply_num from loe_reply where content_id = {$content_id}
SQL;
        $content_reply_num = mysqli_fetch_array($link->execute($sql))['content_reply_num'];
        $max_page_num = floor($content_reply_num /  $page_nums);

        //如果当前页码大于最大码数
        if ($_GET['page'] > $max_page_num && $max_page_num > 0){
            skip("/index.php",PIC_FAILED,"页码超过最大范围！");
        }

        //通过验证,设置sql
        $sql = <<<SQL
select * from loe_reply where content_id = {$content_id} order by time limit {$start},{$page_nums}
SQL;

        $reply_res = $link->execute($sql);
        $i = 1;
        while (($reply_data = mysqli_fetch_array($reply_res))){

            $sql = <<<SQL
select member.member_name as member_name,
       member.image_url as head_img,
       member.member_uid  as member_uid
       from loe_member as member where member.member_uid = {$reply_data['member_uid']}
SQL;

            $member_data = mysqli_fetch_array($link->execute($sql));
            //如果是有回复的
            if ($reply_data['quote_id'] == 0){
                $reply_html = $reply_data['reply_content'];
            }else{
                $sql = <<<SQL
select * from loe_reply where reply_id = {$reply_data['quote_id']}
SQL;
                $quota_data = mysqli_fetch_array($link->execute($sql));

                $sql = <<<SQL
select * from loe_member where member_uid = {$quota_data['member_uid']}
SQL;
                $queto_member_data = mysqli_fetch_array($link->execute($sql));
                $reply_html = <<<HTML
<div class="quote">
<h2>引用 {$queto_member_data['member_name']} 发表的: </h2>
{$quota_data['reply_content']}
</div>
<br>    
{$reply_data['reply_content']}
HTML;

            }

            $html = <<<HTML
<div class="wrapContent">
        <div class="left">
            <div class="face">
                <a target="_blank" href="">
                    <img style="width: 120px; height: 120px" src="imgs/user_HeadImages/{$member_data['head_img']}" />
                </a>
            </div>
            <div class="name">
                <a href="">{$member_data['member_name']}</a>
            </div>
        </div>
        <div class="right">

            <div class="pubdate">
                <span class="date">回复时间：{$reply_data['time']}</span>
                <span class="floor">{$i}楼&nbsp;|&nbsp;<a href="/quote.php?content_id={$content_data['content_id']}&reply_id={$reply_data['reply_id']}&member_id={$_COOKIE['loe_info']['uid']}&floor={$i}">引用</a></span>
            </div>
            <div class="content">
                {$reply_html}
            </div>
        </div>
        <div style="clear:both;"></div>
    </div>
HTML;
        echo $html;
        $i++;
        }

    ?>

    <div class="wrap1">
        <div class="pages">
            <?php
            if (!($_GET['page'] == 1 || $_GET['page'] == 2 || $_GET['page'] == 3)){
                $new_page = $_GET['page'] - 1;
                echo "<a href='content.php?content_id={$content_id}&page={$new_page}'>« 上一页</a>";
            }
            ?>
            <?php

            if ($_GET['page'] == 1 || $_GET['page'] == 2 || $_GET['page'] == 3){
                if ($max_page_num == 0 || $max_page_num == 1){
                    echo "<span>1</span>";
                }else{
                    if ($max_page_num > 3){
                        $times = 3;
                    }else{
                        $times = $max_page_num;
                    }
                    for ($i = 1; $i <= $times; $i++){//如果是当前页码
                        if ($i == $_GET['page']){
                            echo "<span>$i</span>";
                        }else{
                            echo "<a href='content.php?content_id={$content_id}&page={$i}'>$i</a>";
                        }
                    }
                }
            }elseif ($_GET['page'] == $max_page_num) {
                echo "<a href='content.php?content_id={$content_id}&page=1'>1...</a>";
                for ($i = $max_page_num - 3; $i <= $max_page_num; $i++) {
                    //如果是当前页码
                    if ($i == $max_page_num) {
                        echo "<span>{$i}</span>";
                    } else {
                        echo "<a href='content.php?content_id={$content_id}&page={$i}'>$i</a>";
                    }
                }
            }else {
                if (($back_page = $_GET['page'] + 3) > $max_page_num){
                    $back_page = $max_page_num;
                }
                for ($i = $_GET['page'] - 3; $i <= $back_page; $i++){
                    //如果是当前页码
                    if ($i == $_GET['page']){
                        echo "<span>{$i}</span>";
                    }else{
                        "<a href='content.php?content_id={$content_id}&page={$i}'>$i</a>";
                    }
                }
            }
            if (!($max_page_num == 0)){
                echo "<a href='content.php?content_id={$content_id}&page={$max_page_num}'>...{$max_page_num}</a>";
            }
            ?>
            <?php
                if (!($_GET['page'] == $max_page_num)){
                    $pages = $_GET['page'] + 1;
                    echo "<a href='content.php?content_id={$content_id}&page={$pages}'>下一页</a>";
                }
            ?>
        </div>
        <a class="btn reply" href="/reply.php?content_id=<?php echo $content_data['content_id']?>"></a>
        <div style="clear:both;"></div>
    </div>
</div>
<?php include_once "inc/bottom.inc.php"?>

