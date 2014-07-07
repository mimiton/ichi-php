<?php
/**
 * @desc   App类
 */
class App {
	
	// 路由器
	private $router;
	
	/**
	 * @desc  启动App
	 */
	public function run() {
		
		$this->route();
		
	}
	
	/**
	 * @desc  进行路由
	 */
	private function route() {

		$reqUri = strtok( $_SERVER['REQUEST_URI'], '?' );
		
		Router::to( $reqUri );
		
	}


}