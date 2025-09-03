<?php
    include_once "inc/config.php";
    include_once "inc/mysql_tools.php";
    include_once "inc/tools.inc.php";

    $link = new mysql_tools();
    if (!($member_id = is_login($link))){
        skip("/login.php",PIC_FAILED,"请先登录！");
    }

    $_POST = $link->escape($_POST);

    //如果提交
    if (isset($_POST['submit'])){

        //数据验证脚本
        include_once "inc/check_publish.inc.php";

        $uid = $_COOKIE['loe_info']['uid'];
        $e_title = $link->escape($_POST['title']);
        $e_content = $link->escape($_POST['content']);
        $sql = <<<SQL
insert into loe_content(`module_id`,`title`,`content`,`member_uid`) value({$_POST['son_module_id']},"{$e_title}","{$e_content}",{$uid})
SQL;

        if ($link->execute_bool($sql)) {
            skip("/index.php",PIC_SUCCESS,"发布成功！");
        }
    }

    $infos['title'] = "发帖页面";
    $infos['css'] = array('/style/public.css','/style/publish.css');
?>

<?php include_once "inc/header.inc.php"?>

<div id="position" class="auto">
    <a href="/index.php">首页</a> &gt; 发布帖子
</div>
<div id="publish">
    <form method="post">
        <select name="son_module_id">
            <?php
            //查询父板块
                $sql = <<<SQL
select * from loe_father_module order by sort desc
SQL;
            $f_res = $link->execute($sql);

            while ($f_data = mysqli_fetch_array($f_res)){
                echo "<optgroup label='{$f_data['module_name']}'>";
                $sql = <<<SQL
select * from loe_son_module where father_module_id = {$f_data['module_id']} order by sort desc
SQL;
                $s_res = $link->execute($sql);
                while ($s_data = mysqli_fetch_array($s_res)){
                    echo <<<HTML
<option value="{$s_data['module_id']}">{$s_data['module_name']}</option>
HTML;

                }
                echo "</optgroup>";
            }
            ?>
        </select>
        <input class="title" placeholder="请输入标题" name="title" type="text" />
        <textarea name="content" class="content"></textarea>
        <input class="publish" type="submit" name="submit" value="" />
        <div style="clear:both;"></div>
    </form>
</div>
<?php include_once "inc/bottom.inc.php"?>
