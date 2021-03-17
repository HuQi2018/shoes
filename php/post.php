<?php
/*
//unset($_SESSION['msgCode']);
//	$image = strtoupper($_POST['image']);//取得用户输入的图片验证码并转换为大写
// 	$image2 = $_SESSION['pic'];//取得图片验证码中的四个随机数
//type=code 发送验证码
type=add_role  添加角色
type=delete_role  删除角色
type=put_shoes  放鞋
type=get_shoes  取鞋
*/
include('conn.php');
session_start();
$userid = $_SESSION['user_id'];

$type=$_GET['type'];

if(!$userid){
    header("Location:../");
    exit();
}

if($type=='add_role'){//添加角色
    
    /*
        验证用户提交的数据
    */
    if (!preg_match('/^[\x{4e00}-\x{9fa5}A-Za-z0-9_]{1,6}$/u', $_POST['name'])||!$_POST['name']){
        echo '{"status": "0", "message": "用户名1-6位字符，只能由中文、数字、字母（区分大小写）和下划线组合", "id": "#name"}';
        exit;
    }
    if (!preg_match('/^#[a-f0-9]{6}$/u', $_POST['bgColor'])||!$_POST['bgColor']){
        echo '{"status": "0", "message": "颜色值非法", "id": "#bgColor"}';
        exit;
    }
    if ((!preg_match('/^[爷爷|奶奶|爸爸|妈妈|儿子|姐姐|男孩|女孩|宝贝\.png]{6}$/u', $_POST['img'])&&!preg_match('/^..\/tmp\/[0-9]*-[0-9]*.(jpg|png|jpeg)$/u', $_POST['img']))||!$_POST['img']){
        echo '{"status": "0", "message": "照片地址非法", "id": "#bgColor"}';
        exit;
    }
    
    /*
        对用户提交的数据进行过滤
    */
    $name=mysql_real_escape_string($_POST['name']);
    $bgColor=mysql_real_escape_string($_POST['bgColor']);
    $img=mysql_real_escape_string($_POST['img']);
    
    /*
        查询当前用户的角色名是否已存在
    */
    $result_checkName_num=mysql_query("select * from `shoe_role` where `shoe_role_name`='$name' and `shoe_user_id` = '$userid' limit 1") or die(mysql_error());
    if(mysql_num_rows($result_checkName_num)!=0){
        echo '{"status": "0", "message": "角色名已存在，请更换名称！", "id": "#name"}';
        exit;
    }
    
    /*
        更新用户表信息
    */
    $sql3 = "update `shoe_user` set shoe_user_roleNumber=shoe_user_roleNumber+1 where `shoe_user_id` = '$userid'";
    mysql_query($sql3) or die(mysql_error());
    
    /*
        新建角色
    */
    $shoe_role_id=time();
    $sql="INSERT INTO `shoe_role` (`shoe_role_id`, `shoe_user_id`, `shoe_role_name`, `shoe_role_img`, `shoe_role_color`) VALUES ('$shoe_role_id', '$userid', '$name', '$img', '$bgColor')";
    $result=mysql_query($sql)or die(mysql_error());
    if($result){
        echo '{"status": "1", "message": "角色添加成功！"}';
        exit;
    }else{
        echo '{"status": "2", "message": "角色添加失败，请重试！"}';
        exit;
    }
    // var_dump($_POST);
    
}elseif ($type=="delete_role") {
    
    /*
        验证用户提交的数据
    */
    $shoe_role_id = $_POST['role_id'];
    if (!preg_match('/^[0-9]*$/u', $shoe_role_id)||!$shoe_role_id){
        echo '{"status": "0", "message": "非法参数提交！"}';
        exit;
    }
    
    /*
        查询当前用户是否有权限操作该用户，并从中取出角色唯一标识
    */
    $user_query = mysql_query("select `id` from `shoe_role` where `shoe_user_id` = '$userid' and `shoe_role_id` = '$shoe_role_id'");
    if(mysql_num_rows($user_query)==0){
        echo '{"status": "0", "message": "您无权操作该角色！"}';
        exit;
    }
    $row = mysql_fetch_array($user_query);
    $row_id = $row['id'];
    
    /*
        通过取得的角色唯一标识删除该角色，同时删除该角色所关联的鞋柜，并更新用户信息
    */
    $shoes_num = mysql_query("select `id` from `shoes` where `shoe_user_id` = '$userid' and `shoes_role_id` = '$shoe_role_id'");
    $shoes_num = mysql_num_rows($shoes_num);//查询该角色所使用的鞋柜个数
    $update_user = mysql_query("update `shoe_user` set shoe_user_roleNumber=shoe_user_roleNumber-1,shoe_user_cabLeftNumber=shoe_user_cabLeftNumber+'$shoes_num' where `shoe_user_id` = '$userid'");
    //更新相关鞋柜信息
    $update_shoes = mysql_query("update `shoes` set `shoes_status`='0',`shoes_name`='',`shoes_img`='',`shoes_role_id`='' where `shoe_user_id` = '$userid' and `shoes_role_id` = '$shoe_role_id'");
    //根据唯一标识删除角色
    $delete_role = mysql_query("delete from `shoe_role` where `id` = '$row_id'");
    
    if($delete_role&&$update_shoes&&$update_user){
        echo '{"status": "1", "message": "角色删除成功！"}';
        exit;
    }else{
        echo '{"status": "0", "message": "角色删除失败！"}';
        exit;
    }
    
}elseif ($type=="put_shoes") {//放鞋
    
    /*
        验证用户提交的数据
    */
    $role_id = $_POST['role_id'];
    if (!preg_match('/^[0-9]*$/u', $role_id)||!$role_id){
        echo '{"status": "0", "message": "非法参数提交！"}';
        exit;
    }
    
    /*
        判断该角色是否属于该用户
    */
    $user_query = mysql_query("select `shoe_role_name` from `shoe_role` where `shoe_user_id` = '$userid' and `shoe_role_id` = '$role_id'");
    if(mysql_num_rows($user_query)==0){
        echo '{"status": "0", "message": "您无权操作该角色！"}';
        exit;
    }
    $role_name = mysql_fetch_array($user_query);
    $role_name = $role_name['shoe_role_name'];
    
    /*
        判断该用户鞋柜是否已存满
    */
    $select_left_num = mysql_query("select `shoe_user_cabLeftNumber` from `shoe_user` where `shoe_user_id` = '$userid'");
    $left_num = mysql_fetch_array($select_left_num);
    if($left_num['shoe_user_cabLeftNumber']==0){
        echo '{"status": "0", "message": "鞋柜已放满，取出后再放！"}';
        exit;
    }
    
    /*
        取取到的第一个鞋柜进行放鞋
    */
    $shoes_first = mysql_query("select `id` from `shoes` where `shoe_user_id` = '$userid' and `shoes_status`='0'");
    $shoes_first_id = mysql_fetch_array($shoes_first);
    $shoes_first_id = $shoes_first_id['id'];
    
    //定义接口获取图片信息，因为实现，先使用自定义随机代替
    $num=rand(1,3);
    $img_name=array("mother", "son", "father");
    $shoes_img = $img_name[rand(0,2)]."_".$num.".jpg";
    
    /*
        放鞋，更新鞋柜表和用户表
    */
    $update_shoes = mysql_query("update `shoes` set `shoes_status`='1',`shoes_name`='$role_name',`shoes_img`='$shoes_img',`shoes_role_id`='$role_id' where `id` = '$shoes_first_id'");
    $update_user = mysql_query("update `shoe_user` set shoe_user_cabLeftNumber=shoe_user_cabLeftNumber-1 where `shoe_user_id` = '$userid'");
    if($update_shoes&&$update_user){
        echo '{"status": "1", "message": "放鞋成功！"}';
        exit;
    }else{
        echo '{"status": "2", "message": "放鞋失败，请重试！"}';
        exit;
    }
    
}elseif ($type=="get_shoes") {//取鞋
    
    /*
        验证用户提交的数据
    */
    $shoes_id = $_POST['shoes_id'];
    if (!preg_match('/^[0-9]*$/u', $shoes_id)||!$shoes_id){
        echo '{"status": "0", "message": "非法参数提交！"}';
        exit;
    }
    
    /*
        查询当前用户是否有权限操作该鞋柜，并从中取出鞋柜唯一标识
    */
    $user_query = mysql_query("select `id` from `shoes` where `shoe_user_id` = '$userid' and `shoes_id` = '$shoes_id'");
    if(mysql_num_rows($user_query)==0){
        echo '{"status": "0", "message": "您无权操作该鞋柜！"}';
        exit;
    }
    $row = mysql_fetch_array($user_query);
    $row_id = $row['id'];
    
    /*
        通过取得的鞋柜唯一标识更新鞋柜信息，和用户信息
    */
    $delete_shoes = mysql_query("update `shoes` set `shoes_status`='0',`shoes_name`='',`shoes_img`='',`shoes_role_id`='' where `id` = '$row_id'");
    $update_user = mysql_query("update `shoe_user` set shoe_user_cabLeftNumber=shoe_user_cabLeftNumber+1 where `shoe_user_id` = '$userid'");
    if($delete_shoes){
        echo '{"status": "1", "message": "取鞋成功！"}';
        exit;
    }else{
        echo '{"status": "2", "message": "取鞋失败，请重试！"}';
        exit;
    }
    
}
