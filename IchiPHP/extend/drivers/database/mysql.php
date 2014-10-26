<?php

namespace extend\drivers\database;
use \IDriver;

/**
 * @desc   mysql数据库驱动
 * @author xiaozheen
 *
 */
class mysql implements IDriver {
	
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

        if( !$result ) {
            $msg = 'mysql error:`'.mysql_error().'`. sql str:`'.$sql.'`';
            throw new \IchiStatusException( 500, $msg );
        }

		// 行数
		$numRows = mysql_num_rows($result);

		// 存在结果
		if( $numRows > 0 ) {
			
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
     * @desc   快速插入数据
     * @param  String $table
     * @param  Array  $data
     * @return bool|int
     */
    function insert( $table, $data = NULL ) {

        // $data参数不是数组，把$table视作查询语句
        if( !is_array($data) )
            $sql = $table;

        // 根据数组拼装查询语句
        else {

            $fields = '';
            $values = '';

            foreach( $data as $k => $v ) {
                $fields .= $k.',';
                $values .= '"'.$v.'",';
            }
            $fields = substr($fields,0,-1);
            $values = substr($values,0,-1);

            $sql = 'INSERT INTO '.$table.' ('.$fields.') VALUES ('.$values.')';

        }

        if( $this->query($sql) )
            return mysql_insert_id();
        else
            return false;

    }
	
	/**
	 * @desc  使用sql直接查询
	 * @param unknown $sql
	 * @return resource
	 */
	function query( $sql ) {
		return @mysql_query( $sql );
	}
	
}

?>