<?php
/**
 * @desc   App类
 */
class App {
	
	// 路由器
	private static $router;
	// 应用路由表
	private static $appMap;
	
	/**
	 * @desc  设置应用路由表
	 * @param unknown $map
	 */
	static function setAppMap( $map ) {
		
		self::$appMap = $map;
		
	}
	
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

		// 当前来源域名
		$domain = $_SERVER['SERVER_NAME'];
		
		// 获取对应app的名字
		$appName = self::getAppNameByDomain($domain);
		
		// 路由参数
		$routerConfig = array(
				'appName' => $appName
		);
		
		// 设置路由参数
		Router::config( $routerConfig );
		
		// 去掉GET参数
		$reqUri = strtok( $_SERVER['REQUEST_URI'], '?' );
		
		// 调用路由器
		Router::to( $reqUri );
		
	}
	
	/**
	 * @desc  以正则匹配方式获取域名对应的应用名
	 * @param unknown $domain
	 * @return unknown|string
	 */
	private static function getAppNameByDomain( $domain ) {
		
		foreach ( self::$appMap as $pattern => $appName )
			if( preg_match( '/^' . $pattern . '$/i', $domain ) )
				return $appName;
		
		return '';
		
	}


}