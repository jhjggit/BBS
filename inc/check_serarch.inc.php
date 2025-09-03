<?php
    if (!isset($_GET['keyword']) || empty($_GET['keyword'])){
        skip_error("请输入关键字！");
    }