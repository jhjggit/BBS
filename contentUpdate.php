<?php
    include_once "inc/config.php";
    include_once "inc/mysql_tools.php";
    include_once "inc/tools.inc.php";

    $infos['title'] = "修改帖子";
    $infos['css'] = array('/style/public.css','/style/publish.css');
?>

<?php
    //引入检查脚本
    //通过验证,在检查脚本中已经获得数据库中的 帖子数据 与 用户数据
    //设置flag
     $flag = "你没有权力对该帖子进行操作！";
    include_once "inc/check_contentUpdate.inc.php";
?>

<?php
    //处理POST请求
    if (isset($_POST['submit'])){

        //如果帖子和之前一样
        if (strcasecmp($_POST['title'],$contentData['title']) == 0 && strcasecmp($_POST['content'],$contentData['content']) == 0) {
            skip_error("内容没有变化");
        }

        $_POST = $link->escape($_POST);
        $_POST['title'] = htmlspecialchars($_POST['title']);
        $_POST['content'] = htmlspecialchars($_POST['content']);

        $sql = <<<SQL
update loe_content set title = "{$_POST['title']}",content = "{$_POST['content']}" where content_id = {$contentData['content_id']}
SQL;
        if ($link->execute_bool($sql)){
            skip("/content.php?content_id={$contentData['content_id']}",PIC_SUCCESS,"更改成功！正在跳转");
        }else{
            skip_error("修改失败！正在跳转！");
        }
    }
?>

<?php include_once "inc/header.inc.php"?>

    <div id="position" class="auto">
        <a href="/index.php">首页</a> &gt; 修改帖子
    </div>
    <div id="publish">
        <form method="post">
            <div style="margin-top:10px;"></div>
            <input class="title" value="<?php echo $contentData['title']?>" name="title" type="text" />
            <textarea name="content" class="content"><?php echo $contentData['content']?></textarea>
            <input style="" type="submit" name="submit" value="更改" />
            <div style="clear:both;"></div>
        </form>
    </div>
<?php include_once "inc/bottom.inc.php"?>