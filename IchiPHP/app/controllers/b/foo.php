<?php
class foo {
	
	function _default( $req, $res, $args ) {
		$res->write('this is default function');
		print_r($args);
	}
	
	function _numeric( $req, $res, $n, $args ) {
		$res->write( 'jumped ' . $n . ' meters' );
		print_r($args);
	}
	
	function jump( $req, $res, $args ) {
		echo 'jump';
		print_r($args);
	}
}