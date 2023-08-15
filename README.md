# linkedsql
[![php_version](https://img.shields.io/badge/php_version-%3E5.3-blue)](https://www.php.net/downloads)

一个方便，轻巧，解耦性强的php链式预处理操作数据库  
可以根据自己需要修改或添加目录下的入口或操作文件来达到自己的需求
近期修复了诸多bug，可以放心使用，本仓库还会继续随着个人项目开发的需求而持续优化更新
# How to use?
先准备好数据库名，用户名和密码，然后创建一个连接句柄：
```php 
$con = new mysqli(127.0.0.1,$user,$pwd,$dbname);
```
接着引入入口文件：
```php 
require 'sqlfunc.php';
```
实例化类：
```php
$sql = new \GetSqlFunc\GetSqlFunc();
```
绑定一个数据库连接句柄，在后续需要操作其他数据库的时候可以再次用以下代码绑定新的数据库连接句柄！
```php
$sql->Connect_Loader($con);
```
最后，各个查询的使用例子如下:  
SELECT:  
```php
$sql->select('表名')->key(array('想查询的键',...))->clause(array('查询的键',...))->bind(array('绑定查询值',...))->run();  
```
当然也可不加子句:
```php
$sql->select('表名')->key(array('想查询的键'))->run(); 
```
UPDATE:  
```php
$sql->update('表名')->key(array('想修改的键',...))->value(array('修改值',...))->clause(array('查询的键',...))->bind(array('绑定查询值',...))->run();
```
INSERT:  
```php
$sql->insert('表名')->key(array('想插入的值'))->value(array('修改值',...))->run();  
```
DELETE:  
```php
$sql->delete('表名')->clause(array('查询的键',...))->bind(array('绑定值',...))->run();  
$sql->delete('表名')->run();  
```
# More
喜欢的话点一个Star吧⭐！
