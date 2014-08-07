<?php
/**
 * @desc   请求类
 * @author xiaozheen
 */
class Request {

    private static $method;

	/**
	 * @desc   获取请求方法
	 * @return string
	 */
	static function getMethod() {

        if( !isset(self::$method) )
            self::$method = strtoupper($_SERVER['REQUEST_METHOD']);

		return self::$method;

	}
	
	/**
	 * @desc   请求参数
	 * @return unknown
	 */
	static function getParam( $key, $secure = true, $charset = 'UTF-8' ) {

        $method = self::getMethod();

        if( $method == 'GET' )
            $param = $_GET[ $key ];

        else if( $method == 'POST' )
            $param = $_POST[ $key ];

        if( $secure )
            $param = htmlentities( $param, ENT_NOQUOTES, $charset );

		return $param;

	}
	
	/**
	 * @desc   获取浏览器信息
	 * @return multitype:unknown string
	 */
	static function getBrowserInfo() {
		
		$UA = $_SERVER['HTTP_USER_AGENT'];
		
		if( !preg_match('/(chrome|safari|opera)\/([\d\.]+)/i', $UA, $matches) )
			preg_match('/(MSIE)\s*([\d\.]+)/i', $UA, $matches);
		
		return array(
			'name'    => strtolower( $matches[1] ),
			'version' => $matches[2]
		);
		
	}
	
	/**
	 * @desc   判断旧IE
	 * @param  number $version
	 * @return boolean
	 */
	static function fromOldIE( $versionLimit = 8 ) {
		
		$browserInfo = self::getBrowserInfo();
		
		if( $browserInfo['name'] == 'msie' ) {

			$version = explode( '.', $browserInfo['version'] );
			$version = $version[0];
			
			if( $version < $versionLimit )
				return true;
			
		}
		
		return false;
		
	}
	
	/**
	 * @desc   判断来自移动设备
	 * @return boolean
	 */
	static function fromMobileDevice() {
		
		$UA = $_SERVER['HTTP_USER_AGENT'];
		
		if( preg_match('/android|iphone|ipod|ucweb/i', $UA) )
			return true;
		else
			return false;
		
	}
	
}