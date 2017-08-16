SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS friend_profile;
DROP TABLE IF EXISTS friends;
DROP TABLE IF EXISTS users;

CREATE TABLE `users` (
  `id`            BIGINT(20) UNSIGNED AUTO_INCREMENT NOT NULL,
  `name`          CHAR(255)                          NOT NULL,
  `password`      CHAR(32)                           NOT NULL,
  `user_type`     TINYINT(20)                        NOT NULL
  COMMENT '1,member;2,admin',
  `ts_created`    TIMESTAMP                          NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ts_last_login` TIMESTAMP                          NOT NULL DEFAULT current_timestamp,
  PRIMARY KEY (`id`),
  UNIQUE KEY (`name`)

)
  ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8;


ALTER TABLE items CHANGE COLUMN status `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '{1:"show_global", 2:"enabled", 3:“draft"}';
UPDATE items set status = 3 WHERE status = 2;
UPDATE items set status = 2 where status = 1;

-- 放弃了与排序无限级分类树的做法
alter table items DROP COLUMN t_left;
alter table items DROP COLUMN t_right;
ALTER table items drop column depth ;


-- 删除了自增主键，方便后期分库
ALTER TABLE items CHANGE id id int(10) unsigned NOT NULL  comment '联合主键之一';

ALTER TABLE items DROP primary key;

ALTER TABLE items add primary key (user_id, id);

ALTER TABLE items CHANGE COLUMN user_id user_id int(11) unsigned NOT NULL  COMMENT '用户id';

ALTER TABLE items CHANGE COLUMN status status tinyint(1) unsigned NOT NULL DEFAULT '2' COMMENT '{1:"show_global", 2:"enabled", 3:“draft"}';
