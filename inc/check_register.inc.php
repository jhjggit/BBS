<?php
    //检查注册
    switch ($check_flag){
        case "reg":
            //检查用户名
            if (empty($_POST['username'])){
                jump_info_error("用户名不得为空！");
            }
            if (mb_strlen($_POST['username']) > 64){
                jump_info_error("用户名长度错误！");
            }



            //检查密码
            if (empty($_POST['passwd']) || empty($_POST['ensure_passwd'])){
                jump_info_error("请设置密码！");
            }

//            //使用正则表达式进行验证
//            $exp = "/^[A-Za-z0-9]+$";
//            if (preg_match($exp,)){
//
//            }

            //若两次密码不相同
            if (!($_POST['passwd'] == $_POST['ensure_passwd'])){
                jump_info_error("两次密码不一致！");
            }

            //若密码长度错误
            if (mb_strlen($_POST['passwd']) < 6 || mb_strlen($_POST['passwd']) > 32){
                jump_info_error("密码长度错误！");
            }

            //验证码的检查
            if (empty($_POST['check_code'])){
                jump_info_error("请输入验证码！");
            }

            //判断验证码是否正确
            //验证码存在了 SESSION 中
            if (!(strcasecmp($_POST['check_code'],$_SESSION['check_code']) == 0)){
                jump_info_error("验证码错误！");
            }
            break;
    }

    function jump_info_error($jump_info){
        skip($_SERVER['HTTP_REFERER'],PIC_FAILED,$jump_info);
    }