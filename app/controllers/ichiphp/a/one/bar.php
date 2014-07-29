<?php

namespace controllers\ichiphp\a\one;
use \Response;

class bar {
	
	function _numeric( $n ) {
		Response::write( 'the number is:' . $n );
	}
	
	function play() {
		echo 'play';
	}
}