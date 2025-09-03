<?php
    include_once "./inc/config.php";
    include_once "./inc/mysql_tools.php";
    include_once "./inc/tools.inc.php";

    //引入检查脚本
    $flag = "你没有权力修改此用户的头像";
/**
 * 此脚本会产生三个数据:
 * 1、$link --- mysql_tools
 * 2、$memberRes --- 结果集对象
 * 3、$memberData --- 数据库数据
 */
    include_once "inc/check_memberUpdate.inc.php";
?>

<?php

    //处理POST请求
    if (isset($_POST['submit'])){

        //调用上传文件函数
        $upload_data = upload('2m', 'userHeadImg', array('type' => array("image/png"), 'name' => array("png")), './imgs/user_HeadImages/');

        if ($upload_data['return']){
            //先获取到用户原本的image_url
            $source_url = $memberData['image_url'];
            if (!($source_url == "null_user_img.png")){
                //删除原本的头像文件
                if (!unlink("./imgs/user_HeadImages/" . $source_url)){
                    skip_error("删除文件失败！");
                }
            }


            $sql = <<<SQL
update loe_member set image_url = "{$upload_data['fileName']}" where member_uid = {$_GET['member_id']}
SQL;
            $link = new mysql_tools();

            if ($link->execute_bool($sql)){
                skip("/member.php?member_id={$_GET['member_id']}",PIC_SUCCESS,"上传成功！");
            }else{
                skip_error("上擦混失败,请联系管理员！");
            }
        }else{
            switch ($upload_data['error_id']){
                case 4:
                    skip_error("头像只能上传 png 格式的文件！");
                    break;
                case 6:
                    skip_error("文件不能为空");
                    break;
            }
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
    <style type="text/css">
        body {
            font-size:12px;
            font-family:微软雅黑;
        }
        h2 {
            padding:0 0 10px 0;
            border-bottom: 1px solid #e3e3e3;
            color:#444;
        }
        .submit {
            background-color: #3b7dc3;
            color:#fff;
            padding:5px 22px;
            border-radius:2px;
            border:0px;
            cursor:pointer;
            font-size:14px;
        }
        #main {
            width:80%;
            margin:0 auto;
        }
    </style>
</head>
<body>
<div id="main">
    <h2>更改头像</h2>
    <div>
        <h3>原头像：</h3>
        <img style="width: 100px; height: 100px" src="imgs/user_HeadImages/<?php echo $memberData['image_url']?>" />
    </div>
    <div style="margin:15px 0 0 0;">
        <form method="post" enctype="multipart/form-data">
            <input name="userHeadImg" width="100" type="file" /><br /><br />
            <input class="submit" name="submit" type="submit" value="保存" />
        </form>
    </div>
</div>
</body>
</html>

