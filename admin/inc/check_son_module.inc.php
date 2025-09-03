<?php

    switch ($check_flag){
        case "add":
            //验证数据
            if ((strlen($_POST['module_name']) / 3 == 0 || strlen($_POST['sort']) == 0 || !is_numeric($_POST['sort']) || strlen($_POST['module_name']) / 3 > 66)
                || (strlen($_POST['module_info']) / 3 > 255)  || !isset($_POST['module_name']) || !isset($_POST['father_module_id']) || !isset($_POST['manager_id']) || !is_numeric($_POST['manager_id'])){
                skip($_SERVER['REQUEST_URI'],PIC_FAILED,"填入数据出错！");
            }

            //如果没有选择任何父板块
            if ($_POST['father_module_id'] == "0"){
                skip($_SERVER['REQUEST_URI'],PIC_FAILED,"未选择父板块！");
            }

            //验证父板块是否存在
            if (!check_module_exist(FATHER_MODULE,$_POST['father_module_id'])){
                skip($_SERVER['REQUEST_URI'],PIC_FAILED,"父板块不存在！");
            }

            //验证版主是否存在 暂不验证,未建表
            //if (!check_user_exist())
            break;
        case "update":
            //判断数据是否有变化
            if ($_POST['update_name'] == $son_data["module_name"] && $_POST['update_sort'] == $son_data["sort"]
                && $_POST['update_father_id'] == $son_data["father_module_id"] && $_POST['update_info'] == $son_data["info"] &&
                $_POST['update_manager_id'] == $son_data["manager_member_id"]){
                skip($_SERVER['REQUEST_URI'],PIC_FAILED,"数据无变化！");
            }

            //验证数据格式
            if ((strlen($_POST['update_name']) / 3 == 0 || strlen($_POST['update_sort']) == 0 || !is_numeric($_POST['update_sort']) || strlen($_POST['update_name']) / 3 > 66)
                || strlen($_POST['update_info']) / 3 > 255 || strlen($_POST['update_info']) / 3 ==0 || !is_numeric($_POST['update_father_id'])){
                skip($_SERVER['REQUEST_URI'],PIC_FAILED,"数据填写有误！");
            }

            //验证父板块是否存在
            if (!check_module_exist(FATHER_MODULE,$_POST['update_father_id'])){
                skip($_SERVER['REQUEST_URI'],PIC_FAILED,"父板块不存在！");
            }

            //验证版主是否存在 暂不验证,未建表
            //if (!check_user_exist())
            break;
    }