<?php
/**
 * @desc   驱动接口
 *         提供驱动类的抽象，所有驱动需实现此接口
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

/**
 * @desc   视图引擎接口
 *         所有视图引擎需实现此接口
 * @author xiaozheen
 *
 */
interface IViewEngine {
	
	/**
	 * @desc  设置变量键值对
	 * @param unknown $key
	 * @param unknown $value
	 */
	public function assign( $key, $value );
	
	/**
	 * @desc  渲染指定路径的模板
	 * @param unknown $tplPath
	 */
	public function render( $tplPath );
	
}
?>