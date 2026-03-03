Artisanx 使用规范
==========

### 模块接口
>目录说明
~~~
一个业务主单元作为一个模块,
模块内的子目录,按相应的子业务配置多个功能类.
例如:
在user模块下的controller目录 新建UserController.php, 对应用户相关功能 和 users 表.
在user模块下的controller目录 新建UserAccountController.php, 对应用户消费账户相关功能 和 user_accounts 表.
其它子目录 依此类推.
~~~
~~~
app 根目录
    |__ Demo  模块目录
       |__ Command 业务指令目录
       |   |__ SampleCmd.php  业务指令类
       |__ Config  状态设置目录
       |   |__ SampleStatus.php  状态设置类
       |__ Exception  异常设置目录
       |   |__ SampleErrorCode.php  错误码设置类
       |__ Validate  输入过滤目录
    |   |__ SampleValidate.php  输入过滤类
       |__ Controller  控制器设置目录
       |   |__SampleController.php  控制器设置类
       |__ Job 队列目录
       |   |__ SampleCreateJob.php 数据新增队列类
       |__ Service 业务逻辑目录
       |   |__ SampleService.php 业务逻辑类
       |__ Model 数据模型目录
       |   |__ SampleModel.php 数据模型类
       |__ Transformer 输出过滤目录
       |__ SampleTransformer.php 输出过滤类

~~~

>示例模块代码定制
~~~
以上示例模块代码,能够根据项目规范定制,这样就可以把最优代码整合起来,作为自动生成代码的模板.
~~~

>自动生成 基本业务代码
~~~
根据示例模块结构, 自动生成需要的子功能接口,
且按照相应功能或数据表结构, 提供CURD基本接口, 并能满足一般的增删查改需要, 取消注释就能使用.
接口业务逻辑统一写在service类里, 方便跨业务或单元测试调用.
~~~
~~~
* 命令流程
*
* php artisanx module:make Test                              //创建模块+目录结构
*
* php artisanx module:base TestChild Test c,u,r,d            //创建子接口组 -无数据库
* php artisanx module:base-on TestChild Test mysql c,u,r,d   //创建子接口组 -有数据库
*
* php artisanx module:route TestChild Test                   //创建模块-路由 -无数据库
*
* php artisanx module:job TestChild Test                     //创建模块-消息队列 -无数据库
* php artisanx module:job-on TestChild Test mysql            //创建模块-消息队列 -有数据库
*
* php artisanx module:cmd TestChild Test                     //创建模块-业务指令 -无数据库
* php artisanx module:cmd-on TestChild Test mysql            //创建模块-业务指令 -有数据库
*
* php artisanx module:model TestChild Test                   //新建-模型 -无数据库
* php artisanx module:model-on TestChild Test mysql          //新建-模型 -有数据库
*
* php artisanx module:logic-fields TestChild Test mysql             //更新-逻辑层 筛选字段 -有数据库
* php artisanx module:model-fields TestChild Test mysql             //更新-模型 过滤字段 -有数据库
* php artisanx module:model-repository-fields TestChild Test mysql  //更新-模型 过滤字段 -有数据库
* php artisanx module:request-fields TestChild Test mysql           //更新-转化器 过滤字段 -有数据库
* php artisanx module:trans-fields TestChild Test mysql             //更新-转化器 过滤字段 -有数据库
*
* php artisanx test:make Test                                //创建测试模块+目录结构
* php artisanx test:base TestChild Test                      //创建测试接口组 -无数据库
* php artisanx test:base-on TestChild Test                   //创建测试接口组 -有数据库

~~~

### 单元测试

>目录说明
~~~
根据示例模块结构, 配置 api单元测试和 behavior行为测试.
其中,
api单元测试的CURD测试 对应 同名模块子接口中的CURD, 取消注释就能使用.
api -> url 单元测试 同上, 不同之处在于,是预先登录情况下, 通过url请求接口.
behavior行为测试,默认提供 批量新增数据 到同名模块子接口 对应的表中, 可做综合业务流程测试.
~~~
~~~
test 根目录
    |__ demo  模块目录
         |__ api  接口单元测试目录
         |    |__ SampleTest.php  接口单元测试类
         |__ behavior  行为测试目录
         |  |__ SampleBehavior.php  行为测试类
      |__doc 单元测试生成文档目录
         |__sample_table.md 文档

