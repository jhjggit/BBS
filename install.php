
<?php
    if(file_exists('inc/install.lock')){
        header("Location:index.php");
    }


    if (isset($_POST['submit'])){
        $link = mysqli_connect($_POST['db_host'],$_POST['db_user'],$_POST['db_pw'],$_POST['db_database'],$_POST['db_port']);



        //引入检查脚本
        include_once "inc/check_install.inc.php";
        //执行sql语句
        $sqls = array();

        $sqls['loe_father_module'] = <<<SQL
CREATE TABLE `loe_father_module` (
  `module_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `module_name` CHAR(66) NOT NULL,
  `sort` INT(11) DEFAULT '0',
  PRIMARY KEY (`module_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8
SQL;

        $sqls['loe_son_module'] = <<<SQL
CREATE TABLE `loe_son_module` (
  `module_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `father_module_id` INT(10) UNSIGNED NOT NULL,
  FOREIGN KEY (`father_module_id`) REFERENCES `loe_father_module`(`module_id`),
  `module_name` VARCHAR(66) NOT NULL DEFAULT 'Null Module name',
  `info` VARCHAR(255) NOT NULL DEFAULT 'Null Info',
  `manager_member_id` INT(11) NOT NULL,
  FOREIGN KEY (`manager_member_id`) REFERENCES loe_member(`member_uid`),
  `sort` INT(11) DEFAULT '0',
  PRIMARY KEY (`module_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8
SQL;

        $sqls['loe_member'] = <<<SQL
CREATE TABLE `loe_member` (
  `member_uid` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `member_name` VARCHAR(64) NOT NULL DEFAULT "Null",
  `password` CHAR(32) NOT NULL,
  `image_url` VARCHAR(64) NOT NULL DEFAULT "null_user_img.png",
  `register_time` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_time` DATETIME,
  PRIMARY KEY(`member_uid`)
)ENGINE=MYISAM AUTO_INCREMENT 100000 DEFAULT CHARSET=utf8
SQL;

        $sqls['loe_content'] = <<<SQL
CREATE TABLE `loe_content`(
	`content_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`module_id` INT UNSIGNED NOT NULL,
	FOREIGN KEY (`module_id`) REFERENCES `loe_son_module`(`module_id`),
	`title` VARCHAR(128) NOT NULL DEFAULT "",
	`content` TEXT NOT NULL,
	`time` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`member_uid` INT UNSIGNED NOT NULL,
	FOREIGN KEY (`member_uid`) REFERENCES `loe_member`(`member_uid`),
	`look_times` INT UNSIGNED NOT NULL DEFAULT 0,
	PRIMARY KEY(`content_id`)
)ENGINE=MYISAM AUTO_INCREMENT 1 DEFAULT CHARSET=utf8
SQL;

        $sqls['loe_reply'] = <<<SQL
CREATE TABLE `loe_reply`(
	`reply_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`content_id` INT UNSIGNED NOT NULL,
	FOREIGN KEY (`content_id`) REFERENCES `loe_content`(`content_id`),
	`quote_id` INT UNSIGNED NOT NULL DEFAULT 0, -- 这是引用回复的ID
	FOREIGN KEY (`quote_id`) REFERENCES `loe_reply`(`reply_id`),
	`reply_content` TEXT NOT NULL,
	`time` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`member_uid` INT UNSIGNED NOT NULL,
	FOREIGN KEY (`member_uid`) REFERENCES `loe_member`(`member_uid`),
	PRIMARY KEY(`reply_id`)
)ENGINE=MYISAM AUTO_INCREMENT 1 DEFAULT CHARSET=utf8
SQL;

        $sqls['loe_manage'] = <<<SQL
CREATE TABLE `loe_manage`(
	`manager_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`manager_name` VARCHAR(64) NOT NULL DEFAULT "Null",
	`password` CHAR(32) NOT NULL,
	`create_time` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`level` TINYINT UNSIGNED NOT NULL,
	FOREIGN KEY (`manager_id`) REFERENCES `loe_member`(`member_uid`)
)ENGINE=MYISAM AUTO_INCREMENT 100000 DEFAULT CHARSET=utf8
SQL;


        //循环执行
        foreach ($sqls as $key=>$val){
            if (!mysqli_query($link,$val)) {
                exit("错误！<a href='index.php'>点击返回！</a>>");
            }
        }

        //创建用户
        $sql = <<<SQL
insert into loe_manage(`manager_name`,`password`,`level`) value("{$_POST['manage_name']}",md5("{$_POST['manage_pw']}"),0)
SQL;
        if (!mysqli_query($link,$sql)){
            exit("管理员创建失败！<a href='index.php'>点击返回！</a>>");
        }


        $filename='inc/config.inc.php';
        $str_file=file_get_contents($filename);
        $pattern="/'DB_ADDR',.*?\)/";
        if(preg_match($pattern,$str_file)){
            $_POST['db_host']=addslashes($_POST['db_host']);
            $str_file=preg_replace($pattern,"'DB_ADDR','{$_POST['db_host']}')", $str_file);
        }
        $pattern="/'DB_USER',.*?\)/";
        if(preg_match($pattern,$str_file)){
            $_POST['db_user']=addslashes($_POST['db_user']);
            $str_file=preg_replace($pattern,"'DB_USER','{$_POST['db_user']}')", $str_file);
        }
        $pattern="/'DB_PWD',.*?\)/";
        if(preg_match($pattern,$str_file)){
            $_POST['db_pw']=addslashes($_POST['db_pw']);
            $str_file=preg_replace($pattern,"'DB_PWD','{$_POST['db_pw']}')", $str_file);
        }
        $pattern="/'DB_USEDB',.*?\)/";
        if(preg_match($pattern,$str_file)){
            $_POST['db_database']=addslashes($_POST['db_database']);
            $str_file=preg_replace($pattern,"'DB_USEDB','{$_POST['db_database']}')", $str_file);
        }
        $pattern="/\('DB_PORT',.*?\)/";
        if(preg_match($pattern,$str_file)){
            $_POST['db_port']=addslashes($_POST['db_port']);
            $str_file=preg_replace($pattern,"('DB_PORT',{$_POST['db_port']})", $str_file);
        }
        if(!file_put_contents($filename, $str_file)){
            exit("配置文件写入失败，请检查config.inc.php文件的权限!<a href='install.php'>点击返回</a>");
        }
        if(!file_put_contents('inc/install.lock',':))')){
            exit('文件inc/install.lock创建失败，但是您的系统其实已经安装了，您可以手动建立inc/install.lock文件!');
        }
        echo "<div style='font-size:16px;color:green;'>:)) 恭喜您,安装成功! <a href='index.php'>访问首页</a> | <a href='admin/login.php'>访问后台</a></div>";
        exit();

    }

?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8" />
    <title>欢迎使用 本引导安装程序</title>
    <meta name="keywords" content="欢迎使用本引导安装程序" />
    <meta name="description" content="欢迎使用本引导安装程序" />
    <style type="text/css">
        body {
            background:#f7f7f7;
            font-size:14px;
        }
        #main {
            width:560px;
            height:490px;
            background:#fff;
            border:1px solid #ddd;
            position:absolute;
            top:50%;
            left:50%;
            margin-left:-280px;
            margin-top:-280px;
        }
        #main .title {
            height: 48px;
            line-height: 48px;
            color:#333;
            font-size:16px;
            font-weight:bold;
            text-indent:30px;
            border-bottom:1px dashed #eee;
        }
        #main form {
            width:400px;
            margin:20px 0 0 10px;
        }
        #main form label {
            margin:10px 0 0 0;
            display:block;
            text-align:right;
        }
        #main form label input.text {
            width:200px;
            height:25px;
        }

        #main form label input.submit {
            width:204px;
            display:block;
            height:35px;
            cursor:pointer;
            float:right;
        }
    </style>
</head>
<body>
<div id="main">
    <div class="title">欢迎使用 本引导安装程序</div>
    <form method="post">
        <label>数据库地址：<input class="text" type="text" name="db_host" value="localhost" /></label>
        <label>端口：<input class="text" type="text" name="db_port" value="3306" /></label>
        <label>数据库用户名：<input class="text" type="text" name="db_user" /></label>
        <label>数据库密码：<input class="text" type="text" name="db_pw" /></label>
        <label>数据库名称：<input class="text" type="text" name="db_database" /></label>
        <br /><br />
        <label>后台管理员名称：<input class="text" type="text" name="manage_name" readonly="readonly" value="admin" /></label>
        <label>密码：<input class="text" type="password" name="manage_pw" /></label>
        <label>密码确认：<input class="text" type="password" name="manage_pw_confirm" /></label>
        <label><input class="submit" type="submit" name="submit" value="确定安装" /></label>
    </form>
</div>
</body>
</html>
