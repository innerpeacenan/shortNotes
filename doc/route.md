路由设计先在的几个问题:
(1) 目录进行了调整, 不许要对路进进行解析了,交给 autoload 方法去报错, 对 action 的存在性进行判断,并给出错误信息
(2) 路由网页路径到 module, controller 和 action 部分的映射关系,需要和 zend framework 保持一致
(3) 给 controller 写一个 render 方法, 根据路径,进行简单的 require,最后根据require的至,设 置response 对象的接过,完成一次请求
(3) 写一个 web\controller 方便扩展
(4) 写一个全局类,方便向容器内注入对象
  参考yii全局类的工作方式

  action,module,controller

 @todo 目前这套依赖加载还有很大的问题,需要进一步修改和完善
 @todo 解决了部分问题,重新设计路由机制,让路由功能更加健全


use middleware to display json before page


对 items.status 字段做如下调整:
10 启用

20 暂停

30 归档




增加一个字段, visibleRange
10 仅在当前目录下可见

20 全局可见


alter table items change column `status` `status` tinyint(3) not null default 10 comment '事项状态';

alter table items add column `visible_range` tinyint(3) not null default 10 comment '显示范围';

--- 修复旧数据
update  items set `visible_range` = 20 where `status` = 1;

update items set `status` = 10  where status in (1, 2);

update items set `status` = 30  where status = 3;


代码规范:
属性名称,数据库的字段名称,数组的键统一采用下划线
方法名称和变量名称以及类名统一采用驼峰法


从前端开始调整:
(1) 默认的字段模式
(2) constant 含义更新




















