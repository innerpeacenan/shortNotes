想一种 sql 语句种自动参数绑定的方案,用于标识一个东西是用户输入

Query::one($sql,$params=null);

Query::all($sql,$params=null);

Query::cell($sql,$params=null);

Query::column($sql,$params=null);

Query::indexBy($sql,$params=null);

Query::map($sql,$params=null);

// 用于对两个结果集按照给定的键合并
Query::innerJoin($from,$join,$feild);

Query::leftJoin($from,$join,$feild);

Query::groupBy($id);

Query::excute($sql,$params=null);

Query::where();

Query::filterWhere();

Query::isEmpty();

