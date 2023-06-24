# linkedsql
一个方便，轻巧，解耦性强的php链式预处理操作数据库
# How to use?
填写好数据库信息并实例化类，各个使用例子如下:  
SELECT:$sql->select('表名')->key(array('想查询的键',...))->clause(attay('查询的键',...))->bind(array('绑定查询值',...))->run();  
当然也可不加子句:$sql->select('表名')->key(array('想查询的键'))->run();  
