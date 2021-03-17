<?php 
 $hostname="localhost"; //mysql地址
 $basename="root"; //mysql用户名
 $basepass="123456"; //mysql密码
 $database="shoe_cab"; //mysql数据库名称
 $conn=mysql_connect($hostname,$basename,$basepass)or die(mysql_error()); //连接mysql              
 mysql_select_db($database,$conn); //选择mysql数据库
 mysql_query("set names 'utf8'");//mysql编码
?> 