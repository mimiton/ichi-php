<?php
// 关闭不需要的 error report
error_reporting(E_ALL &~ (E_STRICT | E_NOTICE));
// 载入核心库
require 'IchiPHP/lib/lib.php';

// 配置别名为mysql的预设数据库
// 在模型类里使用 /Driver::useDefault(别名); 载入
Driver::config(array(
	'defaultDrivers' => array(
		'mysql' => array(
			'path' => '/database/mysql',
			'host' => 'localhost',
			'user' => 'root',
			'password'=> '123456',
			'database'=> 'db_ichiso_main'
		)
	)
));

// 实例化一个App类
$app = new App();
// 启动
$app->run();

//debugging
//Cookie::forever('abc', 'hahdiwjdjwoaks');
echo Cookie::get('abc');

/*echo SQL::parseArr2ConditionString(array(
	array( '','name','=','123' ),
	array( 'or',array(
		array('','nickname','=','456'),
		array('and','name','=','789')
	))
));*/
echo '<br>';
echo SQL::table('abc')
->select('a,b,c')
->select(array('se','ba','aer'))
->where('name', 'between', 'caonima', 'kalia')
->orWhere('[["","nickname","=","abc"],["and","abc",">","123df"]]')
->orWhere(function ($q){
	$q->where('a','=','b')->where('c','=','d')->orWhere('a','between','c','d')->orWhere('kkk','not in',array('kqoq','fjru'));
})
->where('kajima','in','1,4,5,6,8')
->orWhere('fuckyou','not in',array('kajima','caonima'))
->orderBy('kax','desc')->get();