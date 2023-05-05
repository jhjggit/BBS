<?php
    include_once "inc/config.php";
    include_once "inc/mysql_tools.php";
    include_once "inc/tools.inc.php";

    //引入检查脚本
    include "inc/check_quote.inc.php";

    //获取回复帖子的ID
    $reply_id = $_GET['reply_id'];
    $sql = <<<SQL
select * from loe_member where member_uid = (select member_uid from loe_reply where reply_id = {$reply_id})
SQL;
    $link = new mysql_tools();
    $source_reply_member_data = mysqli_fetch_array($link->execute($sql));

    $infos['title'] = "回复 {$source_reply_member_data['member_name']}";
    $infos['css'] = array('/style/public.css','/style/publish.css');
?>

<?php
    //获取帖子对应父板块、子版块信息
    //获取对应帖子的信息
    $sql = <<<SQL
select 
       member.member_name as member_name,
       content.title as title,
       content.content_id as content_id,
       son.module_name as son_name,
       son.module_id as son_id,
       father.module_name as father_name,
       father.module_id as father_id
from loe_content as content,loe_member as member,loe_son_module as son,loe_father_module as father
where content.content_id = {$_GET['content_id']} and content.member_uid = member.member_uid and content.module_id = son.module_id and son.father_module_id = father.module_id
SQL;

    $position_data = mysqli_fetch_array($link->execute($sql));

    //获取reply帖子的数据
    $sql = <<<SQL
    select * from loe_reply where reply_id = {$_GET['reply_id']}
SQL;
    $current_reply_data = mysqli_fetch_array($link->execute($sql));
?>

<?php
    //处理POST
    if (isset($_POST['submit'])){
        //获取回复的内容,并判断
        $reply_content = $_POST['content'];

        if (mb_strlen($reply_content) == 256){
            skip_error("回复内容过长！");
        }

        //组织sql
        $sql = <<<SQL
insert into `loe_reply`(`content_id`,`quote_id`,`reply_content`,`member_uid`) value({$position_data['content_id']},{$_GET['reply_id']},"{$reply_content}",{$_GET['member_id']})
SQL;
        if ($link->execute_bool($sql)){
            skip("/content.php?content_id={$position_data['content_id']}&page=1",PIC_SUCCESS,"回复成功！正在跳转...");
        }else{
            skip_error("All goddamn fu*king wrong！！！！！！");
        }
    }
?>

<?php include_once "inc/header.inc.php"?>
<div id="position" class="auto">
    <a href="/index.php">首页</a> &gt; <a href="/list_father.php?father_id=<?php echo $position_data['father_id']?>"><?php echo $position_data['father_name']?></a> &gt; <a href="/list_son.php?son_id=<?php echo $position_data['son_id']?>"><?php echo $position_data['son_name']?></a> &gt; <a href="content.php?content_id=<?php echo $position_data['content_id']?>&page=1"><?php echo $position_data['title']?></a>
</div>
<div id="publish">

    <div class="quote">
        <p class="title">引用<?php echo $_GET['floor']?>楼 <?php echo $source_reply_member_data['member_name']?> 发表的: </p>
        <?php echo $current_reply_data['reply_content']?>
    </div>
    <form method="post">
        <textarea name="content" class="content"></textarea>
        <input class="reply" type="submit" name="submit" value="" />
        <div style="clear:both;"></div>
    </form>
</div>
<?php include_once "inc/bottom.inc.php"?>
