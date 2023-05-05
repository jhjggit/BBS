<?php
    include_once "inc/config.php";
    include_once "inc/mysql_tools.php";
    include_once "inc/tools.inc.php";

    if (!isset($_GET['son_id']) || !is_numeric($_GET['son_id'])){
        skip("/index.php",PIC_FAILED,"请正确访问！");
    }

    $link = new mysql_tools();

    $sql = <<<SQL
select * from loe_son_module where module_id = {$_GET['son_id']}
SQL;

    $s_res = $link->execute($sql);
    if (mysqli_num_rows($s_res) == 0){
        skip("/index.php",PIC_FAILED,"子板块不存在！");
    }

    $s_data = mysqli_fetch_array($s_res);

    //获取子版块今日新帖和总帖数量
    $s_today_num =  0;
    $s_all_count = 0;
    $sql = <<<SQL
select * from loe_content where module_id = {$s_data['module_id']} and datediff(`time`,current_date()) = 0
SQL;
    $s_today_num = mysqli_num_rows($link->execute($sql));
    $sql =  <<<SQL
select count(*) as all_content from loe_content where module_id = {$s_data['module_id']}
SQL;
    $s_all_count = mysqli_fetch_array($link->execute($sql))['all_content'];

    //获取其父板块数据
    $sql = <<<SQL
select * from loe_father_module where module_id = {$s_data['father_module_id']}
SQL;
    $f_data = mysqli_fetch_array($link->execute($sql));

    //获取该板块的版主
    $sql = <<<SQL
select * from loe_member where member_uid = (select manager_member_id from loe_son_module where module_id = {$s_data['module_id']})
SQL;

    $module_manager = mysqli_fetch_array($link->execute($sql));

    //获取信息
    $son_id = $s_data['module_id'];
    $son_name = $s_data['module_name'];
    $father_name = $f_data['module_name'];
    $father_id = $f_data['module_id'];

    $infos['title'] = "子板块列表";
    $infos['css'] = array('/style/public.css','/style/list.css');
?>
<?php include_once "inc/header.inc.php"?>
<div id="position" class="auto">
    <a href="/index.php">首页</a> &gt; <a href="/list_father.php?father_id=<?php echo $f_data['module_id']?>"><?php echo $f_data['module_name']?></a> &gt; <a><?php echo $s_data['module_name']?></a>
</div>
<div id="main" class="auto">
    <div id="left">
        <div class="box_wrap">
            <h3><?php echo $son_name?></h3>
            <div class="num">
                今日：<span><?php echo $s_today_num?></span>&nbsp;&nbsp;&nbsp;
                总帖：<span><?php echo $s_all_count?></span>
            </div>
            <div class="moderator">版主：<span><?php echo $module_manager['member_name']?></span></div>
            <div class="notice"><?php echo $s_data['info']?></div>

        </div>
        <div style="clear:both;"></div>
        <ul class="postsList">
            <?php
            //这里主要是展示子版块下的帖子信息
            //通过多表查询需要的信息
            $sql = <<<SQL
SELECT content.content_id,content.title,
content.time,content.look_times,member.member_name,member.image_url,member.member_uid
FROM loe_content AS content,loe_member AS member
WHERE content.member_uid = member.member_uid AND content.module_id = {$son_id} ORDER BY content.content_id ASC
SQL;

            $select_info = page("loe_content",5,$_GET['page'],$son_id,"son_id","/list_son.php",true);
            $sql .= " " . $select_info['limited'];

            $res = $link->execute($sql);
            while ($data = mysqli_fetch_array($res)){
                $send_time = substr($data['time'],0,10);
                $data['title'] = htmlspecialchars($data['title']);

                //获取回复相关
                $sql = <<<SQL
select count(*) as reply_num from loe_reply where content_id = {$data['content_id']}
SQL;
                //回复的总数
                $reply_num = mysqli_fetch_array($link->execute($sql))['reply_num'];

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
                        <img width="45" height="45"src="imgs/user_HeadImages/{$data['image_url']}">
                    </a>
                </div>
                <div class="subject">
                    <div class="titleWrap"><a href="#">[*]</a>&nbsp;<h2><a href="/content.php?content_id={$data['content_id']}&page=1">{$data['title']}</a></h2></div>
                    <p>
                        楼主：<a style="color: #b1aeae" href="./member.php?member_id={$data['member_uid']}">{$data['member_name']}</a>&nbsp;{$send_time}&nbsp;&nbsp;&nbsp;&nbsp;最后回复：{$last_reply_time}
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
        <div class="pages_wrap">
            <a class="btn publish" href="/publish.php"></a>
            <div class="pages">
                <?php echo $select_info['html']?>
            </div>
            <div style="clear:both;"></div>
        </div>
    </div>
    <div id="right">
        <div class="classList">
            <div class="title">版块列表</div>
            <ul class="listWrap">
                <?php
                $sql = <<<SQL
SELECT * FROM loe_father_module
SQL;
                $father_res = $link->execute($sql);
                echo "<li>";
                while ($father_data =  mysqli_fetch_array($father_res)){
                echo <<<HTML
<h2><a href="/list_father.php?father_id={$father_data['module_id']}">{$father_data['module_name']}</a></h2>
HTML;
                ?>
                <ul>
                    <?php
                    $sql = <<<SQL
select * from loe_son_module where father_module_id = {$father_data['module_id']}
SQL;
                    $son_res = $link->execute($sql);
                    while ($son_data = mysqli_fetch_array($son_res)){
                        echo <<<HTML
<li><h3><a href="/list_son.php?son_id={$son_data['module_id']}">{$son_data['module_name']}</a></h3></li>
HTML;
                    }
                    }
                    echo "</ul>";
                    echo "</li>";

                    ?>
                </ul>
        </div>
    </div>
    <div style="clear:both;"></div>
</div>
<?php include_once "inc/bottom.inc.php"?>

