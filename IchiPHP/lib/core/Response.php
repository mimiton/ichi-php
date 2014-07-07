<?php
/**
 * @desc   响应类
 * @author xiaozheen
 *
 */
class Response {
	
	/**
	 * @desc  直接输出字符
	 * @param unknown $data
	 */
	function write( $data ) {
		echo $data;
	}
	
	
	/**
	 * @desc  设置模板渲染的数据
	 * @param unknown $key
	 * @param unknown $value
	 */
	function assign( $key, $value ) {
		
	}
	
	/**
	 * @desc  渲染模板
	 * @param unknown $tplPath
	 */
	function render( $tplPath ) {
		
	}
}