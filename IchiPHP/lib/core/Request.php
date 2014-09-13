<?php
/**
 * @desc   请求类
 * @author xiaozheen
 */
class Request {

    private static $method;
    private static $params;
    private static $secureParams;

	/**
	 * @desc   获取来源请求的method方法类型
	 * @return string
	 */
	static function getMethod() {

        if( !isset(self::$method) )
            self::$method = strtoupper($_SERVER['REQUEST_METHOD']);

		return self::$method;

	}
	
	/**
	 * @desc   获取请求参数
	 * @return unknown
	 */
	static function getParam( $key, $secure = true, $charset = 'UTF-8' ) {

        $params = self::getRawParams();

        $param = $params[$key];

        if( !isset($param) )
            return NULL;

        // 安全模式，过滤html字符
        if( $secure )
            $param = htmlentities( $param, ENT_NOQUOTES, $charset );

		return $param;

	}

    /**
     * @desc  获取全部的请求参数
     * @param bool $secure
     * @param string $charset
     * @return mixed
     */
    static function getParams( $secure = true, $charset = 'UTF-8' ) {

        if( $secure )
            if( isset(self::$secureParams) )
                return self::$secureParams;


        $params = self::getRawParams();

        if( $secure ) {

            foreach( $params as $k => $v )
                $params[$k] = htmlentities( $v, ENT_NOQUOTES, $charset );

            self::$secureParams = $params;

        }

        return $params;

    }

    /**
     * @desc   获取原始的请求参数
     * @return mixed
     */
    private static function getRawParams() {

        if( isset(self::$params) )
            return self::$params;

        // 请求method
        $method = self::getMethod();

        // 根据method类型获取对应请求参数
        if( $method == 'GET' )
            self::$params = $_GET;
        else if( $method == 'POST' )
            self::$params = $_POST;

        // 其他情况的参数获取
        else
            parse_str( file_get_contents('php://input'), self::$params );

        return self::$params;

    }
	
	/**
	 * @desc   获取浏览器信息
	 * @return array(
     *              'name'    => '' // 浏览器名称（小写）
     *              'version' => '' // 浏览器版本号
     *         )
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
	 * @desc   判断是否来自旧IE
	 * @param  number $version 最低版本下限（默认为8，即小于IE8被判断为旧版IE）
	 * @return boolean
	 */
	static function fromOldIE( $versionLimit = 8 ) {

        // 浏览器信息
		$browserInfo = self::getBrowserInfo();

        // 是否IE浏览器
		if( $browserInfo['name'] == 'msie' ) {

            // 拆解版本号
			$version = explode( '.', $browserInfo['version'] );
			$version = $version[0];

            // 比较版本号大小
			if( $version < $versionLimit )
				return true;
			
		}
		
		return false;
		
	}
	
	/**
	 * @desc   判断是否来自移动设备
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