<?php

    function skip($link,$pic,$jump_info,$sec=3)
    {
        $html = <<<A
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="refresh" content="{$sec};URL={$link}" />
    <title>正在跳转中...</title>
    <link rel="stylesheet" type="text/css" href="../admin/style/remind.css" />
</head>
<body>
<div class="notice">
<span class="pic {$pic}"></span> {$jump_info} <a href="$link">{$sec}秒后自动跳转</a>
</div>
</body>
</html>
A;
        echo $html;
        exit;
    }

    function skip_error($jump_info,$link = "/index.php"){
        skip($link,PIC_FAILED,$jump_info);
    }

    function skip_success($jump_info,$link = "/index.php"){
        skip($link,PIC_SUCCESS,$jump_info);
    }

    /**
     * 对删除的条目进行检查
     * @return void
     */
    function delete_check(){
        //对GET传来的数据进行安全验证
        if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            skip("{$_GET['return_url']}", PIC_FAILED, "非法访问!", 2);
        }
    }

    function check_module_exist($table,$id){
        $sql = <<<SQL
select * from {$table} where module_id = {$id}
SQL;
        return execute_sql_bool($sql);
    }


    /**
     * 返回随机指定位数字符串
     * @param $length
     * @return string
     */
    function randomkeys($length)
    {
        $pattern = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ';
        for($i=0;$i<$length;$i++)
        {
            $key .= $pattern{rand(0,51)};    //生成php随机数
        }
        return $key;
    }


    function execute_sql_bool($sql,$error_info="错误"){
        $link = new mysql_tools();
        $res = $link->execute($sql);

        if (!$res = $link->execute($sql)){
            skip($_SERVER['REQUEST_URI'],PIC_FAILED,$error_info);
        }

        return !($res->num_rows == 0);
    }

    function execute_sql($sql,$error_info="错误"){
        $link = new mysql_tools();

        if (!$res = $link->execute($sql)){
            skip($_SERVER['REQUEST_URI'],PIC_FAILED,$error_info);
        }

        return $res;
    }

    function check_user_exist($table,$id){
        $sql = <<<SQL
select * from {$table} where module_id = {$id}
SQL;
        return execute_sql_bool($sql);
    }


    function is_login($link){
        if (isset($_COOKIE['loe_info']['name']) && isset($_COOKIE['loe_info']['passwd'])){
            $sql = <<<SQL
select * from loe_member where member_name = '{$_COOKIE['loe_info']['name']}' and password = '{$_COOKIE['loe_info']['passwd']}'
SQL;

            $res = $link->execute($sql);
            if ($res->num_rows == 1){
                return mysqli_fetch_array($res)['member_uid'];
            }else{
                return false;
            }
        }else{
            return false;
        }
    }


