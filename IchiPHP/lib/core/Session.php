<?php
/**
 * @desc   session操作类
 * @author xiaozheen
 *
 */
class Session {
	
	static $driver;
	
	/**
	 * @desc  初始化
	 */
	static function init() {
		session_start();
	}
	
	/**
	 * @desc  获取session id
	 */
	static function getToken() {
		return session_id();
	}
	
	/**
	 * @desc  获取指定值
	 * @param unknown $key
	 * @param string $default
	 * @return Ambigous <string, unknown>
	 */
	static function get( $key, $default = NULL ) {
		
		$val = $_SESSION[ $key ];
		
		return !$val? $default : $val;
		
	}
	
	/**
	 * @desc  设置指定值
	 * @param unknown $key
	 * @param unknown $val
	 */
	static function set( $key, $val ) {
		
		$_SESSION[ $key ] = $val;
		
	}
	
}
