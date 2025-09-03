<?php
    include_once "inc/config.php";
    include_once "inc/mysql_tools.php";
    include_once "inc/tools.inc.php";

    $link = new mysql_tools();

    //验证实现
    if (!($member_id = is_login($link))){
        skip("/login.php",PIC_FAILED,"请先登录！");
    }

    if (!isset($_GET['content_id']) || !is_numeric($_GET['content_id'])){
        skip("/index.php",PIC_FAILED,"非法访问！");
    }

    $sql = <<<SQL
select * from loe_content where content_id = {$_GET['content_id']}
SQL;

    if (mysqli_num_rows(($content_res = $link->execute($sql))) == 0){
        skip("/index.php",PIC_SUCCESS,"该帖子不存在！");
    }

    $content_id = $_GET['content_id'];

    $infos['title'] = "回复帖子";
    $infos['css'] = array('/style/public.css','/style/publish.css');
?>
<?php
    //处理POST请求
    if (isset($_POST['submit'])){
        include_once "inc/check_reply.inc.php";
        $link =  new mysql_tools();
        $_POST = $link->escape($_POST);

        $sql = <<<SQL
INSERT INTO `loe_reply`(`content_id`,`reply_content`,`member_uid`) VALUE({$_GET['content_id']},"{$_POST['content']}",{$member_id})
SQL;

        if (!$link->execute_bool($sql)){
            skip("/index.php",PIC_FAILED,"Wrong! all fu*king wrong!!!!!!");
        }else{
            $jmp = "/content.php?content_id=$content_id";
            skip($jmp,PIC_SUCCESS,"回复成功！");
        }
    }
?>
<?php
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
where content.content_id = {$content_id} and content.member_uid = member.member_uid and content.module_id = son.module_id and son.father_module_id = father.module_id
SQL;

    $reply_data = mysqli_fetch_array($link->execute($sql));

?>

<?php include_once "inc/header.inc.php"?>
    <div id="position" class="auto">
        <a href="/index.php">首页</a> &gt; <a href="/list_father.php?father_id=<?php echo $reply_data['father_id']?>"><?php echo $reply_data['father_name']?></a> &gt; <a href="/list_son.php?son_id=<?php echo $reply_data['son_id']?>"><?php echo $reply_data['son_name']?></a> &gt; <a href="/content.php?content_id=<?php echo $reply_data['content_id']?>"><?php echo $reply_data['title']?></a>
    </div>
    <div id="publish">
        回复：由 <?php echo $reply_data['member_name']?> 发布的 <?php echo $reply_data['title']?>
        <form method="post">
            <textarea name="content" class="content"></textarea>
            <input class="reply" type="submit" name="submit" value="" />
            <div style="clear:both;"></div>
        </form>
    </div>
<?php include_once "inc/bottom.inc.php"?>