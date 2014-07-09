<?php

namespace app\models\user;

class rank {
	
	function getRank( $uid ) {
		
		$mysql = \Driver::useDefault('mysql');
		
		return $mysql->getData('SELECT * FROM tb_usr');
		
	}
	
}

?>