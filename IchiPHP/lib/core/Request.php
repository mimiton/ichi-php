<?php
/**
 * @desc   请求类
 * @author xiaozheen
 */
class Request {
	
	function __construct() {
		
		// 请求方法
		$this->method = strtoupper($_SERVER['REQUEST_METHOD']);
		
		// 请求参数
		$this->params = $_REQUEST;

	}
	
	/**
	 * @desc   获取浏览器信息
	 * @return multitype:unknown string
	 */
	function getBrowserInfo() {
		
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
	function fromOldIE( $versionLimit = 8 ) {
		
		$browserInfo = $this->getBrowserInfo();
		
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
	function fromMobileDevice() {
		
		$UA = $_SERVER['HTTP_USER_AGENT'];
		
		if( preg_match('/android|iphone|ipod|ucweb/i', $UA) )
			return true;
		else
			return false;
		
	}
	
}