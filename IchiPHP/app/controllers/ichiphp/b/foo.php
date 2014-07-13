<?php

namespace app\controllers\ichiphp\b;
use \Model;
use \Router;
use \Response;
use app\models\user\rank;

class foo {

	function _default( $args ) {
		Response::write('this is default function');
		print_r($args);
		
		// debug
		Model::load('/user/rank');
		$userRankModel = new rank();
		
		echo '<br>';
		print_r( $userRankModel->getRank(1) );
	}
	
	function _numeric( $n, $args ) {
		Response::write( 'jumped ' . $n . ' meters' );
		print_r($args);
		Router::to('/a/one/foo/hi');
	}
	
	function jump( $args ) {
		echo 'jump';
		print_r($args);
		Router::to('/a/one/bar/play');
	}
	
}

?>