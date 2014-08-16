<?php

namespace extend\drivers\viewengine;

/**
 * @desc   简单的默认视图引擎
 * @author xiaozheen
 *
 */
class IchiViewEngine implements \IViewEngine {
	
	// 视图变量
	private $vars = array();
	
	
	/**
	 * (non-PHPdoc)
	 * @see IViewEngine::assign()
	 */
	function assign( $key, $value ) {
		$this->vars[ $key ] = $value;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IViewEngine::render()
	 */
	function render( $tplPath ) {
		
		foreach ( $this->vars as $k => $v )
			$$k = $v;

        if( !file_exists( $tplPath ) )
            throw new \IchiStatusException( 404, 'View file: `' . $tplPath . '` NOT FOUND!' );

        else
		    include $tplPath;
		
	}
	
}

?>