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
		
		$host   = $cfg['host'];
		$port   = $cfg['port'];
		
		if( class_exists('\Memcache') )
			$this->cache = new \Memcache();
		else
			throw new \IchiStatusException( 500, 'Memcache class is not available' );
		
		if( !is_array($host) )
			$servers = array( 'host' => $host, 'port' => $port );
		else
			$servers = $host;
		
		foreach ( $servers as $item ) {
			$this->cache->addServer( $item['host'], $item['port'] );
		}
	}
	
	/**
	 * @desc  设置缓存值
	 * @param unknown $key
	 * @param unknown $val
	 */
	function set( $key, $val ) {
		$this->cache->set( $key, $val );
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