<?php
/**
 * @desc   响应类
 * @author xiaozheen
 *
 */
class Response {

    static $statusDesc = array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Move temporarily',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        421 => 'Extension Required',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        449 => 'Retry With',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        510 => 'Not Extended',
        600 => 'Unparseable Response Headers'
    );

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
     * @desc  以表格输出数据
     * @param $data
     */
    static function writeGrid( $data ) {

        /*$tbody = '';

        foreach( $data as $item ) {

            if( !isset($thead) ) {
                $thead = '';
                foreach( $item as $key => $val )
                    $thead .= '<td>'.$key.'</td>';
            }

            $tbody .= '<tr>';

            foreach( $item as $val )
                $tbody .= '<td>'.$val.'</td>';

            $tbody .= '</tr>';

        }

        echo '<table border="1">'.$thead.$tbody.'</table>';*/

        self::assign( 'data', $data );

        self::render( 'tpl_data_grid.html', ICHI_PHP_PATH . '/lib/assets/templates/' );

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
    static function render( $tplRelativePath, $viewPath = NULL ) {
        
        // 初始化视图引擎实例
        self::initViewEngine();

        // 使用配置好的视图文件目录
        if( !$viewPath )
            $viewPath = self::$viewPath;

        // 调用引擎实例的render方法
        self::$viewEngine->render( $viewPath . '/' . $tplRelativePath );
        
    }

    /**
     * @desc  输出异常相关响应头
     * @param $e
     */
    static function writeException( $e ) {

        // 获取错误码
        $status = $e->status;

        // 无可用错误码时作500处理
        if( !is_numeric($status) )
            $status = 500;

        // 输出响应头状态码
        header( 'HTTP/1.1 ' . $status .' '. self::$statusDesc[$status] );

        // 输出错误信息到header
        if( !empty($e->msg) )
            self::setHeader( 'Ichi-Msg', $e->msg );

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