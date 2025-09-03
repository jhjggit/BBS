<?php
    include_once "inc/config.php";
    include_once "inc/mysql_tools.php";
    include_once "inc/tools.inc.php";

    if (is_login(new mysql_tools())){
        skip("/index.php",PIC_FAILED,"您已经登录了！");
    }

    if (isset($_POST['submit'])){
        $check_flag = "login";
        include_once "./inc/check_login.inc.php";

        $link = new mysql_tools();

        //检查用户是否存在
        $sql = <<<SQL
SELECT * FROM loe_member where member_name = "{$_POST['username']}"
SQL;

        if($link->execute($sql)->num_rows == 0){
            jump_info_error("用户不存在");
        }

        $data = mysqli_fetch_array($link->execute($sql));

        //若密码错误
        if (!($data['password'] == md5($_POST['passwd']))){
            jump_info_error("密码错误！");
        }

        if (empty($_POST['auto_login_time']) || !is_numeric($_POST['auto_login_time']) || $_POST['auto_login_time'] > 2592000){
            $_POST['auto_login_time'] = 2592000;
        }

        //登录成功, 跳转到首页
        $uid = $data['member_uid'];
        //设置cookie
        setcookie("loe_info[name]",$_POST['username'],time() + $_POST['auto_login_time']);
        setcookie("loe_info[passwd]",md5($_POST['passwd']),time() + $_POST['auto_login_time']);
        setcookie("loe_info[uid]",$uid,time() + $_POST['auto_login_time']);
        skip("/index.php?UID={$uid}",PIC_SUCCESS,"登录成功！");
    }

    $infos['title'] = "登录";
    $infos['css'] = array('/style/public.css','/style/register.css');

?>

<?php include_once "inc/header.inc.php"?>
<div id="register" class="auto">
    <h2>欢迎登录 Loe-BBS</h2>
    <form method="POST">
        <label>用户名：<input type="text"  name="username" /><span>*请输入正确的用户名</span></label>
        <label>密码：<input type="password" name="passwd" /><span>*请输入正确密码</span></label>
        <label>验证码：<input name="check_code" type="text"  /><span>*请输入下方验证码</span></label>
        <img class="vcode" src="./inc/show_code.inc.php" />
        <label>自动登录：
            <select style="width:236px;height:25px;" name="auto_login_time">
                <option value="3600">1小时内</option>
                <option value="86400">1天内</option>
                <option value="259200">3天内</option>
                <option value="2592000">30天内</option>
            </select>
            <span>*公共电脑上请勿长期自动登录</span>
        </label>
        <div style="clear:both;"></div>
        <input class="btn" type="submit" name="submit" value="登录" />
    </form>
</div>
<?php include_once "inc/bottom.inc.php"?>
