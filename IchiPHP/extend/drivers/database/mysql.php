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
		
		if( !$charset )
            $charset = 'utf8';
		
		$this->conn = mysql_connect( $host, $user, $pwd );
		
		if( $this->conn ) {
			mysql_select_db( $db );
			mysql_query( 'set names ' . $charset );
		}
		else
			throw new \IchiStatusException( 500, 'Failed connecting to database server:`'.$host.'`' );
	}
	
	/**
	 * @desc  使用sql查询获取数组形式数据
	 * @param unknown $sql
	 * @return multitype:NULL |NULL
	 */
	function getData( $sql ) {
		
		// 获取查询结果集
		$result = $this->query($sql);
		
		// 行数
		$numRows = mysql_num_rows($result);
		
		// 存在结果
		if( $result && $numRows>0 ) {
			
			$data = array();
			
			// 取出结果，储存数组
			for( $i=0; $i<$numRows; $i++ )
				$data[$i]= @mysql_fetch_array( $result , MYSQL_ASSOC );
			
			// 返回数组
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