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
	static function assign( $key, $value ) {
		self::initEngine();
		self::$viewEngine->assign( $key, $value );
	}
	
	/**
	 * @desc  渲染模板
	 * @param unknown $tplPath
	 */
	static function render( $tplRelativePath ) {
		self::initEngine();
		
		if( !isset(self::$viewPath) )
			self::$viewPath = ICHI_APP_PATH . '/views/';
		
		self::$viewEngine->render( self::$viewPath . $tplRelativePath );
	}
	
	/**
	 * @desc   初始化模板引擎
	 * @throws IchiStatusException
	 */
	private static function initEngine() {
		
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