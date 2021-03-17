-- Adminer 4.6.3 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `shoes`;
CREATE TABLE `shoes` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '唯一标识ID',
  `shoe_user_id` int(11) NOT NULL COMMENT '用户ID',
  `shoes_id` varchar(255) NOT NULL COMMENT '鞋柜ID',
  `shoes_role_id` varchar(255) NOT NULL COMMENT '所属的角色ID',
  `shoes_img` text NOT NULL COMMENT '鞋柜所存储鞋的照片',
  `shoes_name` text NOT NULL COMMENT '鞋柜名称',
  `shoes_status` int(11) NOT NULL DEFAULT '0' COMMENT '鞋柜使用状态：0为未使用、1为已使用',
  `shoes_remark` text NOT NULL COMMENT '备注',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `shoe_role`;
CREATE TABLE `shoe_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '唯一标识ID',
  `shoe_role_id` varchar(255) NOT NULL COMMENT '角色ID，时间戳',
  `shoe_user_id` varchar(255) NOT NULL COMMENT '所属用户ID',
  `shoe_role_name` text NOT NULL COMMENT '角色名称',
  `shoe_role_img` text NOT NULL COMMENT '角色头像',
  `shoe_role_color` text NOT NULL COMMENT '角色背景颜色',
  `shoe_user_remark` text NOT NULL COMMENT '备注',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `shoe_user`;
CREATE TABLE `shoe_user` (
  `shoe_user_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `shoe_user_name` varchar(255) NOT NULL COMMENT '用户姓名',
  `shoe_user_phone` text NOT NULL COMMENT '用户手机号',
  `shoe_user_email` varchar(255) NOT NULL COMMENT '用户邮箱',
  `shoe_user_password` text NOT NULL COMMENT '用户登录密码（MD5）',
  `shoe_user_roleNumber` text NOT NULL COMMENT '角色数量',
  `shoe_user_cabNumber` text NOT NULL COMMENT '鞋柜数量',
  `shoe_user_cabLeftNumber` text NOT NULL COMMENT '剩余空闲鞋柜数量',
  `shoe_user_register` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '用户注册时间',
  `shoe_user_login_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '用户最近登陆时间',
  `shoe_user_remark` text NOT NULL COMMENT '备注',
  PRIMARY KEY (`shoe_user_id`),
  UNIQUE KEY `shoe_user_name` (`shoe_user_name`),
  UNIQUE KEY `shoe_user_email` (`shoe_user_email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- 2020-01-18 10:53:48
