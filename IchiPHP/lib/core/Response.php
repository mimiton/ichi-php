<?php
/**
 * @desc   响应类
 * @author xiaozheen
 *
 */
class Response {

    private static $outputModeKey  = '_data_mode';
    // 默认视图引擎名称
	private static $viewEngineName = 'IchiViewEngine';

    // 视图文件目录
    // 默认视图文件路径为：app目录下views目录
    // app目录取决于全局常量ICHI_APP_PATH的定义
	private static $viewPath = ICHI_VIEWS_PATH;

    // 视图引擎实例
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
     * @desc  设置响应头
     * @param $key
     * @param $value
     */
    static function setHeader( $key, $value ) {
        header( $key .':'. $value );
    }

	/**
	 * @desc  输出数据
	 * @param unknown $data
	 */
	static function write( $data ) {

        if( is_string($data) )
		    echo $data;

        else {

            $mode = Request::getParam( self::$outputModeKey );
            if( $mode == 'xml' )
                self::writeXML($data);
            else if( $mode == 'print' )
                print_r($data);
            else if( $mode == 'dump' )
                var_dump($data);
            else
                self::writeJSON($data);

        }

	}

    /**
     * @desc  输出XML文档
     * @param $data
     * @return bool
     */
    static function writeXML( $data ) {

        if( !is_array($data) )
            $data = Array();

        self::setHeader( 'Content-Type', 'application/xml' );

        self::write( Util::array2XML( $data ) );

    }

    /**
     * @desc  输出JSON文档
     * @param $data
     * @return bool
     */
    static function writeJSON( $data ) {

        if( !is_array($data) )
            $data = NULL;

        self::setHeader( 'Content-Type', 'application/json');

        self::write( Util::array2JSON($data) );

    }
	
	
	/**
	 * @desc  设置模板渲染的数据
	 * @param String|Array $key
	 * @param String       $value
	 */
	static function assign( $key, $value = NULL ) {
		
		// 初始化视图引擎实例
		self::initViewEngine();
		
		// 设置视图变量键值对
        // $key可为字符串或数组
		if( !is_array( $key ) )
			$keyValues = array( $key => $value );

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

		// 调用引擎实例的render方法
		self::$viewEngine->render( self::$viewPath . '/' . $tplRelativePath );
		
	}
	
	/**
	 * @desc   初始化模板引擎
	 * @throws IchiStatusException
	 */
	private static function initViewEngine() {
		
		if( !isset(self::$viewEngine) ) {
			
			// 引擎名
			$engineName = self::$viewEngineName;
			
			// 文件抽象路径
			$fileAbstractPath = '/viewengine/' . $engineName;
			
			// 载入驱动文件并创建实例
			self::$viewEngine = Driver::load( $fileAbstractPath, true );
			
		}
		
	}
}