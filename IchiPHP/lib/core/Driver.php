<?php
/**
 * @desc   驱动调用类
 * @author xiaozheen
 *
 */
class Driver {
	
	static $defaultDrivers    = array();
	static $defaultDriverCfgs = array();
	
	/**
	 * @desc  配置预设驱动等
	 * @param unknown $props
	 */
	static function config( $props ) {
	
		// 储存预设驱动配置信息，以alias别名为索引
		foreach ( $props['defaultDrivers'] as $alias => $cfg ) {
			self::$defaultDriverCfgs[ $alias ] = $cfg;
		}
	
	}
	
	/**
	 * @desc  使用预设驱动
	 * @param unknown $alias
	 */
	static function useDefault( $alias ) {
		
		// 驱动实例存在，直接返回
		if( isset( self::$defaultDrivers[ $alias ] ) )
			return self::$defaultDrivers[ $alias ];
		
		// 驱动配置信息存在，使用它进行初始化
		if( isset( self::$defaultDriverCfgs[ $alias ] ) ) {
			
			// 驱动配置信息
			$cfg = self::$defaultDriverCfgs[ $alias ];
			
			// 载入驱动，获取创建好的实例
			self::$defaultDrivers[ $alias ] = self::load( $cfg['path'], true );
			
			// 初始化驱动实例
			self::$defaultDrivers[ $alias ]->init( $cfg );
			
			// 返回实例
			return self::$defaultDrivers[ $alias ];
			
		}
		
	}
	
	/**
	 * @desc   载入驱动
	 */
	static function load( $driverPath, $autoInstatiate = false ) {
		
		// 驱动文件路径
		$filePath  = ICHI_DRIVERS_PATH . $driverPath . '.php';
		
		// 文件不存在，抛出500异常，由Router类抓取
		if( !file_exists($filePath) )
			throw new IchiStatusException( 500, 'File of driver:`'.$driverPath.'` not found!' );
		
		// 载入文件
		require_once $filePath;

		// 非自动创建模式，返回
		if( !$autoInstatiate )
			return;
			
		// 驱动命名空间
		$nameSpace = ICHI_DRIVERS_NS . str_replace( '/', '\\', $driverPath );
		
		// 尝试使用命名空间创建实例
		if( class_exists($nameSpace) )
			$driver = new $nameSpace();
		// 降级，使用文件名创建实例
		// 从文filePath中获取文件名：\foo\bar\abc => new abc();
		else {
			$matches = explode( '\\', $driverPath );
			$className = $matches[ count($matches)-1 ];
			$driver = new $className();
		}
		
		return $driver;
		
	}

}

/**
 * @desc   驱动接口
 *         提供驱动类的抽象，所有驱动需使用此接口
 * @author xiaozheen
 *
 */
interface IDriver {
	/**
	 * @desc  初始化
	 * @param unknown $cfg
	 */
	public function init( $cfg );
}