DROP TABLE IF EXISTS `items`;
CREATE TABLE `items` (
  `id`      INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT
  COMMENT '主键',
  `fid`     INT(10) UNSIGNED    NOT NULL
  COMMENT '父节点id',
  `user_id` INT(11) UNSIGNED    NOT NULL
  COMMENT '用户id',
  `name`    CHAR(20)            NOT NULL,
  --  修改了排序号的数据类型为 mysql
  `rank`    FLOAT               NOT NULL
  COMMENT '排序号',
  `c_time`  TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP
  COMMENT '创建时间',
  `u_time`  TIMESTAMP           NOT NULL DEFAULT '0000-00-00 00:00:00'
  COMMENT '更新时间,需要在修改basic_info后,或者更新了与之对应的note后,更新该字段',
  `status`  TINYINT(1) UNSIGNED NOT NULL DEFAULT '2'
  COMMENT '{1:"show_global", 2:"enabled", 3:“draft"}',
  PRIMARY KEY (`id`),
  KEY `c_time` (`c_time`),
  KEY `u_time` (`u_time`),
  KEY `rank` (`rank`)
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 66
  DEFAULT CHARSET = utf8
  COMMENT = 'items';


DROP TABLE IF EXISTS `notes`;
CREATE TABLE `notes` (
  `id`      INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `item_id` INT(10) UNSIGNED NOT NULL
  COMMENT 'eq to items.id, 项目ID',
  `content` TEXT             NOT NULL,
  `c_time`  TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP
  COMMENT '创建时间',
  `status`  TINYINT(1)                DEFAULT '1',
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 373
  DEFAULT CHARSET = utf8
  COMMENT = 'notes';


DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id`            BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`          CHAR(255)           NOT NULL,
  `password`      VARCHAR(32)         NOT NULL,
  `user_type`     VARCHAR(20)         NOT NULL
  COMMENT '1,member;2,admin',
  `ts_created`    TIMESTAMP           NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ts_last_login` TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 2
  DEFAULT CHARSET = utf8;

### 以下几张表目前数据库里边还没有
### @todo 在创建用户的时候,即为该用户创建两个特殊tag, todo 和 done, 并将状态设置我 disable, 这样页面筛查标签的时候才不会显示出来

CREATE TABLE `tags` (
  `id`         BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`       CHAR(20)            NOT NULL DEFAULT ''
  COMMENT '名称',
  `tag_status` TINYINT(4) UNSIGNED NOT NULL DEFAULT 10
  COMMENT 'tag状态',
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;


CREATE TABLE `notes_tag_rel` (
  `id`      BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `note_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0
  COMMENT '笔记ID',
  `tag_id`  BIGINT(20) UNSIGNED NOT NULL DEFAULT 0
  COMMENT '标签ID',
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;


CREATE TABLE `tag_user_rel` (
  `id`      BIGINT              NOT NULL AUTO_INCREMENT,
  `tag_id`  BIGINT(20) UNSIGNED NOT NULL DEFAULT 0
  COMMENT '标签ID',
  `user_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0
  COMMENT '用户ID',
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;