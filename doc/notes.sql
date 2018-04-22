DROP TABLE IF EXISTS `items`;
CREATE TABLE `items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `fid` int(10) unsigned NOT NULL COMMENT '父节点id',
  `user_id` int(11) unsigned NOT NULL COMMENT '用户id',
  `name` char(20) NOT NULL,
  `rank` int(11) unsigned NOT NULL COMMENT '排序号(从应用程序实现默认相邻间隔10)',
  `c_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `u_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间,需要在修改basic_info后,或者更新了与之对应的note后,更新该字段',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '2' COMMENT '{1:"show_global", 2:"enabled", 3:“draft"}',
  PRIMARY KEY (`id`),
  KEY `c_time` (`c_time`),
  KEY `u_time` (`u_time`),
  KEY `rank` (`rank`)
) ENGINE=InnoDB AUTO_INCREMENT=66 DEFAULT CHARSET=utf8 COMMENT='items';


DROP TABLE IF EXISTS `notes`;
CREATE TABLE `notes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` int(10) unsigned NOT NULL COMMENT 'eq to items.id, 项目ID',
  `content` text NOT NULL,
  `c_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `status` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=373 DEFAULT CHARSET=utf8 COMMENT='notes';


DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(255) NOT NULL,
  `password` varchar(32) NOT NULL,
  `user_type` varchar(20) NOT NULL COMMENT '1,member;2,admin',
  `ts_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ts_last_login` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
