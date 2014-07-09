<?php

namespace extend\drivers\database;

/**
 * @desc   mysql数据库驱动
 * @author xiaozheen
 *
 */
class mysql implements \IDriver {
	
	/**
	 * (non-PHPdoc)
	 * @see IDriver::init()
	 */
	function init( $cfg ) {
		
		$host    = $cfg['host'];
		$user    = $cfg['user'];
		$pwd     = $cfg['password'];
		$db      = $cfg['database'];
		$charset = $cfg['charset'];
		
		if( !$charset ) $charset = 'utf8';
		
		$this->conn = mysql_connect( $host, $user, $pwd );
		
		if( $this->conn ) {
			mysql_select_db( $db );
			mysql_query( 'set names ' . $charset );
		}
	}
	
	/**
	 * @desc  使用sql查询获取数组形式数据
	 * @param unknown $sql
	 * @return multitype:NULL |NULL
	 */
	function getData( $sql ) {
		$result = $this->query($sql);
		
		if( $result && mysql_num_rows($result)>0 ) {
			
			$data = array();
			
			for( $i=0; $i<mysql_num_rows($result); $i++ )
				$data[$i]= @mysql_fetch_array( $result , MYSQL_ASSOC );
			
			return $data;
		}
		else
			return NULL;
	}
	
	
	/**
	 * @desc  使用sql直接查询
	 * @param unknown $sql
	 * @return resource
	 */
	function query( $sql ) {
		return mysql_query( $sql );
	}
	
}

?>