<?php
/**
 * @desc   路由器
 * @author xiaozheen
 *
 */
class Router {

    // 通配符常量
    const GENERAL_WILDCARD = '_default';
    const NUMERIC_WILDCARD = '_numeric';

    // 路由的目标应用
    private static $appName;
    // 特殊路由规则数组
    private static $specialRoute;

    /**
     * @desc  设置路由的目标应用名
     * @param $name
     */
    static function setAppName( $name ) {
        self::$appName = $name;
    }

    /**
     * @desc  设置特殊重定向路由规则
     * @param $route
     */
    static function setSpecialRoute( $route ) {
        self::$specialRoute = $route;
    }

    /**
     * @desc  调用路由方法，并捕捉异常
     * @param unknown $uri             URI
     *        boolean $handleException 是否处理异常
     */
    static function to( $uri, $handleException = true ) {

        // 优先匹配ALL方法里的路由规则，处理所有请求方法
        $specialUri = self::matchSpecialRoute( 'ALL', $uri );

        // 次先匹配具体请求方法里的路由规则
        if( !$specialUri )
            $specialUri = self::matchSpecialRoute( Request::getMethod(), $uri );

        // 有匹配到的特殊路由
        if( $specialUri )
            // 使用新路由
            $uri = $specialUri;

        try {

            // 未成功调用控制器，抛出404异常
            if( !self::routeToController( $uri ) )
                throw new IchiStatusException( 404, 'Controller for uri:`'.$uri.'` in App:`'.self::$appName.'` not found!' );

        } catch( Exception $e ) {

            // 处理异常
            if( $handleException )
                self::handleException( $e );

            return false;
        }

        return true;
    }

    /**
     * @desc  匹配特殊路由，成功则返回路由映射的控制器对应真实uri
     * @param $reqMethod  请求方法{ALL|GET|POST|PUT|DELETE}
     * @param $uri        需要匹配的URI
     * @return bool|mixed
     */
    private static function matchSpecialRoute( $reqMethod, $uri ) {

        // 根据请求method获取对应的特殊路由规则
        $specialRoute = self::$specialRoute[ $reqMethod ];

        if( !is_array( $specialRoute ) )
            return false;

        // 遍历规则
        foreach ( $specialRoute as $pattern => $newPattern ) {

            $pattern = str_replace( '/', '\/', $pattern );

            $newUri  = preg_replace( '/^' .$pattern. '$/', $newPattern, $uri );

            // 例如：
            //      `/foo(\d+)/(\w+) => /foo/$1/$2` 的规则
            //      可把 `/foo1234/bar` 路由至 `/foo/1234/bar`
            return $newUri;

        }

        return false;
    }

    /**
     * @desc  根据uri路由到匹配的可用的控制器上
     * @param unknown $uri
     * @return boolean
     */
    private static function routeToController( $uri ) {

        $GENERAL_WILDCARD = self::GENERAL_WILDCARD;
        $NUMERIC_WILDCARD = self::NUMERIC_WILDCARD;

        // 访问的是根目录uri（形如`/`,`///`这样）
        // 路由至根目录特殊控制器
        if( preg_match( '/^\/+$/', $uri ) )
            $uri = ICHI_URI_APP_HOMEPAGE;


        // 控制器目录
        $path = ICHI_CONTROLLERS_PATH . '/' . self::$appName;

        // 控制器命名空间
        $nameSpace = ICHI_CONTROLLERS_NS . '\\' . self::$appName;

        // 用 `/` 拆解uri
        $matches = explode( '/', $uri );


        // 遍历uri的每一层
        // 在其对应的控制器目录每一层里寻找控制器
        for( $i = 0; isset($matches[$i]); $i++ ) {

            $name = $matches[$i];

            // 当前层名字为空，跳过
            if( strlen($name) < 1 ) continue;

            // 寻找对应控制器php文件
            $fileFound = self::findControllerFile( $path, $name );

            if( is_string($fileFound) )
                $name = $fileFound;
            else {
                $dirFound = self::findControllerDir( $path, $name );
                if( is_string($dirFound) )
                    $name = $dirFound;
            }

            // 进入下一层目录
            $path      .= '/'  . $name;
            // 进入下一层命名空间
            $nameSpace .= '\\' . $name;

            // 有可用控制器php文件
            if( is_string($fileFound) ) {

                // 载入文件并创建控制器
                require_once $path . '.php';

                // 尝试用命名空间创建实例
                if( class_exists($nameSpace) )
                    $controller = new $nameSpace();

                // 直接使用当前层的目录名创建
                // （兼容未写命名空间的控制器php文件，建议都写上命名空间）
                else
                    $controller = new $name();

                break;

            }

        }

        // URI遍历结束

        // 每一个URI层名字作调用控制器的参数使用
        $args = array_slice( $matches, 1 );

        // 向后一层URI的名字，作调用的目标方法名
        $fnName = $matches[ $i+1 ];

        // 后一层URI不存在，调用默认方法
        if( strlen($fnName) < 1 )
            $fnName = $GENERAL_WILDCARD;

        // 调用控制器方法
        if( self::callControllerFn( $controller, $fnName, $args ) )
            return true;
        // 数字类型 通配方法
        else if( is_numeric($fnName) && self::callControllerFn( $controller, $NUMERIC_WILDCARD, $fnName, $args ) )
            return true;
        // 默认 通配方法
        else if( self::callControllerFn( $controller, $GENERAL_WILDCARD, $args ) )
            return true;
        // 未找到可用方法
        else
            return false;

    }


