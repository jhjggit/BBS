<?php
    include_once "inc/config.php";
    include_once "inc/mysql_tools.php";
    include_once "inc/tools.inc.php";

    $link = new mysql_tools();
    $infos['title'] = "首页";
    $infos['css'] = array('/style/public.css','/style/index.css');




?>
<?php include_once "inc/header.inc.php"?>
<div id="hot" class="auto">
    <div class="title">热门动态</div>
    <ul class="newlist">
        <!-- 20条 -->
        <li><a href="#">[库队]</a> <a href="#">私房库实战项目录制中...</a></li>

    </ul>
    <div style="clear:both;"></div>
</div>

<?php
    $sql = <<<SQL
select * from loe_father_module order by sort desc
SQL;

    $f_res = $link->execute($sql);

    while ($f_data = mysqli_fetch_array($f_res)){
        $s_res = $link->execute("select * from loe_son_module where father_module_id = {$f_data['module_id']}");
        $s_num = mysqli_num_rows($s_res);
        echo <<<HTML
<div class="box auto">
    <div class="title">
        <a style="color: #105cb6" href="/list_father.php?father_id={$f_data['module_id']}&page=0">{$f_data['module_name']}</a>>
    </div>
HTML;
        if ($s_num == 0){
            echo <<<HTML
    <div class="classList">
            <div style="padding:10px 0;">暂无子版块...</div>
            <div style="clear:both;"></div>
    </div>
HTML;
        }else{
            echo <<<HTML
<div class="classList">
HTML;
            while ($s_data = mysqli_fetch_array($s_res)){
                $type_flag = "new";
                $s_module_id = $s_data['module_id'];
                $s_content_num = mysqli_num_rows($link->execute("select * from loe_content where module_id = {$s_module_id}"));
                $s_today_content_num = mysqli_num_rows($link->execute("select * from loe_content where module_id = {$s_module_id} and datediff(`time`,current_date()) = 0"));

                $html = <<<HTML
<div class="childBox {$type_flag}">
            <h2><a href="/list_son.php?son_id={$s_data['module_id']}">{$s_data['module_name']}</a> <span>(今日{$s_today_content_num})</span></h2>
            帖子：{$s_content_num}<br />
</div>
HTML;
                echo $html;
                echo <<<HTML
HTML;
            }
            echo <<<HTML
<div style="clear:both;"></div>
</div>
HTML;
        }


        echo "</div>";
    }

?>
<?php include_once "inc/bottom.inc.php"?>