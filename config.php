<?php

// 配置别名为mysql的预设数据库
// 在模型类里使用 /Driver::useDefault(别名); 载入
// Driver类会自动管理驱动的初始化，已经创建过的不会重复创建
// 如：
//      通过     /Driver::useDefault('mysql');     使用下面预设的mysql驱动
Driver::config(
	array(
		'defaultDrivers' => array(
			'mysql' => array(
				'path'     => '/database/mysql', // 驱动文件路径，省略`.php`的扩展名
				'host'     => 'localhost',
				'user'     => 'root',
				'password' => '123456',
				'database' => 'db_ichiso_main',
                'charset'  => 'utf8'
			),
			'memcache' => array(
				'path'     => '/cache/memcache',
				'host'     => 'localhost',
				'port'     => '11211'
			)
		)
	)
);


// 配置域名路由，域名正则 => 应用名
// 不同域名对应不同的控制器目录，干净地管理控制器路由规则！
App::onDomain('ichi[ph]{3}.com')->toApp('ichiphp')
->all('/shop(\d+)','/a/one/bar/$1')  // 将所有URI匹配`/shop(\d+)`请求的请求转到`/a/one/bar/$1`对应的控制器
->get('/food-(\w+)','/b/foo/$1');    // 将URI匹配`/food-(\w+)`的GET请求转到`/b/foo/$1`的控制器
