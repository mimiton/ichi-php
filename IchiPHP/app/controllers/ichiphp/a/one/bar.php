<?php

namespace app\controllers\ichiphp\a\one;

class bar {
	
	function _numeric( $req, $res, $n ) {
		$res->write( 'the number is:' . $n );
	}
	
	function play() {
		echo 'play';
	}
}