<?php
/**
 * @desc   路由器
 * @author xiaozheen
 *
 */
class Router {
	
	static $appName;
	
	/**
	 * @desc  配置参数
	 * @param unknown $props
	 */
	static function config( $props ) {
		
		// 当前路由的应用名
		self::$appName = $props['appName'];
		
	}
	
	/**
	 * @desc  调用路由方法，并捕捉异常
	 * @param unknown $uri             URI
	 *        boolean $handleException 是否处理异常
	 */
	static function to( $uri, $handleException = true ) {
		
		try {
			
			// 返回false，则抛出异常
			if( !self::routeToController( $uri ) )
				throw new Exception();
			
		} catch( Exception $e ) {
			
			// 处理异常
			if( $handleException )
				self::handleException();
			
			return false;
		}
		
		return true;
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
			self::to( self::$appName, '/_default/_root' );
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
	 * @desc  处理异常，路由至404页面对应控制器
	 */
	private static function handleException() {
		
		if( !self::to( '/_default/_404', false ) ) {
			header('HTTP/1.1 404 Not Found');
			exit();
		}
		
	}
}

?>