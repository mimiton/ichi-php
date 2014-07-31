<?php
/**
 * @desc   响应类
 * @author xiaozheen
 *
 */
class Response {
	
	private static $viewEngineName = 'IchiViewEngine';
	private static $viewPath;
	private static $viewEngine;
	
	/**
	 * @desc  设置视图（模板）引擎
	 * @param unknown $engine
	 */
	static function setViewEngine( $engineName ) {
		self::$viewEngineName = $engineName;
	}
	
	/**
	 * @desc  设置视图文件目录
	 * @param unknown $path
	 */
	static function setViewPath( $path ) {
		self::$viewPath = $path;
	}
	
	/**
	 * @desc  直接输出字符
	 * @param unknown $data
	 */
	static function write( $data ) {
		echo $data;
	}
	
	
	/**
	 * @desc  设置模板渲染的数据
	 * @param unknown $key
	 * @param unknown $value
	 */
	static function assign( $key, $value = NULL ) {
		
		// 初始化视图引擎实例
		self::initViewEngine();
		
		// 设置视图变量键值对
		if( !is_array( $key ) ) {
			$keyValues = array( $key => $value );
		}
		else
			$keyValues = $key;
		
		foreach ( $keyValues as $k => $v )
			self::$viewEngine->assign( $k, $v );
		
	}
	
	/**
	 * @desc  渲染模板
	 * @param unknown $tplPath
	 */
	static function render( $tplRelativePath ) {
		
		// 初始化视图引擎实例
		self::initViewEngine();
		
		// 默认视图文件路径为：app目录下views目录
		// app目录取决于全局常量ICHI_APP_PATH的定义
		if( !isset(self::$viewPath) )
			self::$viewPath = ICHI_VIEWS_PATH;
		
		// 调用引擎实例的render方法
		self::$viewEngine->render( self::$viewPath . $tplRelativePath );
		
	}
	
	/**
	 * @desc   初始化模板引擎
	 * @throws IchiStatusException
	 */
	private static function initViewEngine() {
		
		if( !isset(self::$viewEngine) ) {
			
			// 引擎名
			$engineName = self::$viewEngineName;
			
			// 文件路径
			$fileRelativePath = '/viewengine/' . $engineName;
			
			// 载入驱动文件并创建实例
			self::$viewEngine = Driver::load( $fileRelativePath, true );
			
		}
		
	}
}