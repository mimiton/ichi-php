<?php
/**
 * Created by PhpStorm.
 * User: xiaozheen
 * Date: 14-8-10
 * Time: 上午10:38
 */

namespace extend\drivers\cache;


/**
 * @desc  文件缓存驱动
 */
class file_cache implements \IDriver {

    var $directory;

    /**
     * @desc  初始化方法
     * @param Array $cfg
     */
    function init( $cfg ) {

        $this->directory = ICHI_CWD . '/.tmp/cache';

        // 创建目录
        if( !file_exists( $this->directory ) )
            mkdir( $this->directory, 0777, true );

    }


    /**
     * @desc  由于memcache扩展不可用时会降级使用file_cache
     *        所以提供此方法以兼容memcache驱动的使用
     * @param $host
     * @param $port
     */
    function addServer( $host, $port ) {

    }

    function get( $key ) {

        $path = $this->path( $key );

        if( !file_exists($path) )
            return NULL;

        return unserialize( file_get_contents( $path ) );

    }

    function set( $key, $value ) {

        $path = $this->path( $key );

        $this->createDirectory( $path );

        $bytesWrite = file_put_contents( $path, serialize($value) );

        return $bytesWrite > 0;

    }

    function add( $key, $value ) {

        $path = $this->path( $key );

        if( file_exists($path) )
            return false;

        return $this->set( $key, $value );

    }

    /**
     * @desc  根据key值转换哈希路径
     * @param $key
     * @return string
     */
    protected function path( $key ) {

        $parts = array_slice( str_split($hash = md5($key), 2), 0, 2 );

        return $this->directory.'/'.join('/', $parts).'/'.$hash;

    }

    /**
     * @desc  创建目录
     * @param $path
     * @return bool
     */
    protected function createDirectory( $path ) {

        if ( !is_dir( $directory = dirname($path) ) )
            return mkdir( $directory, 0777, true );

        return true;

    }
} 