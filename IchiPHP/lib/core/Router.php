<?php
/**
 * @desc   路由器
 * @author xiaozheen
 *
 */
class Router {
	
	// 路由的目标应用
	private static $appName;
	// 特殊路由规则数组
	private static $specialRoute;
	
	/**
	 * @desc  配置参数
	 * @param unknown $props
	 */
	static function config( $props ) {
		
		// 当前路由的应用名
		self::$appName      = $props['appName'];
		// 特殊路由规则
		self::$specialRoute = $props['specialRoute'];
		
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
	 * @desc  匹配特殊路由，成功则返回路由映射的控制器对应uri
	 * @param unknown $uri
	 * @return mixed|boolean
	 */
	private function matchSpecialRoute( $reqMethod, $uri ) {
		
		// 根据请求method获取对应的特殊路由规则
		$specialRoute = self::$specialRoute[ $reqMethod ];
		
		if( !is_array( $specialRoute ) )
			return false;
		
		// 遍历规则
		foreach ( $specialRoute as $pattern => $newPattern ) {
			
			$pattern = str_replace( '/', '\/', $pattern );
			
			$newUri = preg_replace( '/^' .$pattern. '$/', $newPattern, $uri );
			
			// 例如：
			//      `/foo(\d+)/(\w+) => /foo/$1/$2` 的规则
			//      可把 `/foo1234/bar` 路由至 `/foo/1234/bar`
			if( strcmp( $newUri, $uri ) != 0 )
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
		
		// 控制器目录
		$path = ICHI_CONTROLLERS_PATH . '/' . self::$appName;
		
		// 控制器命名空间
		$nameSpace = ICHI_CONTROLLERS_NS . '\\' . self::$appName;
		
		// 访问的是根目录uri，路由至根目录特殊控制器
		if( $uri == '/' ) {
			self::routeToController( '/_default/_root' );
			return true;
		}
		
		// 用 `/` 拆解uri
		$matches = explode( '/', $uri );
		

		// 遍历uri的每一层
		// 在其对应的控制器目录每一层里寻找控制器
		foreach ( $matches as $i => $name ) {
			
			// 当前层名字为空，跳过
			if( empty($name) ) continue;
			
			// 进入下一层目录
			$path      .= '/'  . $name;
			// 进入下一层命名空间
			$nameSpace .= '\\' . $name;
			
			// 控制器已经找到
			if( isset($controller) ) {

				// 剩下部分的uri做调用控制器的参数使用
				$args = array_slice( $matches, $i );
				
				// 调用控制器方法
				return Router::callControllerFn( $controller, $name, $args );
				
			}
			
			// 判断对应文件是否存在
			else if( file_exists( $path . '.php' ) ) {
				// 包含文件并创建控制器
				require_once $path . '.php';
				
				// 尝试用命名空间创建实例
				if( class_exists($nameSpace) )
					$controller = new $nameSpace();
				// 直接使用当前层的目录名创建
				// （兼容未写命名空间的控制器php文件，不建议不写命名空间）
				else
					$controller = new $name();
			}
			
		}
		
		// uri没有下一层了
		// 调用控制器的_default方法
		return Router::callControllerFn( $controller, '_default' );
		
	}
	
	/**
	 * @desc  调用给定的类的指定方法
	 * @param unknown $controller
	 * @param unknown $fnName
	 */
	private static function callControllerFn( $controller, $fnName, $args = NULL ) {
		
		if( !isset($controller) ) return false;
		
		// 添加带有请求方法后缀的方法名
		$fnNameWithMethod = $fnName . '_' . Request::getMethod();
		
		// 调用->functionName_GET()
		//    ->functionName_POST()
		//    ->functionName_PUT()
		//    ->functionName_DELETE()
		// 这样的方法，不存在则正常调用不带后缀的普通方法->functionName()
		if( method_exists( $controller, $fnNameWithMethod ) )
			$controller->$fnNameWithMethod( $args );
		
		// 方法存在
		else if( method_exists( $controller, $fnName ) )
			$controller->$fnName( $args );
		
		// 数字类型方法
		else if( is_numeric($fnName) && method_exists( $controller, '_numeric' ) )
			$controller->_numeric( $fnName, $args );
		
		// 默认方法
		else if( method_exists( $controller, '_default' ) )
			$controller->_default( $args );
		else
			return false;
		
		return true;
	}
	
	/**
	 * @desc  处理异常，路由错误页面对应控制器
	 */
	private static function handleException( $e ) {
		
		// 清除已经输出的内容
		ob_clean();
		
		// 获取错误码
		$status = $e->status;
		
		// 无可用错误码时作404处理
		if( !is_numeric($status) )
			$status = 404;
		
		// 路由至指定错误码对应的控制器
		self::routeToController( '/_default/_' . $status );
		
		// 输出响应头状态码
		header( 'HTTP/1.1 ' . $status );
		
		// 输出错误信息到header
		if( !empty($e->msg) )
			header( 'Ichi-Msg:' . $e->msg );
		
		exit();
		
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
?>