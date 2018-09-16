CREATE TABLE `collection_not_need_check` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `collection_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '所属集合id',
  `date` date DEFAULT '0000-00-00' COMMENT '日期',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='集合免签到表';

