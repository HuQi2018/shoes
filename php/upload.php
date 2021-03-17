<?php
session_start();
$userid = $_SESSION['user_id'];

if(!$userid){
    echo '{"status": "0", "message": "未登录！", "url": "login.html"}';
    exit;
}

if($_FILES["upload_file"]){
    
    $allowedExts = array("jpeg", "jpg", "png");
    $file_name = pathinfo($_FILES["upload_file"]["name"]);
    $file_extension = strtolower($file_name['extension']);// 获取文件后缀名
    $uploaded_tmp = $_FILES["upload_file"]["tmp_name"];
    $file_size = $_FILES["upload_file"]["size"];
    $file_type = $_FILES["upload_file"]["type"];
    // $temp = explode(".", $_FILES["upload_file"]["name"]);
    // echo $_FILES["upload_file"]["size"];
    // $extension = strtolower(end($temp));     // 获取文件后缀名
    if ((($file_type == "image/jpeg")
    || ($file_type == "image/jpg")
    || ($file_type == "image/pjpeg")
    || ($file_type == "image/x-png")
    || ($file_type == "image/png"))
    && in_array($file_extension, $allowedExts))
    {
        if($file_size > 204800){   // 小于 200 kb
            echo '{"status": "0", "message": "请上传小于200kb的图片！"}';
            exit;
        }
        if(!getimagesize($uploaded_tmp)){//防止图片头部修改
            echo '{"status": "0", "message": "非法文件上传！"}';
            exit;
        }
        
        //重新制作一张图片，抹去任何可能有危害的数据，可能需要额外的扩展库
        // if( $file_extension == 'jpeg' ) {
        //     $img = imagecreatefromjpeg( $uploaded_tmp );
        //     imagejpeg( $img, $temp_file, 100);
        // }
        // else {
        //     $img = imagecreatefrompng( $uploaded_tmp );
        //     imagepng( $img, $temp_file, 9);
        // }
        // imagedestroy( $img );
        
        if ($_FILES["upload_file"]["error"] > 0)
        {
            echo '{"status": "0", "message": "'."错误：: " . $_FILES["upload_file"]["error"].'"}';
            exit;
        }
        else
        {
            // echo "上传文件名: " . $_FILES["upload_file"]["name"] . "<br>";
            // echo "文件类型: " . $file_type . "<br>";
            // echo "文件大小: " . ($file_size / 1024) . " kB<br>";
            // echo "文件临时存储的位置: " . $uploaded_tmp . "<br>";
            
            $file_path = "tmp/" .time()."-". $file_size.".".$file_extension;
            //文件转储
            // 如果 upload 目录不存在该文件则将文件上传到 upload 目录下
            move_uploaded_file($uploaded_tmp, "../images/".$file_path);
            // echo "文件存储在: " . $file_path;
            //输出图片文件<img>标签
            
            $data = base64_encode('<span class="i-img" style="" onclick="change_img(this);">
                                    <input type="radio" name="img" value="../'.$file_path.'">
                                    <img class="" src="images/'.$file_path.'" alt="">
                                </span>');
            echo '{"status": "1", "message": "'.$data.'"}';
        }
    }
    else
    {
        echo '{"status": "0", "message": "只允许jpeg、jpg、png等图片类型的文件格式上传！！"}';
        exit;
    }
}