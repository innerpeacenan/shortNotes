create table image (
 `id` bigint unsigned not null auto_increment comment '自增主键',
 `note_id` bigint  not null default 0 comment 'note.id',
 `index` tinyint unsigned not null default 0 comment '图片组序列号',
 `base64` longtext comment '图片base64位置编码',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='图片';