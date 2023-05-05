# 一、简介

用于更深层次理解安全方面的知识，以及一些漏洞的原理

# 二、目录层次介绍

`加粗即为 文件夹`

- /
  - **admin**
  - **front**
  - **imgs**
  - **inc**
  - **other**
  - **style**
  - confirm.php
  - content.php
  - contentDelete.php
  - contentUpdate.php
  - index.php
  - install.php
  - list_father.php
  - list_son.php
  - login.php
  - logout.php
  - member.php
  - memberUpdateImg.php
  - publish.php
  - quote.php
  - register.php
  - reply.php
  - serarch.php

---

## 1、关于 / 

此目录下的文件就是一些具体页面，像是首页、登录页、用户页....

由于基本都是一些查库，展示的逻辑，这里不过多赘述；**但主要着重介绍一下登录的实现逻辑**

### 1）login.php

主要的php代码：

```php
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
```

**is_login函数：**

```php
if (is_login(new mysql_tools())){
    skip("/index.php",PIC_FAILED,"您已经登录了！");
}
```

你可以在 **/inc/tools.inc.php** 中看到此函数的具体实现

主要逻辑就是判断本地存储的cookie，其中的用户uid与密码，与数据库存储的是否一致

**从头 if 入手：**

```php
if (isset($_POST['submit'])){
}
```

你如果查看了此文件的html部分，你会发现：表单有一个input的元素名为：submit

所以当用户提交了表单后，POST中的 submit设置，此 if 就能进入

---

**接着，我们来看下面的检查：**

```php
$check_flag = "login";
include_once "./inc/check_login.inc.php";
```

为了防止用户填写一些乱七八糟的东西，这里的check_flag是为了通用性而写的

这点你可以在此检查脚本中的具体实现中看到

---

**通过验证后，我们需要检查用户是否存在：**

```php
//检查用户是否存在
    $sql = <<<SQL
SELECT * FROM loe_member where member_name = "{$_POST['username']}"
SQL;

    if($link->execute($sql)->num_rows == 0){
        jump_info_error("用户不存在");
    }
```

---

**最后的：**

```php
//登录成功, 跳转到首页
    $uid = $data['member_uid'];
    //设置cookie
    setcookie("loe_info[name]",$_POST['username'],time() + $_POST['auto_login_time']);
    setcookie("loe_info[passwd]",md5($_POST['passwd']),time() + $_POST['auto_login_time']);
    setcookie("loe_info[uid]",$uid,time() + $_POST['auto_login_time']);
```

我们需要设置cookie，将用户信息存储到本地

---

## 2、关于 admin

![image-20230429185431570](C:\Users\86135\AppData\Roaming\Typora\typora-user-images\image-20230429185431570.png)

---

### 1）inc

主要存放检查脚本

### 2）style

存放样式脚本

### 3）login.php

我们来着重介绍一下 login.php此文件

大体的实现逻辑与/login.php类似

但是存储用户信息的方式有所不同，是通过 SESSION 来存到服务器本地的

如果你是一个管理员，正常登录过后，你的 cookie 中就会有一个 **PHPSESSID** 的属性：

![image-20230429185936119](C:\Users\86135\AppData\Roaming\Typora\typora-user-images\image-20230429185936119.png)

此 SESSID 是与 服务器的一一对应，这也是服务器来区分用户的依据

---

## 3、关于 front

模板.... 无需理会

## 4、关于 imgs

存放用户上传的头像

## 5、关于 inc

存放前端数据验证的脚本

## 6、关于 other

i don‘t konw

---

## 7、关于 style

存放css、图片等

---

# 三、功能性脚本

im wirte ing

