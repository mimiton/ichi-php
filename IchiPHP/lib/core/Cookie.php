<?php
class Cookie {
	
	// cookie的密匙cookie名称后缀
	const TOKEN_SUFFIX = '_i_c';

	/**
	 * @desc  天数cookie
	 * @param unknown $key
	 * @param unknown $val
	 * @param number $dayNum
	 */
	static function day( $key, $val, $dayNum = 1 ) {
		self::set( $key, $val, time() + $dayNum*24*60*60 );
	}
	
	/**
	 * @desc  周数cookie
	 * @param unknown $key
	 * @param unknown $val
	 * @param number $weekNum
	 */
	static function week( $key, $val, $weekNum = 1 ) {
		self::set( $key, $val, time() + $weekNum*7*24*60*60 );
	}
	/**
	 * @desc  月数cookie
	 * @param unknown $key
	 * @param unknown $val
	 * @param number $monthNum
	 */
	static function month( $key, $val, $monthNum = 1 ) {
		self::set( $key, $val, time() + $monthNum*30*24*60*60 );
	}
	
	/**
	 * @desc  永久cookie
	 * @param unknown $key
	 * @param unknown $val
	 */
	static function forever( $key, $val ) {
		self::set( $key, $val, time() + 36500*24*60*60 );
	}
	
	/**
	 * @desc  获取cookie
	 * @param string $key      键名
	 * @param string $safeMode 安全模式（可选，默认true）
	 */
	static function get( $key, $safeMode = true ) {
		
		if( $safeMode && !self::isSafe( $key ) )
			return false;
		
		return $_COOKIE[ $key ];
		
	}
	
	/**
	 * @desc  设置cookie
	 * @param string $key    键名
	 * @param string $val    设置值
	 * @param string $expire 过期时间
	 * @param string $path   路径
	 * @param string $domain 域名
	 * @param string $secure 使用https
	 */
	static function set( $key, $val, $expire = NULL, $path = NULL, $domain = NULL, $secure = NULL ) {
		
		$UA    = $_SERVER['HTTP_USER_AGENT'];
		$token = md5( $val . $UA );
		
		$tokenKey = $key . self::TOKEN_SUFFIX;
		
		
		setcookie( $key, $val, $expire, $path, $domain, $secure, $secure );
		setcookie( $tokenKey, $token, $expire, $path, $domain, $secure, $secure );
	}
	
	/**
	 * @desc  检查指定cookie是否安全
	 */
	private static function isSafe( $key ) {
		
		$UA    = $_SERVER['HTTP_USER_AGENT'];
		$val   = $_COOKIE[ $key ];
		$token = $_COOKIE[ $key . self::TOKEN_SUFFIX ];
		
		
		$md5 = md5( $val . $UA  );
		
		return strcmp( $md5, $token ) == 0;
		
	}
	
}