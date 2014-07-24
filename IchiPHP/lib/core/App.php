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
 * @desc   链式操作工具，配置域名->应用的映射，以及特殊路由规则
 * @author xiaozheen
 */
class DomainConfigSetter {
	
	/**
	 * @desc  构造方法，传入要配置的目标域名
	 * @param unknown $domain
	 */
	function __construct( $domain ) {
		$this->domain = $domain;
	}
	
	/**
	 * @desc   映射到指定应用
	 * @param  unknown $appName
	 * @return DomainConfigSetter
	 */
	function toApp( $appName ) {
		
		$domain = $this->domain;
		
		App::$domainConfigs[ $domain ]['appName'] = $appName;
		
		return $this;
	}
	
	/**
	 * @desc  添加特殊路由
	 * @param unknown $method
	 * @param unknown $uriRegex
	 * @param unknown $reRouteUri
	 * @return DomainConfigSetter
	 */
	private function specialRoute( $method, $uriRegex, $reRouteUri ) {

		$domain = $this->domain;
		
		App::$domainConfigs[ $domain ]['specialRoute'][ $method ][ $uriRegex ] = $reRouteUri;
		
		return $this;
		
	}
	
	/**
	 * @desc  添加处理所有（ALL）请求类型的特殊路由
	 * @param unknown $uriRegex
	 * @param unknown $reRouteUri
	 */
	function all( $uriRegex, $reRouteUri ) {
		
		return $this->specialRoute( App::REQ_METHOD_ALL, $uriRegex, $reRouteUri );
		
	}
	
	/**
	 * @desc  添加处理（GET）请求类型的特殊路由
	 * @param unknown $uriRegex
	 * @param unknown $reRouteUri
	 */
	function get( $uriRegex, $reRouteUri ) {
		
		return $this->specialRoute( App::REQ_METHOD_GET, $uriRegex, $reRouteUri );
	
	}

	/**
	 * @desc  添加处理（POST）请求类型的特殊路由
	 * @param unknown $uriRegex
	 * @param unknown $reRouteUri
	 */
	function post( $uriRegex, $reRouteUri ) {
	
		return $this->specialRoute( App::REQ_METHOD_POST, $uriRegex, $reRouteUri );
	
	}

	/**
	 * @desc  添加处理（PUT）请求类型的特殊路由
	 * @param unknown $uriRegex
	 * @param unknown $reRouteUri
	 */
	function put( $uriRegex, $reRouteUri ) {
	
		return $this->specialRoute( App::REQ_METHOD_PUT, $uriRegex, $reRouteUri );
	
	}

	/**
	 * @desc  添加处理（DELETE）请求类型的特殊路由
	 * @param unknown $uriRegex
	 * @param unknown $reRouteUri
	 */
	function delete( $uriRegex, $reRouteUri ) {
	
		return $this->specialRoute( App::REQ_METHOD_DELETE, $uriRegex, $reRouteUri );
	
	}
	
}