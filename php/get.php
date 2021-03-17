<?php
/*
    type=setting 获取用户信息，包含鞋柜信息
    type=get_role 获取角色信息
    type=get_shoes 获取鞋柜信息
*/
session_start();
$userid = $_SESSION['user_id'];

if(!$userid){
    echo '{"status": "0", "message": "未登录！", "url": "login.html"}';
    exit;
}

include('conn.php');

if($_GET['type']=='get_user'){//获取用户和角色信息
    
    //获取角色信息
    $role_query = mysql_query("select * from `shoe_role` where `shoe_user_id` = '$userid'");
    if(mysql_num_rows($role_query)!=0){//有角色
        while($row = mysql_fetch_assoc($role_query)) { 
            $list[] = $row; 
        } 

        foreach($list as $v) {
            $data = $data.'<li onclick="show_shoes(this)" data-id="'.$v['shoe_role_id'].'" class="image_list" style="background-color:'.$v['shoe_role_color'].';">
                            <i onclick="delete_role(this,'.$v['shoe_role_id'].')" class="li_delete" style=""></i>
                            <img class="image_botton" src="images/img/'.$v['shoe_role_img'].'" alt="'.$v['shoe_role_name'].'" />
                            <p class="image_name">'.$v['shoe_role_name'].'</p>
                        </li>';
        }
    }
    $data = $data."<li class='image_list' style='' onclick='javascript:window.location.href=\"add_role.html\";'>
                        <img class='image_botton' src='images/add.png' alt='添加' />
                        <p class='image_name'>添加</p>
                    </li>";
    $data = base64_encode($data);
    
    //获取用户信息
    $user_query = mysql_query("select * from `shoe_user` where `shoe_user_id` = '$userid'");
    if($user_query&&$role_query){
        $user_row = mysql_fetch_array($user_query);
        echo '{"status": "1", "name": "'.$user_row['shoe_user_name'].'", "roleNumber": "'.$user_row['shoe_user_roleNumber'].'", "cabNumber": "'.$user_row['shoe_user_cabNumber'].'", "cabLeftNumber": "'.$user_row['shoe_user_cabLeftNumber'].'", "message": "'.$data.'"}';
        exit;
    }else{
        echo '{"status": "2", "message": "获取用户信息失败，请重试！"}';
        exit;
    }
    
}elseif($_GET['type']=='get_shoes'){
    
    /*
        验证用户提交的数据
    */
    $shoe_role_id = $_POST['role_id'];
    if (!preg_match('/^[0-9]*$/u', $shoe_role_id)||!$shoe_role_id){
        echo '{"status": "0", "message": "非法参数提交！"}';
        exit;
    }
    
    /*
        查询对应用户对应角色的鞋柜信息，只提取已使用的鞋柜
    */
    $data='<h1 id="foot_tip" style=""></h1>'."<ul>";
    $user_query = mysql_query("select `id`,`shoes_img` from `shoes` where `shoe_user_id` = '$userid' and `shoes_role_id` = '$shoe_role_id' and `shoes_status` = '1'");
    if($user_query){
        if(mysql_num_rows($user_query)!=0){
            while($row = mysql_fetch_assoc($user_query)) { 
                $list[] = $row; 
            } 
    
            foreach($list as $v) {
                $data=$data.'<li class="foot_li">
                                    <i onclick="delete_foot(this,'.$v['id'].','.$shoe_role_id.')" class="foot_li_delete" style=""></i>
                                    <img class="foot_li_img" src="images/shoes/'.$v['shoes_img'].'" alt="" />
                                </li>';
            }
        }
        $data=$data.'<li class="foot_li" style="width: 130px;height: 130px;" onclick="put_foot(this,'.$shoe_role_id.');">
                                <img class="foot_li_add" src="images/add.png" alt="" />
                            </li></ul>';
        $data = base64_encode($data);
        echo '{"status": "1", "message": "'.$data.'"}';
        exit;
    }else{
        echo '{"status": "2", "message": "获取鞋柜信息失败，请重试！"}';
        exit;
    }
                        
}