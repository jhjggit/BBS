<?php
    include_once "./inc/config.php";
    include_once "./inc/mysql_tools.php";
    include_once "./inc/tools.inc.php";

    //引入检查脚本
    $flag = "你没有权力删除该帖子";
    include_once "inc/check_contentUpdate.inc.php";

    $infos['title'] = "删除帖子";
?>

<?php
    //处理GET
    if (isset($_GET['flag'])){
        //如果恶意篡改content_id
        $sql = <<<SQL
delete from loe_content where content_id = {$_GET['content_id']}
SQL;
        if ($link->execute_bool($sql)) {
            skip_success("删除成功！");
        }
    }
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8" />
    <title>确认删除 <?php echo $contentData['title']?> 帖子?</title>
    <meta name="keywords" content="确认页面" />
    <meta name="description" content="确认页面" />
    <link rel="stylesheet" type="text/css" href="/style/remind.css" />
</head>
<body>
<div class="notice">
    <br>
    确认删除 <span style="color:#ff5500 "><?php echo $contentData['title']?></span> 帖子?
    <br>
    <a style="color: red" href="<?php echo '/contentDelete.php?'.randomkeys(rand(30,40)).'='.randomkeys(rand(100,400))."&flag=t&content_id={$contentData['content_id']}" ?>">确定</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a style="color: green" href="/index.php">取消</a>
</div>
</body>
</html>
