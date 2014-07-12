<?php
/**
 * @desc   App类
 */
class App {
	
	// 路由器
	private static $router;
	
	/**
	 * @desc  启动App
	 */
	static function run() {
		
		self::route();
		
	}
	
	/**
	 * @desc  进行路由
	 */
	private static function route() {

		$reqUri = strtok( $_SERVER['REQUEST_URI'], '?' );
		
		Router::to( $reqUri );
		
	}


}