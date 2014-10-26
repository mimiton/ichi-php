<?php

namespace extend\drivers\cache;

/**
 * @desc   Memcache连接读写驱动
 * @author xiaozheen
 *
 */
class memcache implements \IDriver {
	
	var $cache,
        $isFileCache = false;
	
	/**
	 * (non-PHPdoc)
	 * @see IDriver::init()
	 */
	function init( $cfg ) {

        if( class_exists('\Memcache') )
            $this->cache = new \Memcache();

        // Memcache扩展不可用时降级使用文件cache
        else {

            $this->cache = \Driver::load( '/cache/file_cache', true );

            // 标记使用文件缓存
            $this->isFileCache = true;

        }

		$host   = $cfg['host'];
		$port   = $cfg['port'];

        // 转换连接参数为数组形式
		if( !is_array($host) )
			$servers = array( array('host' => $host, 'port' => $port) );
		else
			$servers = $host;

        // 遍历连接参数，连接多个服务端
		foreach ( $servers as $item )
			$this->cache->addServer( $item['host'], $item['port'] );

	}
	
	/**
	 * @desc  设置缓存值
	 * @param String        $key
	 * @param String|Number $val
     * @param Number        $expire
     * @param Number        $compressMode
     */
	function set( $key, $val, $expire = NULL, $compressMode = 0 ) {

        // 当前使用文件缓存，没有compressMode参数，把expire传至第三个参数
        if( $this->isFileCache )
            $compressMode = $expire;

		return $this->cache->set( $key, $val, $compressMode, $expire );
	}

    /**
     * @desc  添加缓存值（不覆盖）
     * @param String        $key
     * @param String|Number $val
     * @param Number        $expire
     * @param Number        $compressMode
     */
    function add( $key, $val, $expire = NULL, $compressMode = 0 ) {

        // 当前使用文件缓存，没有compressMode参数，把expire传至第三个参数
        if( $this->isFileCache )
            $compressMode = $expire;

        return $this->cache->add( $key, $val, $compressMode, $expire );
    }

	/**
	 * @desc  获取缓存值
	 * @param String $key
	 */
	function get( $key ) {
		return $this->cache->get( $key );
	}

    /**
     * @desc  删除一个缓存值
     * @param $key
     * @param int $timeout
     */
    function delete( $key, $timeout = 0 ) {

        $this->cache->delete( $key, $timeout );

    }
	
}

?>