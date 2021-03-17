<?php
/*
    type=register 用户注册
    type=login 用户登录
*/
include('conn.php');
session_start();

$type=$_GET['type'];
if($type=="logout"){//用户注销
    unset($_SESSION['user_id']);
    // unset($_SESSION['msgCode']);
    // unset($_SESSION['pic']);
    echo '<meta http-equiv="content-type" content="text/html;charset=utf-8">'."<script>alert('注销成功！');window.location.href=('../login.html');</script>";
}

$userid = $_SESSION['user_id'];

if($userid){
    header("Location:../");
    exit();
}

if($type=='register'){//用户注册
    
    /*
        验证用户提交的数据
    */
    if (!preg_match('/^[\x{4e00}-\x{9fa5}A-Za-z0-9_]{2,10}$/u', $_POST['name'])||!$_POST['name']){
        echo '{"status": "0", "message": "用户名2-10位字符，只能由中文、数字、字母（区分大小写）和下划线组合", "id": "#name"}';
        exit;
    }
    
    if (!preg_match('/^[!@#.\d\w]{6,12}$/i', $_POST['password'])||!$_POST['password']){
        echo '{"status": "0", "message": "密码输入不规范，特殊字符只允许.!@#，长度6-12", "id": "#password"}';
        exit;
    }
        
    if (!preg_match('/^(1(([35789][0-9])|(47)))\d{8}$/i', $_POST['mobile'])&&$_POST['mobile']){
        echo '{"status": "0", "message": "手机号输入不规范！", "id": "#mobile"}';
        exit;
    }
    
    if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
        echo '{"status": "0", "message": "邮箱输入不规范！", "id": "#email"}';
        exit;
    }
    
    $name=mysql_real_escape_string($_POST['name']);
    $mobile=mysql_real_escape_string($_POST['mobile']);
    $email=mysql_real_escape_string($_POST['email']);
    $password=mysql_real_escape_string(md5($_POST['password']));
    // $imageCode=$_POST['imageCode'];
    
    // if($_POST['msgCode']!=$_SESSION['msgCode']){
    //     echo '{"status": "0", "message": "邮箱验证码错误！", "id": "#msgCode"}';
    //     exit;
    // }
    
    $result_checkName_num=mysql_query("select * from `shoe_user` where `shoe_user_name`='$name' limit 1") or die(mysql_error());
    if(mysql_num_rows($result_checkName_num)!=0){
        echo '{"status": "0", "message": "用户名已存在！", "id": "#name"}';
        exit;
    }
    
    $result_checkEmail_num=mysql_query("select * from `shoe_user` where `shoe_user_email`='$email' limit 1") or die(mysql_error());
    if(mysql_num_rows($result_checkEmail_num)!=0){
        echo '{"status": "0", "message": "用户邮箱已存在！", "id": "#email"}';
        exit;
    }
    
    //以上验证成功后进行新建用户
    $sql="INSERT INTO `shoe_user` (`shoe_user_name`, `shoe_user_phone`, `shoe_user_email`, `shoe_user_password`, `shoe_user_roleNumber`, `shoe_user_cabNumber`, `shoe_user_cabLeftNumber`) VALUES ('$name', '$mobile', '$email', '$password', '0', '20', '20')";
    $result=mysql_query($sql)or die(mysql_error());
    
    $result1=mysql_query("select `shoe_user_id` from `shoe_user` where `shoe_user_name` = '$name'")or die(mysql_error());
    $row1=mysql_fetch_array($result1);
    $user_id=$row1['shoe_user_id'];
    
    //同时创建用户对应的鞋柜ID，进行预置
    for($i=1;$i<=20;$i++){
        mysql_query("INSERT INTO `shoes` (`shoe_user_id`, `shoes_id`) VALUES ('$user_id', '$i');")or die(mysql_error());
    }
    
    //返回注册成功信息
    if($result){
        echo '{"status": "1", "message": "注册成功！"}';
    }else{
        echo '{"status": "2", "message": "注册失败，请重试！"}';
    }
    
}elseif ($type=='login') {//用户登录
    
    /*
        验证用户提交的数据
    */
    if (!preg_match('/^[\x{4e00}-\x{9fa5}A-Za-z0-9_]{2,10}$/u', $_POST['loginName'])||!$_POST['loginName']){
        echo '{"status": "0", "message": "账号不正确！", "id": "#loginName"}';
        exit;
    }
    
    if (!preg_match('/^[!@#.\d\w]{6,12}$/i', $_POST['loginPassword'])||!$_POST['loginPassword']){
        echo '{"status": "0", "message": "密码不正确！", "id": "#loginPassword"}';
        exit;
    }
    
    /*
        对用户提交的数据进行过滤
    */
    $name=mysql_real_escape_string($_POST['loginName']);
    $password=mysql_real_escape_string(md5($_POST['loginPassword']));
    
    $result2=mysql_query("select `shoe_user_id` from `shoe_user` where (`shoe_user_name`='$name' or `shoe_user_email`='$name') and `shoe_user_password` = '$password'")or die(mysql_error());
    $row2=mysql_fetch_array($result2);
    $user_id=$row2['shoe_user_id'];
    
    if(!$user_id){
        echo '{"status": "0", "message": "账号或密码不正确！", "id": "#loginName"}';
        exit;
    }else{
        $_SESSION['user_id']=$user_id;
        $sql3 = "update `shoe_user` set `shoe_user_login_time`=now() where `shoe_user_id` = '$user_id'";
        if(mysql_query($sql3)){
            echo '{"status": "1", "message": "登陆成功！"}';
            exit;
        }else{
            echo '{"status": "2", "message": "登陆失败，请重试！"}';
            exit;
        }
    }
    
}
