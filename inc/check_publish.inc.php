<?php
    //获取子版块id
    $son_module_id = $_POST['son_module_id'];

    if (empty($son_module_id) || !is_numeric($son_module_id)){
        jump_info_error("子版块ID错误！");
    }

    $link = new mysql_tools();

    if (!($link->execute("select * from loe_son_module where module_id = {$son_module_id}")->num_rows == 1)){
        jump_info_error("子版块不存在！");
    }

    $title = $_POST['title'];
    if (empty($son_module_id)){
        jump_info_error("请输入帖子标题！");
    }

    if (mb_strlen($title) == 0){
        jump_info_error("帖子标题长度过短！");
    }

    if (mb_strlen($title) > 128){
        jump_info_error("帖子标题长度过长！不能超过128个字符");
    }



    function jump_info_error($jump_info){
        skip("/publish.php",PIC_FAILED,$jump_info);
    }