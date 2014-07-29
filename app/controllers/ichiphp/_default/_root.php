<?php

namespace controllers\ichiphp\_default;
use \Response;
/**
 * @desc   站点根目录控制器
 * @author xiaozheen
 *
 */
class _root {
	
	function _default() {
		Response::render( 'intro.html' );
	}
	
}

?>