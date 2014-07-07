<?php
/**
 * @desc   路由器
 * @author xiaozheen
 *
 */
class Router {
	
	/**
	 * @desc  根据uri路由到匹配的可用的控制器上
	 * @param unknown $uri
	 */
	static function to( $uri ) {
		
		// 控制器目录
		$path = getcwd() . '/'. ICHI_PHP_PATH .'/app/controllers/';
		
		// 用 `/` 拆解uri
		$matches = explode( '/', $uri );
		

		// 遍历uri的每一层
		// 在其对应的控制器目录每一层里寻找控制器
		foreach ( $matches as $i => $name ) {
			
			$path .= $name;
			
			// 控制器已经找到
			// 剩下部分的uri做调用控制器的参数使用
			if( isset($controller) ) {

				// 调用控制器方法
				Router::callFn( $controller, $name, array_slice($matches, $i) );
				
				return;
			}
			
			// 判断对应文件是否存在
			else if( file_exists( $path . '.php' ) ) {
				// 包含文件并创建控制器
				require_once $path . '.php';
				$controller = new $name();
			}
			
			$path .= '/';
			
		}
		
		// uri没有下一层了
		// 调用控制器的_default方法
		Router::callFn( $controller, '_default' );
		
	}
	
	/**
	 * @desc  调用给定的类的指定方法
	 * @param unknown $controller
	 * @param unknown $fnName
	 */
	static function callFn( $controller, $fnName, $args = NULL ) {
		
		if( !isset($controller) ) return;
		
		// 方法存在
		if( method_exists( $controller, $fnName ) )
			$controller->$fnName( new Request(), new Response(), $args );
		
		// 数字类型方法
		else if( is_numeric($fnName) && method_exists( $controller, '_numeric' ) )
			$controller->_numeric( new Request(), new Response(), $fnName, $args );
		
		// 默认方法
		else if( method_exists( $controller, '_default' ) )
			$controller->_default( new Request(), new Response(), $args );
		
	}
}

?>