    /**
     * @desc  寻找指定目录下的指定名称所对应的控制器目录
     *        向下兼容_numeric、_default模式
     * @param $path
     * @param $name
     * @return string
     */
    private static function findControllerDir( $path, $name ) {

        $GENERAL_WILDCARD = self::GENERAL_WILDCARD;
        $NUMERIC_WILDCARD = self::NUMERIC_WILDCARD;

        // 同名目录存在
        if( is_dir( $path . '/' . $name ) )
            $dirFound = $name;

        // 处理当前URI层为数字的情况
        else if( is_numeric($name) && is_dir( $path . '/'. $NUMERIC_WILDCARD ) )
            $dirFound = $NUMERIC_WILDCARD;

        // 处理当前URI层为任意字符的其他情况
        else if( is_dir( $path . '/'. $GENERAL_WILDCARD ) )
            $dirFound = $GENERAL_WILDCARD;

        return $dirFound;

    }

    /**
     * @desc  寻找指定目录下的指定名称所对应的控制器php文件
     *        向下兼容_numeric、_default模式
     * @param unknown $path
     * @param unknown $name
     * @return Ambigous <string, unknown>
     */
    private static function findControllerFile( $path, $name ) {

        $GENERAL_WILDCARD = self::GENERAL_WILDCARD;
        $NUMERIC_WILDCARD = self::NUMERIC_WILDCARD;

        // 判断对应文件是否存在
        if( file_exists( $path . '/' . $name . '.php' ) )
            $fileFound = $name;

        // 有同名目录存在，跳过通配控制器
        //else if( is_dir( $path . '/' . $name ) )
        //    $fileFound = false;

        // 处理当前URI层为数字的情况
        else if( is_numeric($name) && file_exists( $path . '/'. $NUMERIC_WILDCARD .'.php' ) )
            $fileFound = $NUMERIC_WILDCARD;

        // 处理当前URI层为任意字符的其他情况
        else if( file_exists( $path . '/'. $GENERAL_WILDCARD .'.php' ) )
            $fileFound = $GENERAL_WILDCARD;

        return $fileFound;
    }

    /**
     * @desc  调用给定的类的指定方法
     * @param unknown $controller
     * @param unknown $fnName
     */
    private static function callControllerFn( $controller, $fnName, $args_1 = NULL, $args_2 = NULL ) {

        // 请求方法，GET|POST之类的
        $reqMethod = Request::getMethod();

        // 添加带有请求方法后缀的方法名
        $fnNameWithMethod = $fnName . '_' . $reqMethod;


        // 调用->functionName_{GET|POST|PUT|DELETE}();这样的方法
        if( method_exists( $controller, $fnNameWithMethod ) )
            $result = $controller->$fnNameWithMethod( $args_1, $args_2 );

        // 不存在则调用不带后缀的普通方法->functionName()
        else if( method_exists( $controller, $fnName ) )
            $result = $controller->$fnName( $args_1, $args_2 );

        // 没找到可用方法
        else
            return false;


        return $result === false? false : true;

    }

    /**
     * @desc  处理异常，路由错误页面对应控制器
     */
    private static function handleException( $e ) {

        // 清除已经输出的内容
        ob_clean();

        // 无可用错误码时作500处理
        if( !is_numeric($e->status) )
            $e->status = 500;

        $status = $e->status;

        // 响应错误信息
        Response::writeException( $e );

        // 路由至指定错误码对应的控制器
        // 应用层异常控制器保留URI形如：/_config/_exception/_404
        if( !self::routeToController( ICHI_URI_EXCEPTION .'/_'. $status ) ) {

            // 应用目录没有提供用于错误页面的控制器
            // 所以显示框架默认的错误页面
            self::showFrameworkErrPage($status);

        }

        exit();

    }

    /**
     * @desc  显示框架默认的错误页面
     * @param $status
     */
    private static function showFrameworkErrPage( $status ) {

        if( Request::getMethod() == 'GET' )
            Response::render( '_'.$status.'.html', ICHI_PHP_PATH.'/lib/assets/html/' );

    }

}

/**
 * @desc   带有状态码的异常
 * @author xiaozheen
 *
 */
class IchiStatusException extends Exception {

    var $status,$msg;

    function __construct( $status, $msg ) {

        $this->status = $status;

        $this->msg    = $msg;

    }

}