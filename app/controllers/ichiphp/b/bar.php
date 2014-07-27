<?php

namespace app\controllers\ichiphp\b;
use \Response;

class bar {
	
	function _default() {
		Response::assign('aaa', 'abc');
		Response::render( 'demo.html' );
	}
	
	function _numeric( $n ) {
		Response::assign('aaa', $n);
		Response::render( 'demo.html' );
	}
	
}

?>