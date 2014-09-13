<?php
/**
 * Created by PhpStorm.
 * User: xiaozheen
 * Date: 14-9-13
 * Time: 下午2:23
 */

namespace extend\drivers\database;

/**
 * @desc    mysqli数据库驱动
 * @package extend\drivers\database
 */
class mysqli implements \IDriver {

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

        $this->conn = mysqli_connect( $host, $user, $pwd, $db );

        if( $this->conn )
            mysqli_query( $this->conn, 'set names '.$charset );

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
        $numRows = mysqli_num_rows($result);

        // 存在结果
        if( $numRows > 0 ) {

            $data = array();

            // 取出结果，储存数组
            for( $i=0; $i<$numRows; $i++ )
                $data[$i]= @mysqli_fetch_array( $result , MYSQL_ASSOC );

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
            return mysqli_insert_id( $this->conn );
        else
            return false;

    }

    /**
     * @desc  使用sql直接查询
     * @param unknown $sql
     * @return resource
     */
    function query( $sql ) {

        $result = mysqli_query( $this->conn, $sql );

        if( !$result ) {
            $msg = 'Mysqli Error:`'.mysqli_error($this->conn).'`. SQL:`'.$sql.'`';
            throw new \IchiStatusException( 500, $msg );
        }

        return $result;

    }

    function error() {
        return mysqli_error($this->conn);
    }

}

?>