~~~

>示例模块代码定制
~~~
以上示例模块代码,能够根据项目规范定制,这样就可以把最优测试代码整合起来,作为自动生成测试代码的模板.
~~~

>执行方式
~~~
接口-用例
php think unit test/demo/api/SampleTest.php
php think unit clean test/demo/api/SampleTest.php
php think unit stay test/demo/api/SampleTest.php
php think unit test/demo/api/SampleUrlTest.php
php think unit clean test/demo/api/SampleUrlTest.php
php think unit stay test/demo/api/SampleUrlTest.php

行为-用例
php think unit test/demo/behavior/SampleBehavior.php     //默认 不清除 缓存ID和数据
php think unit clean test/demo/behavior/SampleBehavior.php  //清除 所有 缓存ID和数据
php think unit stay test/demo/behavior/SampleBehavior.php  //清除缓存ID 但保留数据
~~~

>自动生成 基本单元测试
~~~
* php artisanx test:make test                  //创建测试模块+目录结构
* php artisanx test:base TestChild test             //创建测试接口组 -无数据库
* php artisanx test:base-on TestChildtest            //创建测试接口组 -有数据库
~~~

### 任务调度
>说明
~~~

~~~
>执行文件
~~~

~~~
>执行方式
~~~

~~~

### 接口调用规范

####说明
~~~
如果是自动生成的接口代码, 且执行本表自定义查询, 都默认支持下面这些查询query, 服务端不用再写.
如果是关联外表自定义查询, 则需要服务端补充.
~~~

#####接口 query 参数示例
###### POST 类型接口
~~~
略,生成的接口函数有备注.
~~~

###### GET 类型接口
~~~
所有get接口, 默认开启 5分钟缓存, 需要实时数据时 要添加 query参数: '_time=1' .
~~~

* 获取-列表-翻页:
~~~
{{base_url}}/demo/list? _pagination = true & _page =1 & _perpage =3
----------------------------------------
'_pagination'   | boolean | 翻页 打开=true,关闭=false; 关闭时,一页100条数据上限; 默认20;
'_page'         | number  | 页码 默认1
'_perpage'      | number  | 页数 默认20
~~~

* 获取-列表-排序:
~~~
{{base_url}}/demo/list? _sort = id
----------------------------------------
'_sort' | string | 自定义排序 正序 = id , 倒序 = -id ; 默认倒序 id 可以是其它字段;
~~~

* 获取-列表-表达式查询 `=` :
~~~
{{base_url}}/demo/list? _where = name/abc
----------------------------------------
'_where'      |   string   | 筛选  name / abc 相当于 name = abc, name可以是其它字段;

~~~

* 获取-列表-表达式查询 `>=` :
~~~
{{base_url}}/demo/list? _where = id>/5
{{base_url}}/demo/list? _where = id>/5,id</10
----------------------------------------
'_where'      |   string   | 筛选  id >/ 5 相当于 id >= 5, id可以是其它字段;

~~~

* 获取-列表-表达式查询 `like` :
~~~
{{base_url}}/demo/list? _where = name|a
----------------------------------------
'_where'      |   string   | 筛选  name|a 相当于 name like abc, name可以是其它字段

~~~

* 获取-列表-表达式IN查询 :
~~~
{{base_url}}/demo/list? _where_in = name/a1,a2 | id/15,16
----------------------------------------
'_where_in'      |   string   | 筛选  name/a1,a2 相当于 name where in (a1,a2), name值包含a1,a2的数据.

~~~

* 获取-列表-实时 & 获取-详情-实时:
~~~
{{base_url}}/demo/list? _time = 1
{{base_url}}/demo/detail/1? _time = 1
----------------------------------------
'_time'      |   number   |  跳过缓存: 不跳过=0,跳过=1, 默认不跳过.

~~~

* 获取-列表-关联查询:
~~~
{{base_url}}/demo/list? _include = user
----------------------------------------
'_include'      |   string   | 关联模型 关联users用户表数据, 需要服务端定制;
~~~

