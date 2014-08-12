<?php

namespace extend\drivers\cache;

/**
 * @desc   Memcache连接读写驱动
 * @author xiaozheen
 *
 */
class memcache implements \IDriver {
	
	var $cache;
	
	/**
	 * (non-PHPdoc)
	 * @see IDriver::init()
	 */
	function init( $cfg ) {

        if( class_exists('\Memcache') )
            $this->cache = new \Memcache();

        // Memcache扩展不可用时降级使用文件cache
        else
            $this->cache = \Driver::load( '/cache/file_cache', true );

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
	 * @param unknown $key
	 * @param unknown $val
	 */
	function set( $key, $val ) {
		return $this->cache->set( $key, $val );
	}

    /**
     * @desc  添加缓存值（不覆盖）
     * @param $key
     * @param $val
     * @return mixed
     */
    function add( $key, $val ) {
        return $this->cache->add( $key, $val );
    }

	/**
	 * @desc  获取缓存值
	 * @param unknown $key
	 */
	function get( $key ) {
		return $this->cache->get( $key );
	}
	
}

?>