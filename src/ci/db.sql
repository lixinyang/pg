delimiter $$

CREATE TABLE `ci_sessions` (
  `session_id` varchar(40) NOT NULL DEFAULT '0',
  `ip_address` varchar(16) NOT NULL DEFAULT '0',
  `user_agent` varchar(500) NOT NULL DEFAULT '',
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `user_data` text,
  PRIMARY KEY (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8$$

delimiter $$

CREATE TABLE `sns_bindings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `sns_website` varchar(10) DEFAULT NULL,
  `sns_uid` varchar(45) DEFAULT NULL,
  `sns_oauth_token` varchar(100) NOT NULL,
  `sns_oauth_token_secret` varchar(100) DEFAULT NULL,
  `sns_display_name` varchar(100) DEFAULT NULL,
  `token_expire_date` datetime DEFAULT NULL COMMENT '标记sns_oauth_token的过期时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8 COMMENT='记录的是用户绑定的sns帐号，一个用户同时绑定了qq和开心，那么就有两条记录'$$

delimiter $$

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `display_name` varchar(45) DEFAULT NULL,
  `user_token` varchar(80) DEFAULT NULL COMMENT '放在cookie里用于保持用户登录状态的token',
  `email` varchar(100) DEFAULT NULL,
  `passwd` varchar(50) DEFAULT NULL COMMENT '用户除了以sns帐号登录外，还可以以email+密码登录',
  `regist_time` datetime DEFAULT NULL,
  `last_login_time` datetime DEFAULT NULL,
  `last_login_ip` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_UNIQUE` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8 COMMENT='用户表'$$

