<?php
    if (!isset($_GET['member_id']) || !is_numeric($_GET['member_id'])){
        skip("/index.php",PIC_FAILED,"参数错误！");
    }