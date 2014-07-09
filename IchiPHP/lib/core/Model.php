<?php
/**
 * @desc   模型调用类
 * @author xiaozheen
 *
 */
class Model {
	
	/**
	 * @desc  载入数据模型
	 * @param unknown $modelPath
	 * @return unknown
	 */
	static function load( $modelPath, $autoInstantiate = false ) {
		
		// 文件路径
		$filePath = ICHI_MODELS_PATH . $modelPath . '.php';
		
		// 载入文件
		require_once $filePath;
		
		// 默认不创建实例
		if( !$autoInstantiate )
			return;

		// 类的命名空间
		$nameSpace = ICHI_MODELS_NS . str_replace( '/', '\\', $modelPath );
		
		// 用命名空间创建实例
		if( class_exists($nameSpace) )
			$model = new $nameSpace();
		
		// 降级尝试使用文件名创建实例
		else {
			$matches = explode( '\\', $modelPath );
			$className = $matches[ count($matches)-1 ];
			$model = new $className();
		}
		
		return $model;
		
	}
}