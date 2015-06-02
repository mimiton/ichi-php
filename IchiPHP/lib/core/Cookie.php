<?php
/**
 * @desc   Cookie操作类
 * @author xiaozheen
 *
 */
class Cookie {
	
	// 密匙cookie的名称后缀
	const TOKEN_SUFFIX = '_i_c';
	
	/**
	 * @desc  小时cookie
	 * @param unknown $key
	 * @param unknown $val
	 * @param number $hourNum
	 */
	static function hour( $key, $val, $hourNum = 1 ) {
		self::set( $key, $val, $hourNum*60*60 );
	}
	
	/**
	 * @desc  天数cookie
	 * @param unknown $key
	 * @param unknown $val
	 * @param number $dayNum
	 */
	static function day( $key, $val, $dayNum = 1 ) {
		self::set( $key, $val, $dayNum*24*60*60 );
	}
	
	/**
	 * @desc  周数cookie
	 * @param unknown $key
	 * @param unknown $val
	 * @param number $weekNum
	 */
	static function week( $key, $val, $weekNum = 1 ) {
		self::set( $key, $val, $weekNum*7*24*60*60 );
	}
	/**
	 * @desc  月数cookie
	 * @param unknown $key
	 * @param unknown $val
	 * @param number $monthNum
	 */
	static function month( $key, $val, $monthNum = 1 ) {
		self::set( $key, $val, $monthNum*30*24*60*60 );
	}
	
	/**
	 * @desc  永久cookie
	 * @param unknown $key
	 * @param unknown $val
	 */
	static function forever( $key, $val ) {
		self::set( $key, $val, 36500*24*60*60 );
	}
	
	/**
	 * @desc  获取cookie
	 * @param string $key      键名
	 * @param string $safeMode 安全模式（可选，false）
	 */
	static function get( $key, $safeMode = false ) {
		
		if( $safeMode && !self::isSafe( $key ) )
			return false;
		
		return $_COOKIE[ $key ];
		
	}
	
	/**
	 * @desc  设置cookie
	 * @param string $key       键名
	 * @param string $val       设置值
	 * @param string $expire    过期时间
	 * @param string $path      路径
	 * @param string $domain    域名
	 * @param string $useHttps  使用https
	 */
	static function set( $key, $val, $expire = NULL, $path = NULL, $domain = NULL, $useHttps = NULL ) {

		// 设置
		return setcookie( $key, $val, time() + $expire, $path, $domain, $useHttps );

	}

    /**
     * @desc  附带加密校验token的cookie设置
     * @param string $key       键名
     * @param string $val       设置值
     * @param string $expire    过期时间
     * @param string $path      路径
     * @param string $domain    域名
     * @param string $useHttps  使用https
     */
    static function setSecurely( $key, $val, $expire = NULL, $path = NULL, $domain = NULL, $useHttps = NULL ) {

        // 调用set方法进行基本cookie设置
        $baseSetted = self::set( $key, $val, $expire, $path, $domain, $useHttps );

        // cookie加密token的生成、设置
        // 密匙cookie名称
        $tokenKey = $key . self::TOKEN_SUFFIX;

        // UA信息
        $UA    = $_SERVER['HTTP_USER_AGENT'];

        // 使用 值+UA信息+密匙 加密作为密匙
        $token = md5( $val . $UA . ICHI_SALT );

        if( $baseSetted )
            $tokenSetted = self::set( $tokenKey, $token, $expire, $path, $domain, $useHttps );

        // 返回设置成功与否 的布尔值
        return $baseSetted && $tokenSetted;

    }
	
	/**
	 * @desc  检查指定cookie是否安全
	 *        使用 cookie值+UA信息+密匙 加密作为验证密匙
	 *        防止攻击者窃取/伪造cookie
	 */
	private static function isSafe( $key ) {
		
		// UA信息
		$UA    = $_SERVER['HTTP_USER_AGENT'];
		
		// 获取cookie值
		$val   = $_COOKIE[ $key ];
		// 获取密匙cookie的值
		$token = $_COOKIE[ $key . self::TOKEN_SUFFIX ];
		
		// 计算期望密匙
		$md5 = md5( $val . $UA . ICHI_SALT );
		
		// 返回 期望密匙 与 实际密匙 的匹配结果
		return strcmp( $md5, $token ) == 0;
		
	}
	
}