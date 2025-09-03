<?php
class mysql_tools{
    public $connect;
    public $username;
    public $password;
    public $usingDB;
    public $address;
    public $port = 3306;
    public $info;
    public $res =  null;
    /**
     * @param $username
     * @param $password
     * @param $usingDB
     */
    public function __construct($address=DB_ADDR,$username=DB_USER
        , $password=DB_PWD, $usingDB=DB_USEDB, $port = DB_POST,$charset = DB_CHARSET)
    {
        if ($this->connect($address,$username, $password, $usingDB,$port,$charset)){
            $this->address = $address;
            $this->username = $username;
            $this->password = $password;
            $this->usingDB = $usingDB;
            $this->port = $port;
        }
    }

    public function getConnect()
    {
        return $this->connect;
    }

    private function connect($address,$username,$pwd,$usingDB,$port,$charset)
    {
        if ($this->connect = @mysqli_connect($address, $username, $pwd, $usingDB,$port)) {
            //设置字符集
            mysqli_set_charset($this->connect,$charset);
            return true;
        }else{
            var_dump(mysqli_error($this->connect));
            return false;
        }
    }

    /**
     * @param $sql
     * @return bool or Result Set
     */
    public function execute($sql){
        $this->res = mysqli_query($this->connect,$sql);
        if (mysqli_errno($this->connect)){
            exit(mysqli_error($this->connect));
        }
        return $this->res;
    }

    /**
     * @param $sql
     * @return bool
     */
    public function execute_bool($sql){
        if (!$res = mysqli_real_query($this->connect,$sql)){
            exit(mysqli_error($this->connect));
        }
        return $res;
    }


    public function multi_execute($sql_arr,&$error){
        $m_sql = implode(';',$sql_arr).';';
        $i = 0;
        if (mysqli_multi_query($this->connect,$m_sql)){
            $data[] = array();

            do{
                //如果返回的不是结果集,就返回null
                if ($res = mysqli_store_result($this->connect)){
                    $data[$i] = mysqli_fetch_all($res);
                    mysqli_free_result($res);
                }else{
                    $data[$i] = null;
                }
                $i++;
                //判断还有没有下一个查询语句
                if (!mysqli_more_results($this->connect)) break;
            }while(mysqli_next_result($this->connect));
            if ($i == count($sql_arr)){
                return $data;
            }else{
                $error =  "更改失败！". $i;
                return false;
            }
        }else{
            $error =  "更改失败！";
            return false;
        }
    }


    /**
     * @param $res
     * @return int|string
     * 获取查询结果集返回的条目
     */
    public function getQueryNums($res){
        return mysqli_num_rows($res);
    }


    //传入的可能是数组
    public function escape($data){
        if (is_string($data)){
            return mysqli_real_escape_string($this->connect,$data);
        }

        if (is_array($data)){
            $i = 0;
            foreach ($data as $ele){
                $data[$i++] = $this->escape($ele);
            }
            return $data;
        }
        return null;
    }

    public function closeLink(){
        mysqli_close($this->connect);
    }

}