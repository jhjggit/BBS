<?php
    include_once "inc/config.php";
    include_once "inc/mysql_tools.php";
    include_once "inc/tools.inc.php";

    if (is_login(new mysql_tools())){
        skip("/index.php",PIC_FAILED,"您已登录！");
    }

    //用户注册
    if (isset($_POST['submit'])){
        //引入检查脚本
        $check_flag = "reg";
        include_once "inc/check_register.inc.php";

        //链接数据库进行查询
        $link = new mysql_tools();
        //转义
        $_POST = $link->escape($_POST);

        $sql = <<<SQL
select * from loe_member where member_name = "{$_POST['username']}"
SQL;
        //如果用户已经存在
        if ($link->execute($sql)->num_rows == 1){
            skip($_SERVER['HTTP_REFERER'],PIC_FAILED,"用户已存在！");
        }

        //组织sql
         $table = MEMBER_TABLE;
        $sql = <<<SQL
INSERT INTO {$table}(`member_name`,`password`) VALUES("{$_POST['username']}",MD5("{$_POST['passwd']}"));
SQL;

        //执行sql
        if ($link->execute_bool($sql) && mysqli_affected_rows($link->connect) == 1){
            //注册成功,将用户信息存入cookie
            setcookie("loe_info[name]",$_POST['username']);
            setcookie("loe_info[passwd]",sha1(md5($_POST['passwd'])));
            setcookie("loe_info[member_uid]",mysqli_fetch_array($link->execute("select member_uid from loe_member where member_name = '{$_POST['username']}'"))['member_uid']);
            skip("index.php",PIC_SUCCESS,"注册用户成功！正在跳转....");
        }else{
            skip($_SERVER['HTTP_REFERER'],PIC_FAILED,"注册失败！");
        }

    }

?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8" />
    <title></title>
    <meta name="keywords" content="" />
    <meta name="description" content="" />
    <link rel="stylesheet" type="text/css" href="/style/public.css" />
    <link rel="stylesheet" type="text/css" href="/style/register.css" />
</head>
<body>
<div class="header_wrap">
    <div id="header" class="auto">
        <div class="logo">Loe-BBS</div>
        <div class="nav">
            <a class="hover" href="<?php echo INDEX_PAGE?>">首页</a>
            <a>新帖</a>
            <a>话题</a>
        </div>
        <div class="serarch">
            <form>
                <input class="keyword" type="text" name="keyword" placeholder="搜索其实很简单" />
                <input class="submit" type="submit" name="submit" value="" />
            </form>
        </div>
        <div class="login">
            <a href="<?php echo LOGIN_PAGE?>">登录</a>&nbsp;
            <a href="#">注册</a>
        </div>
    </div>
</div>
<div style="margin-top:55px;"></div>
<div id="register" class="auto">
    <h2>欢迎注册成为 Loe-BBS会员</h2>
    <form method="post">
        <label>用户名：<input type="text" name="username" /><span>*用户名不得为空, 并且长度不得超过64个字符</span></label>
        <label>密码：<input type="password" name="passwd" /><span>*密码长度不得低于6位, 不得高于32位</span></label>
        <label>确认密码：<input type="password" name="ensure_passwd" /><span>*请保证密码一致！</span></label>
        <label>验证码：<input type="text" name="check_code"/><span>*请输入下方验证码</span></label>
        <img class="vcode" src="inc/show_code.inc.php" />
        <div style="clear:both;"></div>
        <input class="btn" type="submit" name="submit" value="确定注册" />
    </form>
</div>
<div id="footer" class="auto">
    <div class="bottom">
        <a>Loe-BBS</a>
    </div>
    <div class="copyright">Powered by Loe ©2023 loe-bbs.com</div>
</div>
</body>
</html>
