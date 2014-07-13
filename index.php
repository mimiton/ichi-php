<?php
// 关闭不需要的 error report
error_reporting(E_ALL &~ (E_STRICT | E_NOTICE));
// 载入核心库
require 'IchiPHP/lib/lib.php';

// 配置应用路由表，域名正则 => 应用名
App::setAppMap(array(
'ichiphp.com' => 'ichiphp'
));

// 配置别名为mysql的预设数据库
// 在模型类里使用 /Driver::useDefault(别名); 载入
Driver::config(array(
	'defaultDrivers' => array(
		'mysql' => array(
			'path'    => '/database/mysql',
			'host'    => 'localhost',
			'user'    => 'root',
			'password'=> '123456',
			'database'=> 'db_ichiso_main'
		)
	)
));

// 启动
App::run();

//debugging
//Cookie::forever('abc', 'hahdiwjdjwoaks');
echo '<h3>cookie:abc='.Cookie::get('abc').'</h3>';