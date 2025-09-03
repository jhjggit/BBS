<?php
    // 检查reply数据
    if (mb_strlen($_POST['content']) < 10){
        $link = "/reply.php?content_id=".$_GET['content_id'];
        skip($link,PIC_FAILED,"回复内容最少10个字符！");
    }