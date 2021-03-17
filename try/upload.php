<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?php
if($_FILES["file"]){
    $allowedExts = array("jpeg", "jpg", "png");
    $file_name = pathinfo($_FILES["file"]["name"]);
    $file_extension = strtolower($file_name['extension']);// 获取文件后缀名
    $uploaded_tmp = $_FILES["file"]["tmp_name"];
    $file_size = $_FILES["file"]["size"];
    $file_type = $_FILES["file"]["type"];
    // $temp = explode(".", $_FILES["file"]["name"]);
    // echo $_FILES["file"]["size"];
    // $extension = strtolower(end($temp));     // 获取文件后缀名
    if ((($file_type == "image/jpeg")
    || ($file_type == "image/jpg")
    || ($file_type == "image/pjpeg")
    || ($file_type == "image/x-png")
    || ($file_type == "image/png"))
    && in_array($file_extension, $allowedExts))
    {
        if($file_size > 204800){   // 小于 200 kb
            echo "请上传小于200kb的图片！";
            exit;
        }
        if(!getimagesize($uploaded_tmp)){//防止图片头部修改
            echo "非法文件上传！";
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
        
        if ($_FILES["file"]["error"] > 0)
        {
            echo "错误：: " . $_FILES["file"]["error"] . "<br>";
            exit;
        }
        else
        {
            echo "上传文件名: " . $_FILES["file"]["name"] . "<br>";
            echo "文件类型: " . $file_type . "<br>";
            echo "文件大小: " . ($file_size / 1024) . " kB<br>";
            echo "文件临时存储的位置: " . $uploaded_tmp . "<br>";
            
            $file_path = "../images/tmp/" .time()."-". $file_size.".".$file_extension;
            //文件转储
            // 如果 upload 目录不存在该文件则将文件上传到 upload 目录下
            move_uploaded_file($uploaded_tmp, $file_path);
            echo "文件存储在: " . $file_path;
            
        }
    }
    else
    {
        echo "只允许jpeg、jpg、png等图片类型的文件格式上传！";
        exit;
    }
}
?>
<h2 align="center"><font color="red">文件上传</font></h2>
<form align="center" action="<?php echo $url;?>" method="post" enctype="multipart/form-data">
<label for="file">文件上传:</label>
<br />
<a><input type="file" name="file" id="file" />不可覆盖上传</a>
<br />
<a><input type="submit" name="submit" value="上传" />
</form>