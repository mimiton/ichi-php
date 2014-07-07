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
	
}