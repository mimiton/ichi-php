<?php

namespace controllers\ichiphp\a\one;

class _default {
	function _default( $args ) {
		\Response::assign('aaa', $args[0] .'/'. $args[1]);
		\Response::render('demo.html');
	}
	
	function _numeric( $n,$arg ) {
		print_r( $arg );
	}
}

?>