<?php
/**
 * @desc   App类
 */
class App {

	/**
	 * @desc  启动App
	 */
	static function run() {

        // 当前来源域名
        $domain         = $_SERVER['SERVER_NAME'];

        // 一个域名配置器
        $domainConfiger = new DomainConfiger( $domain );

        // 获取匹配的应用名
        $appName        = $domainConfiger->getAppName();

        // 获取特殊路由规则
        $specialRoute   = $domainConfiger->getSpecialRoute();

        // 设置路由器参数
        Router::setAppName( $appName );
        Router::setSpecialRoute( $specialRoute );

        Router::to( ICHI_URI_APP_INIT, false );
        // 开始路由
		self::route();
		
	}

	/**
	 * @desc   开启域名路由配置链式操作
	 * @param  unknown $domain
	 * @return DomainConfigSetter
	 */
	static function onDomain( $domain ) {

        // 返回一个域名配置器给外部使用
		return new DomainConfiger( $domain );

	}
	
	/**
	 * @desc  进行路由
	 */
	private static function route() {
		
		// 去掉GET参数
		$reqUri = strtok( $_SERVER['REQUEST_URI'], '?' );
		
		// 调用路由器
		Router::to( $reqUri );
		
	}
	
}