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