/**
 * 删除cooke
 * @return void
 */
    function logout(){
        setcookie("loe_info[name]",1,time() - 1000);
        setcookie("loe_info[passwd]",1,time() - 1000);
        setcookie("loe_info[uid]",1,time() - 1000);
    }


    /**
     * 组织删除语句[升级中..]
     * @param $table
     * @param $sql_by
     * @param $delete_id
     * @return string
     */
    function detele_sql($table,$sql_by,$delete_id){
        return  <<<SQL
delete from {$table} where {$sql_by} = {$delete_id}
SQL;
    }

    function page($table,$page_num,$current_page,$module_id,$module_key,$jump_url,$is_son = false,$show_num_btn=5){
        //数据验证
        //如果没有设置,或不是数字字符串,或小于1
        if (!isset($current_page) || !is_numeric($current_page)
            || $current_page < 1){
            $current_page = 1;
        }

        if ($is_son){
            $sql = <<<SQL
select count(*) as data_count from {$table} where module_id = {$module_id}
SQL;
        }else{
            $sql = <<<SQL
select count(*) as data_count from {$table}
SQL;
        }

        $link = new mysql_tools();

        //获取总条目数量
        $data_count = mysqli_fetch_array($link->execute($sql))['data_count'];

        if ($data_count == 0){
            return array('limited'=>"",'html'=>"");
        }
        //如果超过了数据所拥有的页码
        $max_page_num = ceil($data_count / $page_num);
        if ($current_page > $max_page_num){
            $current_page = $max_page_num;
        }

        //计算分页从哪里开始
        $start = ($current_page - 1) * $page_num;

        $limited = "limit {$start},{$page_num}";

        //组织HTML代码
        $html = "";
        if ($show_num_btn >= $max_page_num){
            for ($i = 1;$i <= $max_page_num;$i++){
                if ($i == $current_page){
                    $html .= "<span>{$i}</span>";
                }else{
                    $html .= "<a href='{$jump_url}?{$module_key}={$module_id}&page={$i}'>{$i}</a>";
                }
            }
        }else{
            /*
             * 规则:
             * · · | · ·
             */
            //获取最左边的页码号
            $half_num = ceil($show_num_btn / 2);
            if (($left_start = $current_page - $half_num) <= 0){
                $left_start = 1;
            }

            $right_end = 0;
            if (($current_page + $half_num) > $max_page_num){
                $right_end = $max_page_num;
            }else{
                $right_end = $current_page + $half_num;
            }

            $up_page = $current_page - 1;
            if ($current_page == $max_page_num){
                $html .= "<a href='{$jump_url}?{$module_key}={$module_id}&page=1'>« 回到首页</a>";
            }else{
                $html .= "<a href='{$jump_url}?{$module_key}={$module_id}&page={$up_page}'>« 上一页</a>";
            }

            for (;$left_start <= $right_end-1;$left_start++){
                if ($left_start == $current_page){
                    $html .= "<span>{$left_start}</span>";
                }else{
                    $html .= "<a href='{$jump_url}?{$module_key}={$module_id}&page={$left_start}'>{$left_start}</a>";
                }
            }
            $down_page = $current_page + 1;

            //添加上指向最后一个的按钮
            if ($current_page == $max_page_num){
                $html .= "<span>...{$left_start}</span>";
            }else{
                $html .= "<a href='{$jump_url}?{$module_key}={$module_id}&page={$max_page_num}'>...{$max_page_num}</a>";
            }
            $html .= "<a href='{$jump_url}?{$module_key}={$module_id}&page={$down_page}'>下一页 »</a>";
        }


        //直接返回数据集,一个数组
        return array('limited'=>$limited,'html'=>$html);
    }


    /***
     * 上传文件函数
     * 该函数会返回error id:
     *  - 1  说明上传的文件超过的 php.ini 的限制
     *  - 2  表示文件上传失败
     *  - 3  表示文件上传的方式不是 POST
     *  - 4  表示文件的类型与拓展名是  $allow_typeAname 中允许的类型
     * @param $uploadMaxSize
     * @param $uploadFileName
     * @param $allow_typeAname
     * @param $save_path
     * @return array
     */
    function upload($uploadMaxSize,$uploadFileName,$allow_typeAname,$save_path){

        $return_data = array();


        $defaultSize = ini_get("upload_max_filesize");
        $defaultUnit = strtoupper(substr($defaultSize,-1));
        $defaultNum = intval(substr($defaultSize,0,-1));

        //配置文件的默认字节数
        //将所有的单位转换成bytes
        $iniBytes = get_multiple($defaultUnit) * $defaultNum;


        //获取传入的默认字节数目
        $uploadUnit = strtoupper(substr($uploadMaxSize,-1));
        $uploadNum = intval(substr($uploadMaxSize,0,-1));

        //传入的字节数目
        $uploadBytes = get_multiple($uploadUnit) * $uploadNum;


        if ($uploadBytes > $iniBytes){
            $return_data['error'] = "大于PHP.INI 限制的文件大小！";
            $return_data['error_id'] = 1;
            $return_data['return'] = false;
            return $return_data;
        }

        //获取到传输的文件
        $uploadFile = $_FILES[$uploadFileName];

        //判断文件是否为空
        if ($uploadFile['size'] == 0){
            $return_data['error'] = "文件不能为空";
            $return_data['error_id'] = 6;
            $return_data['return'] = false;
            return $return_data;
        }

        //判断上传文件的大小有没有超过$uploadBytes
        if ($uploadFile['size'] > $uploadFile){
            $return_data['error'] = "只能上传大小不超过" . $uploadMaxSize . "的文件！";
            $return_data['error_id'] = 5;
            $return_data['return'] = false;
            return $return_data;
        }




        //进行文件验证
        //如果没有上传成功
        if ($uploadFile['error'] != 0){
            $return_data['error'] = "文件上传失败！";
            $return_data['error_id'] = 2;
            $return_data['return'] = false;
            return $return_data;
        }

        //判断是不是由POST方式上传
        if (!(is_uploaded_file($uploadFile['tmp_name']))){
            $return_data['error'] = "文件上传方式有误！";
            $return_data['error_id'] = 3;
            $return_data['return'] = false;
            return $return_data;
        }

        //判断是不是允许上传的类型
        $fileType = $uploadFile['type'];
        $fileExtendNameArr = explode('.',$uploadFile['name']);
        $fileExtendName = end($fileExtendNameArr);

        $typeIsPass = in_array($fileType,$allow_typeAname['type']);
        $extendIsPass = in_array($fileExtendName,$allow_typeAname['name']);

        if (!($extendIsPass && $typeIsPass)){
            $return_data['error'] = "不合法的文件，不要搞事情哦~";
            $return_data['error_id'] = 4;
            $return_data['return'] = false;
            return $return_data;
        }



        //文件通过验证,开始保存
        $newFileName = randomkeys(rand(10,20)) . substr(microtime(),11) . "." . $fileExtendName;
        $save_path .= $newFileName;


        if (move_uploaded_file($uploadFile['tmp_name'],$save_path)){
            $return_data['success'] = "文件上传成功！";
            $return_data['fileName'] = $newFileName;
            $return_data['return'] = true;
        }

        return $return_data;
    }

    function get_multiple($unit){
        $multiple = 0;
        $unit = strtoupper($unit);
        switch ($unit){
            case "K":
                $multiple = 1024;
                break;
            case "M":
                $multiple = 1024 * 1024;
                break;
            case "G":
                $multiple = 1024 * 1024 * 1024;
                break;
        }
        return $multiple;
    }

    function pagei(){

    }

    function is_manage_login($link){
        if (isset($_SESSION['manage'])){
            echo "<pre>";
            var_dump($_SESSION);
            $sql = <<<SQL
select * from loe_manage where manager_name = "{$_SESSION['manage']['name']}" and manager_id = {$_SESSION['manage']['id']}
SQL;

            if (mysqli_num_rows($link->execute($sql)) == 0){
                return false;
            }else{
                return true;
            }
        }else{
            return false;
        }
    }
