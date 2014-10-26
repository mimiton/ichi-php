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

    /**
     * @desc   获取
     * @param  $key
     * @return mixed|null
     */
    function get( $key ) {

        $path = $this->path( $key );

        if( !file_exists($path) )
            return NULL;

        $str = file_get_contents( $path );

        $time = substr( $str, 0, 10 );

        // 已过期
        if( $time <= time() ) {

            $this->delete($key);

            return NULL;

        }

        return unserialize( substr($str,10) );

    }

    /**
     * @desc   设置
     * @param  $key
     * @param  $value
     * @return bool   true-成功 false-失败
     */
    function set( $key, $value, $expire = 999999999 ) {

        $path = $this->path( $key );

        $this->createDirectory( $path );

        $valueWithTimeStamp = $this->addExpireTimeStamp( $value, $expire );

        $bytesWrite = file_put_contents( $path, $valueWithTimeStamp );

        return $bytesWrite > 0;

    }

    /**
     * @desc   添加值（不覆盖）
     * @param  $key
     * @param  $value
     * @return bool   true-成功 false-失败
     */
    function add( $key, $value, $expire = NULL ) {

        if( $this->get($key) !== NULL )
            return false;

        return $this->set( $key, $value, $expire );

    }


    /**
     * @desc  删除一个缓存值
     * @param $key
     */
    function delete( $key, $timeout = NULL ) {

        // 有设置timeout时间，重置对应值的expire过期时间
        if( is_numeric($timeout) && $timeout>0 )
            $this->updateExpire( $key, $timeout );

        // 删除对应缓存文件
        else {

            $path = $this->path( $key );

            @unlink($path);

        }

    }

    /**
     * @desc  （如果值存在）更新过期时间戳
     * @param  $key
     * @param  $expire
     */
    private function updateExpire( $key, $expire ) {

        $value = $this->get($key);

        if( $value !== NULL )
            $this->set( $key, $value, $expire );

    }

    /**
     * @desc   根据key值转换哈希路径
     * @param  $key
     * @return string
     */
    protected function path( $key ) {

        $parts = array_slice( str_split($hash = md5($key), 2), 0, 2 );

        return $this->directory.'/'.join('/', $parts).'/'.$hash;

    }

    /**
     * @desc   创建目录
     * @param  $path
     * @return bool
     */
    protected function createDirectory( $path ) {

        if ( !is_dir( $directory = dirname($path) ) )
            return mkdir( $directory, 0777, true );

        return true;

    }

    /**
     * @desc  序列化储存对象，并添加过期时间戳至头部
     * @param $val
     * @param $expire
     * @return string
     */
    protected function addExpireTimeStamp( $val, $expire ) {

        $time = time() + $expire;

        return $time . serialize($val);

    }

} 