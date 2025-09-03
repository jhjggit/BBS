<?php
    session_start();

    if(!file_exists('inc/install.lock')){
        header("Location:install.php");
    }

    header("Content-typ:text/html;charset:utf-8");
    define("DB_USEDB",'loebbs');
    define("DB_USER",'root');
    define("DB_PWD",'123456');
    define("DB_ADDR",'127.0.0.1');
    define("DB_POST",'3306');
    define("DB_CHARSET","UTF8");
    define("PIC_SUCCESS","ok");
    define("PIC_FAILED","error");
    define("SON_MODULE","loe_son_module");
    define("FATHER_MODULE","loe_father_module");
    define("MEMBER_TABLE","loe_member");
    define("INDEX_PAGE","/index.php");
    define("LOGIN_PAGE","/login.php");
    define("REGISTER_PAGE","/register.php");
    //定义允许通过的文件类型
    define("ALLOW_PASS_FILE_TYPE", ["image/jpeg","image/png","image/gif","image/bmp"]);
    //定义允许通过的文件扩展名
    define("ALLOW_EXTEND_NAME",["jpeg","jpg","png","gif","bmp"]);

