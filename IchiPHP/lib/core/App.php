<?php
/**
 * @desc   App类
 */
class App {
	
	// 请求方法常量
	const REQ_METHOD_ALL = 'ALL';
	const REQ_METHOD_GET = 'GET';
	const REQ_METHOD_POST= 'POST';
	const REQ_METHOD_PUT = 'PUT';
	const REQ_METHOD_DEL = 'DELETE';
	
	// 路由器
	private static $router;
	// 域名应用配置
	static $domainConfigs = array();
	
	/**
	 * @desc  启动App
	 */
	static function run() {
		
		self::route();
		
	}

	/**
	 * @desc   开启域名路由配置链式操作
	 * @param  unknown $domain
	 * @return DomainConfigSetter
	 */
	static function onDomain( $domain ) {
		
		return new DomainConfigSetter( $domain );
		
	}
	
	/**
	 * @desc  进行路由
	 */
	private static function route() {

		// 当前来源域名
		$domain = $_SERVER['SERVER_NAME'];
		
		// 获取域名对应的应用配置
		$routerConfig = self::getRouterConfigByDomain( $domain );
		
		// 设置路由器参数
		Router::config( $routerConfig );
		
		// 去掉GET参数
		$reqUri = strtok( $_SERVER['REQUEST_URI'], '?' );
		
		// 调用路由器
		Router::to( $reqUri );
		
	}
	
	/**
	 * @desc   以正则匹配方式获取域名对应应用的路由配置
	 * @param  unknown $domain
	 * @return unknown|string
	 */
	private static function getRouterConfigByDomain( $domain ) {
		
		foreach ( self::$domainConfigs as $pattern => $config )
			if( preg_match( '/^' . $pattern . '$/i', $domain ) )
				return $config;
		
		return NULL;
		
	}
	
}

/**
 * @desc   域名配置链式操作工具
 * @author xiaozheen
 */
class DomainConfigSetter {
	
	function __construct( $domain ) {
		$this->domain = $domain;
	}
	
	function toApp( $appName ) {
		
		$domain = $this->domain;
		
		App::$domainConfigs[ $domain ]['appName'] = $appName;
		
		return $this;
	}
	
	private function specialRoute( $method, $uriRegex, $reRouteUri ) {

		$domain = $this->domain;
		
		App::$domainConfigs[ $domain ]['specialRoute'][ $method ][ $uriRegex ] = $reRouteUri;
		
		return $this;
		
	}
	
	function all( $uriRegex, $reRouteUri ) {
		
		return $this->specialRoute( App::REQ_METHOD_ALL, $uriRegex, $reRouteUri );
		
	}
	
	function get( $uriRegex, $reRouteUri ) {
		
		return $this->specialRoute( App::REQ_METHOD_GET, $uriRegex, $reRouteUri );
	
	}
	
}