alter table items change column `status` `status` tinyint(3) not null default 100 comment '事项状态';

alter table items add column `visible_range` tinyint(3) not null default 100 comment '显示范围';

update  items set `visible_range` = 200 where `status` = 1;