SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS friend_profile;
DROP TABLE IF EXISTS friends;
DROP TABLE IF EXISTS users;

ALTER TABLE items CHANGE COLUMN status `status` tinyint(1) unsigned NOT NULL DEFAULT '2' COMMENT '{1:"show_global", 2:"enabled", 3:“draft"}';
UPDATE items set status = 3 WHERE status = 2;
UPDATE items set status = 2 where status = 1;

