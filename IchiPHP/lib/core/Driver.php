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
	
		foreach ( $props['defaultDrivers'] as $alias => $cfg ) {
			self::$defaultDriverCfgs[ $alias ] = $cfg;
		}
	
	}
	
	/**
	 * @desc  使用预设驱动
	 * @param unknown $alias
	 */
	static function useDefault( $alias ) {
		
		if( isset( self::$defaultDrivers[ $alias ] ) )
			return self::$defaultDrivers[ $alias ];
		
		if( isset( self::$defaultDriverCfgs[ $alias ] ) ) {
			
			$cfg = self::$defaultDriverCfgs[ $alias ];
			self::load( $cfg['path'] );
			
			$nameSpace = ICHI_DRIVERS_NS . str_replace( '/', '\\', $cfg['path'] );
			
			self::$defaultDrivers[ $alias ] = new $nameSpace();
			self::$defaultDrivers[ $alias ]->init( $cfg );
			
			return self::$defaultDrivers[ $alias ];
			
		}
		
	}
	
	/**
	 * @desc   载入驱动
	 */
	static function load( $driverPath, $autoInstatiate = false ) {
		
		$filePath  = ICHI_DRIVERS_PATH . $driverPath . '.php';
		
		require_once $filePath;

		if( !$autoInstatiate )
			return;
			
		$nameSpace = ICHI_DRIVERS_NS . str_replace( '/', '\\', $driverPath );
		
		if( class_exists($nameSpace) )
			$driver = new $nameSpace();
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