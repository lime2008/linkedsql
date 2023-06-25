# linkedsql
[![npm version](https://img.shields.io/npm/v/oicq/latest.svg)](https://www.npmjs.com/package/oicq)
[![dm](https://shields.io/npm/dm/oicq)](https://www.npmjs.com/package/oicq)
[![node engine](https://img.shields.io/node/v/oicq/latest.svg)](https://nodejs.org)
一个方便，轻巧，解耦性强的php链式预处理操作数据库  
可以根据自己需要修改或添加目录下的入口或操作文件来达到自己的需求
# How to use?
填写好数据库信息并实例化类，各个使用例子如下:  
SELECT:  
```php
$sql->select('表名')->key(array('想查询的键',...))->clause(attay('查询的键',...))->bind(array('绑定查询值',...))->run();  
当然也可不加子句:$sql->select('表名')->key(array('想查询的键'))->run(); 
```
UPDATE:  
```php $sql->update('表名')->key(array('想修改的键',...))->value(array('修改值',...))->clause(attay('查询的键',...))->bind(array('绑定查询值',...))->run();
```
INSERT:  
```php
$sql->insert('表名')->key(array('想插入的值'))->value(array('修改值',...))->run();  
```
DELETE:  
```php
$sql->delete('表名')->key(array('想删除的值'))->clause(attay('查询的键',...))->bind(array('绑定值',...))->run();  
$sql->delete('表名')->key(array('想删除的值'))->run();  
```
# More
喜欢的话点一个Star吧⭐！
