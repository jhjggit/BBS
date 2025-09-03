<?php
    include_once "inc/config.php";
    include_once "inc/mysql_tools.php";
    include_once "inc/tools.inc.php";

    if (!isset($_GET['father_id']) || !is_numeric($_GET['father_id'])){
        skip("/index.php",PIC_FAILED,"请正确访问！");
    }

    $link = new mysql_tools();

    $sql = <<<SQL
select * from loe_father_module where module_id = {$_GET['father_id']}
SQL;

    $f_res = $link->execute($sql);
    if (mysqli_num_rows($f_res) == 0){
        skip("/index.php",PIC_FAILED,"父板块不存在！");
    }

    $f_data = mysqli_fetch_array($f_res);

    //获取子版块的所有帖子之和
    $sql = <<<SQL
select * from loe_son_module where father_module_id = {$f_data['module_id']}
SQL;

    $s_res = $link->execute($sql);

    $s_content_sum = 0;
    $s_today_content = 0;
    $s_info_html = array();
    //定义sql的in()中的子版块id
    $s_ids = "";
    while ($s_data = mysqli_fetch_array($s_res)){
        $sql = <<<SQL
select * from loe_content where module_id = {$s_data['module_id']}
SQL;

        //将子版块信息组成HTML,并保存到数组中
        $s_info_html[] = <<<HTML
<a style="color: #333333" " href="/list_son.php?son_id={$s_data['module_id']}">{$s_data['module_name']}</a>&nbsp;&nbsp;
HTML;
;

        $s_ids .=  $s_data['module_id'] . ",";

        $s_content_sum += mysqli_num_rows($link->execute($sql));

        $sql = <<<SQL
select * from loe_content where module_id = {$s_data['module_id']} and datediff(`time`,current_date()) = 0
SQL;

        $s_today_content += mysqli_num_rows($link->execute($sql));
    }
    //去掉子ID的最后一位
    $s_ids = substr_replace($s_ids, "", -1);



    $infos['title'] = "父板块列表";
    $infos['css'] = array('/style/public.css','/style/list.css');
?>

<?php include_once "inc/header.inc.php"?>

<div id="position" class="auto">
    <a href="/index.php">首页</a> &gt; <a><?php echo $f_data['module_name']?></a>
</div>
<div id="main" class="auto">
    <div id="left">
        <div class="box_wrap">
            <h3><?php echo $f_data['module_name']?></h3>
            <div class="num">
                今日：<span><?php echo $s_today_content?></span>&nbsp;&nbsp;&nbsp;
                总帖：<span><?php echo $s_content_sum?></span>
                <div class="moderator"> 子版块：
                <?php
                foreach ($s_info_html as $s_info){
                    echo $s_info;
                }
                ?>
                </div>
            </div>
        </div>

        <div style="clear:both;"></div>
        <ul class="postsList">
            <?php
            //这里主要是展示子版块下的帖子信息
            //通过多表查询需要的信息
            $sql = <<<SQL
SELECT content.content_id,content.title,
content.time,content.look_times,member.member_name,member.image_url,member.member_uid,s_module.module_name,s_module.module_id as s_module_id
FROM loe_content content,loe_member member,loe_son_module s_module
WHERE content.module_id IN({$s_ids}) AND content.member_uid = member.member_uid AND
content.module_id = s_module.module_id ORDER BY content.content_id ASC
SQL;
            $select_info = page("loe_content",5,$_GET['page'],$f_data['module_id'],"father_id","/list_father.php");
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
                    <div class="titleWrap"><a href="/list_son.php?son_id={$data['s_module_id']}">[{$data['module_name']}]</a>&nbsp;&nbsp;<h2><a href="/content.php?content_id={$data['content_id']}&page=1">{$data['title']}</a></h2></div>
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
                <?php
                echo $select_info['html'];
                ?>
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
