<?php

namespace app\models\user;

class rank {
	
	function getRank( $uid ) {
		
		$mysql = \Driver::useDefault('mysql');
		
		$sql = \SQL::table('tb_usr')->select('*')->where('id', '>=', 1)->get();
		
		return $mysql->getData($sql);
		
	}
	
}

?>