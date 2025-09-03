<?php
    include_once "../inc/config.php";
    include_once "../inc/mysql_tools.php";
    include_once "../inc/tools.inc.php";
    if (isset($_POST['submit'])){

        //引入检查脚本
        include_once "./inc/check_login.inc.php";
        $link = new mysql_tools();


        $_POST = $link->escape($_POST);

        //组织sql,查询
        $sql = <<<SQL
select * from loe_manage where manager_name = "{$_POST['username']}"
SQL;

        $res = $link->execute($sql);

        if (mysqli_num_rows($res) == 0){
            skip_error("没有此用户！");
        }

        $data = mysqli_fetch_array($res);


        if (!(strcasecmp($data['password'],md5($_POST['password'])) == 0)){
            skip("/admin/login.php",PIC_FAILED,"密码错误！！！");
        }else{
            //设置SESSION
            $_SESSION['manage']['name'] = $data['manager_name'];
            $_SESSION['manage']['passwd'] = $data['password'];
            $_SESSION['manage']['id'] = $data['manager_id'];
            $_SESSION['manage']['level'] = $data['level'];

            skip("/admin/father_module.php",PIC_SUCCESS,"登录成功！");
        }

    }




?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登录页</title>
    <style>
        * {
            margin: 0;
            padding: 0;
        }
        html {
            height: 100%;
        }
        body {
            height: 100%;
        }
        .container {
            height: 100%;
            background-image: linear-gradient(to right, #fbc2eb, #a6c1ee);
        }
        .login-wrapper {
            background-color: #fff;
            width: 358px;
            height: 588px;
            border-radius: 15px;
            padding: 0 50px;
            position: relative;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
        }
        .header {
            font-size: 38px;
            font-weight: bold;
            text-align: center;
            line-height: 200px;
        }
        .input-item {
            display: block;
            width: 100%;
            margin-bottom: 20px;
            border: 0;
            padding: 10px;
            border-bottom: 1px solid rgb(128, 125, 125);
            font-size: 15px;
            outline: none;
        }
        .input-item:placeholder {
            text-transform: uppercase;
        }
        .btn {
            text-align: center;
            padding: 10px;
            width: 100%;
            margin-top: 40px;
            background-image: linear-gradient(to right, #a6c1ee, #fbc2eb);
            color: #fff;
        }
        .msg {
            text-align: center;
            line-height: 88px;
        }
        a {
            text-decoration-line: none;
            color: #abc1ee;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="login-wrapper">
        <div class="header">登录后台</div>
        <div class="form-wrapper">
            <form method="post">
                <input type="text" name="username" placeholder="用户名" class="input-item">
                <input type="password" name="password" placeholder="密码" class="input-item">
                <input type="text" name="checkcode" placeholder="输入下方验证码" class="input-item">
                <img src="../inc/show_code.inc.php" alt="验证码">
                <input class="btn" type="submit" name="submit" value="登录">
            </form>
        </div>
    </div>
</div>
</body>
</html>
<>
